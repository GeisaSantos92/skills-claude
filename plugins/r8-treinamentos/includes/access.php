<?php
/**
 * Função "Agência R8" (agenciar8) e restrições de acesso.
 *
 * - A função agenciar8 tem tudo que o administrador tem e é a única com
 *   `r8t_manage_trainings` (cadastrar treinamentos e mexer na aparência).
 * - Os demais usuários, inclusive administradores, só assistem: não podem
 *   promover ninguém a agenciar8, editar/excluir usuários agenciar8, ver ou
 *   desativar o plugin, nem ver/apagar os vídeos de treinamento na Mídia.
 *
 * @package R8_Treinamentos
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'R8T_ROLE', 'agenciar8' );
define( 'R8T_MANAGE_CAP', 'r8t_manage_trainings' );
define( 'R8T_MEDIA_META', '_r8t_training' );
define( 'R8T_AGENCY_DOMAIN', 'agenciar8.com.br' );

/**
 * O usuário tem a função agenciar8?
 *
 * Checa a função (e não uma capability) para poder ser chamada de dentro
 * dos filtros de permissão sem recursão.
 *
 * @param int|null $user_id Id do usuário; null = usuário atual.
 * @return bool
 */
function r8t_user_is_agency( $user_id = null ) {
	$user = null === $user_id ? wp_get_current_user() : get_userdata( (int) $user_id );

	return $user instanceof WP_User && in_array( R8T_ROLE, (array) $user->roles, true );
}

/**
 * Cria a função agenciar8 ou completa as capabilities dela.
 *
 * A função guarda uma cópia das capabilities do administrador: assim, mesmo
 * com o plugin desativado, quem é agenciar8 continua com acesso total.
 *
 * @return void
 */
function r8t_sync_role() {
	$admin = get_role( 'administrator' );
	$caps  = $admin ? $admin->capabilities : array( 'read' => true );

	$caps[ R8T_MANAGE_CAP ] = true;

	$role = get_role( R8T_ROLE );
	if ( ! $role ) {
		add_role( R8T_ROLE, 'Agência R8', $caps );
		return;
	}

	foreach ( $caps as $cap => $grant ) {
		if ( $grant && empty( $role->capabilities[ $cap ] ) ) {
			$role->add_cap( $cap );
		}
	}
}

/**
 * Instalação e atualização: função, marcação dos vídeos e dono da agência.
 *
 * @return void
 */
function r8t_install() {
	r8t_sync_role();
	r8t_sync_training_media( r8t_get_trainings() );

	$has_agency = get_users(
		array(
			'role'   => R8T_ROLE,
			'number' => 1,
			'fields' => 'ID',
		)
	);
	if ( ! $has_agency ) {
		// Administradores da agência (login agenciar8 ou e-mail @agenciar8.com.br) viram agenciar8 na hora.
		$agency_admins = get_users(
			array(
				'role'           => 'administrator',
				'search'         => '*@' . R8T_AGENCY_DOMAIN,
				'search_columns' => array( 'user_email' ),
			)
		);
		$agency_login  = get_user_by( 'login', R8T_ROLE );
		if ( $agency_login && in_array( 'administrator', (array) $agency_login->roles, true ) ) {
			$agency_admins[] = $agency_login;
		}
		foreach ( $agency_admins as $user ) {
			$user->set_role( R8T_ROLE );
		}

		if ( ! $agency_admins ) {
			update_option( 'r8t_needs_owner', 1, false );
		}
	}

	update_option( 'r8t_version', R8T_VERSION, false );
	r8t_maybe_claim_owner();
}

/**
 * Roda a instalação quando a versão gravada difere da do plugin
 * (sites que já tinham a 1.0 e só atualizaram os arquivos).
 *
 * @return void
 */
function r8t_maybe_upgrade() {
	if ( R8T_VERSION !== get_option( 'r8t_version' ) ) {
		r8t_install();
		return;
	}

	r8t_maybe_claim_owner();
}
add_action( 'admin_init', 'r8t_maybe_upgrade' );

/**
 * Enquanto não houver nenhum agenciar8 (nem admin com e-mail da agência), o
 * primeiro administrador que abrir o painel depois da ativação/atualização vira agenciar8.
 *
 * @return void
 */
function r8t_maybe_claim_owner() {
	if ( ! get_option( 'r8t_needs_owner' ) || ! current_user_can( 'activate_plugins' ) ) {
		return;
	}

	wp_get_current_user()->set_role( R8T_ROLE );
	delete_option( 'r8t_needs_owner' );
}

/**
 * Dá ao agenciar8 as capabilities que o administrador ganhar depois
 * (ex.: um plugin novo que adiciona capabilities só ao administrador).
 *
 * @param array   $allcaps Capabilities do usuário.
 * @param array   $caps    Capabilities pedidas.
 * @param array   $args    Argumentos da checagem.
 * @param WP_User $user    Usuário.
 * @return array
 */
function r8t_inherit_admin_caps( $allcaps, $caps, $args, $user ) {
	if ( ! in_array( R8T_ROLE, (array) $user->roles, true ) ) {
		return $allcaps;
	}

	$admin = get_role( 'administrator' );

	return $admin ? array_merge( array_filter( $admin->capabilities ), $allcaps ) : $allcaps;
}
add_filter( 'user_has_cap', 'r8t_inherit_admin_caps', 10, 4 );

/**
 * Bloqueia, para quem não é agenciar8: editar/excluir/promover usuários
 * agenciar8, editar/excluir vídeos de treinamento e desativar o plugin.
 *
 * @param string[] $caps    Capabilities primitivas exigidas.
 * @param string   $cap     Capability pedida.
 * @param int      $user_id Usuário que está agindo.
 * @param array    $args    Argumentos (id do usuário/post, arquivo do plugin).
 * @return string[]
 */
function r8t_protect_caps( $caps, $cap, $user_id, $args ) {
	if ( empty( $args[0] ) || r8t_user_is_agency( $user_id ) ) {
		return $caps;
	}

	switch ( $cap ) {
		case 'edit_user':
		case 'delete_user':
		case 'remove_user':
		case 'promote_user':
			if ( r8t_user_is_agency( (int) $args[0] ) ) {
				$caps[] = 'do_not_allow';
			}
			break;

		case 'edit_post':
		case 'delete_post':
			$post = get_post( $args[0] );
			if ( $post && 'attachment' === $post->post_type && get_post_meta( $post->ID, R8T_MEDIA_META, true ) ) {
				$caps[] = 'do_not_allow';
			}
			break;

		case 'deactivate_plugin':
			if ( plugin_basename( R8T_FILE ) === $args[0] ) {
				$caps[] = 'do_not_allow';
			}
			break;
	}

	return $caps;
}
add_filter( 'map_meta_cap', 'r8t_protect_caps', 10, 4 );

/**
 * Tira a função agenciar8 das opções de função (criar/editar usuário e
 * "Mudar função para…") de quem não é agenciar8.
 *
 * @param array $roles Funções editáveis.
 * @return array
 */
function r8t_editable_roles( $roles ) {
	if ( ! r8t_user_is_agency() ) {
		unset( $roles[ R8T_ROLE ] );
	}

	return $roles;
}
add_filter( 'editable_roles', 'r8t_editable_roles' );

/**
 * Esconde os usuários agenciar8 da tela Usuários para quem não é agenciar8.
 *
 * @param WP_User_Query $query Consulta de usuários.
 * @return void
 */
function r8t_hide_agency_users( $query ) {
	global $pagenow;

	if ( ! is_admin() || 'users.php' !== $pagenow || r8t_user_is_agency() ) {
		return;
	}

	$exclude   = (array) $query->get( 'role__not_in' );
	$exclude[] = R8T_ROLE;
	$query->set( 'role__not_in', $exclude );
}
add_action( 'pre_get_users', 'r8t_hide_agency_users' );

/**
 * Tira o filtro "Agência R8" da tela Usuários e desconta esses usuários do "Todos".
 *
 * @param array $views Links de filtro por função.
 * @return array
 */
function r8t_users_views( $views ) {
	if ( r8t_user_is_agency() ) {
		return $views;
	}

	unset( $views[ R8T_ROLE ] );

	$counts = count_users();
	$hidden = (int) ( $counts['avail_roles'][ R8T_ROLE ] ?? 0 );

	if ( $hidden && isset( $views['all'] ) ) {
		$visible      = number_format_i18n( max( 0, (int) $counts['total_users'] - $hidden ) );
		$views['all'] = preg_replace( '/\([^()]*\)(?=<\/span>)/', '(' . $visible . ')', $views['all'] );
	}

	return $views;
}
add_filter( 'views_users', 'r8t_users_views' );

/**
 * Esconde o plugin da lista de Plugins para quem não é agenciar8.
 *
 * @param array $plugins Plugins instalados.
 * @return array
 */
function r8t_hide_plugin( $plugins ) {
	if ( ! r8t_user_is_agency() ) {
		unset( $plugins[ plugin_basename( R8T_FILE ) ] );
	}

	return $plugins;
}
add_filter( 'all_plugins', 'r8t_hide_plugin' );

/**
 * Impede quem não é agenciar8 de abrir os arquivos do plugin no editor de plugins.
 *
 * @return void
 */
function r8t_guard_plugin_editor() {
	if ( r8t_user_is_agency() ) {
		return;
	}

	$own = dirname( plugin_basename( R8T_FILE ) ) . '/';
	// phpcs:disable WordPress.Security.NonceVerification.Recommended
	$plugin = isset( $_REQUEST['plugin'] ) ? wp_unslash( (string) $_REQUEST['plugin'] ) : '';
	$file   = isset( $_REQUEST['file'] ) ? wp_unslash( (string) $_REQUEST['file'] ) : '';
	// phpcs:enable

	if ( '' === $plugin && '' === $file ) {
		// Sem plugin escolhido o editor abre o primeiro da lista: pula o nosso.
		$all = array_keys( get_plugins() );
		if ( isset( $all[1] ) && plugin_basename( R8T_FILE ) === $all[0] ) {
			wp_safe_redirect( add_query_arg( 'plugin', rawurlencode( $all[1] ), self_admin_url( 'plugin-editor.php' ) ) );
			exit;
		}
		return;
	}

	if ( 0 === strpos( $plugin, $own ) || 0 === strpos( $file, $own ) ) {
		wp_die( esc_html__( 'Você não tem permissão para editar este plugin.', 'r8-treinamentos' ), 403 );
	}
}
add_action( 'load-plugin-editor.php', 'r8t_guard_plugin_editor' );

/**
 * Marca (post meta) os anexos da Mídia usados como vídeo de treinamento e
 * desmarca os que deixaram de ser usados.
 *
 * @param array $trainings Treinamentos já sanitizados.
 * @return void
 */
function r8t_sync_training_media( $trainings ) {
	$ids = array();
	foreach ( $trainings as $training ) {
		$id = '' !== $training['video'] ? attachment_url_to_postid( $training['video'] ) : 0;
		if ( $id ) {
			$ids[] = $id;
		}
	}

	$marked = get_posts(
		array(
			'post_type'      => 'attachment',
			'post_status'    => 'any',
			'posts_per_page' => -1,
			'fields'         => 'ids',
			'meta_key'       => R8T_MEDIA_META, // phpcs:ignore WordPress.DB.SlowDBQuery
		)
	);

	foreach ( array_diff( $marked, $ids ) as $id ) {
		delete_post_meta( $id, R8T_MEDIA_META );
	}
	foreach ( $ids as $id ) {
		update_post_meta( $id, R8T_MEDIA_META, 1 );
	}
}

/**
 * Condição de meta que exclui os vídeos de treinamento de uma consulta.
 *
 * @param mixed $meta_query meta_query atual.
 * @return array
 */
function r8t_exclude_training_media( $meta_query ) {
	$meta_query   = (array) $meta_query;
	$meta_query[] = array(
		'key'     => R8T_MEDIA_META,
		'compare' => 'NOT EXISTS',
	);

	return $meta_query;
}

/**
 * Esconde os vídeos de treinamento da Mídia em modo lista (upload.php).
 *
 * @param WP_Query $query Consulta.
 * @return void
 */
function r8t_hide_media_list( $query ) {
	global $pagenow;

	if ( ! is_admin() || 'upload.php' !== $pagenow || ! $query->is_main_query() || r8t_user_is_agency() ) {
		return;
	}

	$query->set( 'meta_query', r8t_exclude_training_media( $query->get( 'meta_query' ) ) );
}
add_action( 'pre_get_posts', 'r8t_hide_media_list' );

/**
 * Esconde os vídeos de treinamento da Mídia em grade e do seletor de mídia.
 *
 * @param array $query Argumentos da consulta.
 * @return array
 */
function r8t_hide_media_grid( $query ) {
	if ( ! r8t_user_is_agency() ) {
		$query['meta_query'] = r8t_exclude_training_media( $query['meta_query'] ?? array() );
	}

	return $query;
}
add_filter( 'ajax_query_attachments_args', 'r8t_hide_media_grid' );
