<?php
/**
 * Dados do plugin: treinamentos, configurações e permissões.
 *
 * Tudo fica em duas options (sem tabelas próprias):
 * - `r8t_trainings`: lista ordenada de treinamentos (id, title, description, video).
 * - `r8t_settings`:  aparência e textos da Central.
 *
 * @package R8_Treinamentos
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Capability para assistir aos treinamentos.
 *
 * @return string
 */
function r8t_view_capability() {
	return (string) apply_filters( 'r8t_view_capability', 'edit_posts' );
}

/**
 * Capability para cadastrar/editar treinamentos e configurações.
 * Só a função agenciar8 tem (ver access.php); administradores apenas assistem.
 *
 * @return string
 */
function r8t_manage_capability() {
	return (string) apply_filters( 'r8t_manage_capability', 'r8t_manage_trainings' );
}

/**
 * Configurações padrão. O tema pode ajustá-las pelo filtro `r8t_default_settings`.
 *
 * @return array
 */
function r8t_default_settings() {
	$defaults = array(
		'accent'       => '#2271b1',
		'page_title'   => __( 'Central de Treinamentos', 'r8-treinamentos' ),
		'subtitle'     => __( 'Aprenda a gerenciar o site com tutoriais em vídeo práticos.', 'r8-treinamentos' ),
		'widget_text'  => __( 'Vídeos curtos mostrando, passo a passo, como atualizar o conteúdo do site.', 'r8-treinamentos' ),
		'button_label' => '',
		'button_url'   => '',
	);

	return wp_parse_args( (array) apply_filters( 'r8t_default_settings', array() ), $defaults );
}

/**
 * Configurações salvas, completadas com os padrões.
 *
 * @return array
 */
function r8t_get_settings() {
	return wp_parse_args( (array) get_option( 'r8t_settings', array() ), r8t_default_settings() );
}

/**
 * Lista ordenada de treinamentos.
 *
 * Na primeira leitura (option ainda inexistente) grava o que vier do filtro
 * `r8t_seed_trainings` — é por ele que um tema migra treinamentos antigos.
 *
 * @return array
 */
function r8t_get_trainings() {
	$trainings = get_option( 'r8t_trainings', null );

	if ( null === $trainings ) {
		$trainings = r8t_sanitize_trainings( (array) apply_filters( 'r8t_seed_trainings', array() ) );
		update_option( 'r8t_trainings', $trainings, false );
	}

	return is_array( $trainings ) ? $trainings : array();
}

/**
 * Sanitiza a lista de treinamentos vinda do formulário (ou do seed).
 *
 * Linhas sem título são descartadas. Cada treinamento ganha um id estável,
 * que não muda quando o título é editado (mantém os links funcionando).
 *
 * @param array $rows Linhas cruas.
 * @return array
 */
function r8t_sanitize_trainings( $rows ) {
	$clean = array();
	$ids   = array();

	foreach ( (array) $rows as $row ) {
		if ( ! is_array( $row ) ) {
			continue;
		}

		$title = sanitize_text_field( $row['title'] ?? '' );
		if ( '' === $title ) {
			continue;
		}

		$id = sanitize_key( $row['id'] ?? '' );
		if ( '' === $id || isset( $ids[ $id ] ) ) {
			$id = 't' . strtolower( wp_generate_password( 8, false, false ) );
		}
		$ids[ $id ] = true;

		$clean[] = array(
			'id'          => $id,
			'title'       => $title,
			'description' => sanitize_textarea_field( $row['description'] ?? '' ),
			'video'       => esc_url_raw( trim( (string) ( $row['video'] ?? '' ) ) ),
		);
	}

	return $clean;
}

/**
 * Sanitiza as configurações vindas do formulário.
 *
 * @param array $raw Valores crus.
 * @return array
 */
function r8t_sanitize_settings( $raw ) {
	$defaults = r8t_default_settings();
	$raw      = (array) $raw;

	return array(
		'accent'       => sanitize_hex_color( $raw['accent'] ?? '' ) ?: $defaults['accent'],
		'page_title'   => sanitize_text_field( $raw['page_title'] ?? '' ) ?: $defaults['page_title'],
		'subtitle'     => sanitize_text_field( $raw['subtitle'] ?? '' ),
		'widget_text'  => sanitize_textarea_field( $raw['widget_text'] ?? '' ),
		'button_label' => sanitize_text_field( $raw['button_label'] ?? '' ),
		'button_url'   => sanitize_text_field( $raw['button_url'] ?? '' ),
	);
}

/**
 * Resolve a URL do botão de atalho. Caminhos relativos (ex.: `edit.php?post_type=page`)
 * apontam para o wp-admin, assim o valor vale em qualquer domínio.
 *
 * @param string $url Valor salvo.
 * @return string
 */
function r8t_resolve_url( $url ) {
	$url = trim( (string) $url );

	if ( '' === $url ) {
		return '';
	}

	if ( preg_match( '#^(https?:)?//#i', $url ) ) {
		return $url;
	}

	return admin_url( ltrim( $url, '/' ) );
}

/**
 * URL da página de treinamentos (opcionalmente já num módulo).
 *
 * @param string $module Id do treinamento.
 * @return string
 */
function r8t_page_url( $module = '' ) {
	$args = array( 'page' => 'r8-treinamentos' );

	if ( '' !== $module ) {
		$args['module'] = $module;
	}

	return add_query_arg( $args, admin_url( 'admin.php' ) );
}

/**
 * URL da tela de gerenciamento.
 *
 * @return string
 */
function r8t_manage_url() {
	return add_query_arg( array( 'page' => 'r8-treinamentos-gerenciar' ), admin_url( 'admin.php' ) );
}
