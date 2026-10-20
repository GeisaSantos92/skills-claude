<?php
/**
 * Telas do painel: "Treinamentos" (assistir) e "Gerenciar" (cadastrar).
 *
 * @package R8_Treinamentos
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registra o menu "Treinamentos" e o submenu "Gerenciar".
 *
 * @return void
 */
function r8t_register_pages() {
	$settings = r8t_get_settings();

	add_menu_page(
		$settings['page_title'],
		__( 'Treinamentos', 'r8-treinamentos' ),
		r8t_view_capability(),
		'r8-treinamentos',
		'r8t_render_view_page',
		'dashicons-welcome-learn-more',
		3
	);

	add_submenu_page(
		'r8-treinamentos',
		$settings['page_title'],
		__( 'Assistir', 'r8-treinamentos' ),
		r8t_view_capability(),
		'r8-treinamentos',
		'r8t_render_view_page'
	);

	add_submenu_page(
		'r8-treinamentos',
		__( 'Gerenciar treinamentos', 'r8-treinamentos' ),
		__( 'Gerenciar', 'r8-treinamentos' ),
		r8t_manage_capability(),
		'r8-treinamentos-gerenciar',
		'r8t_render_manage_page'
	);
}
add_action( 'admin_menu', 'r8t_register_pages' );

/**
 * Enfileira CSS/JS só nas telas do plugin e no Dashboard.
 *
 * @param string $hook_suffix Sufixo da tela atual.
 * @return void
 */
function r8t_admin_assets( $hook_suffix ) {
	$is_view      = 'toplevel_page_r8-treinamentos' === $hook_suffix;
	$is_manage    = 'treinamentos_page_r8-treinamentos-gerenciar' === $hook_suffix;
	$is_dashboard = 'index.php' === $hook_suffix;

	// O sufixo do submenu deriva do título traduzido do menu; confere também pelo ?page=.
	$page = isset( $_GET['page'] ) ? sanitize_key( wp_unslash( $_GET['page'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$is_manage = $is_manage || 'r8-treinamentos-gerenciar' === $page;
	$is_view   = $is_view || 'r8-treinamentos' === $page;

	if ( ! $is_view && ! $is_manage && ! $is_dashboard ) {
		return;
	}

	wp_enqueue_style( 'r8t-admin', R8T_URL . 'assets/css/admin.css', array(), R8T_VERSION );

	$accent = r8t_get_settings()['accent'];
	wp_add_inline_style( 'r8t-admin', sprintf( ':root{--r8t-accent:%s;}', sanitize_hex_color( $accent ) ?: '#2271b1' ) );

	if ( $is_manage ) {
		wp_enqueue_media();
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script(
			'r8t-manage',
			R8T_URL . 'assets/js/manage.js',
			array( 'jquery', 'jquery-ui-sortable', 'wp-color-picker' ),
			R8T_VERSION,
			true
		);
		wp_localize_script(
			'r8t-manage',
			'r8tManage',
			array(
				'mediaTitle'    => __( 'Selecionar vídeo de treinamento', 'r8-treinamentos' ),
				'mediaButton'   => __( 'Usar este vídeo', 'r8-treinamentos' ),
				'confirmRemove' => __( 'Remover este treinamento?', 'r8-treinamentos' ),
			)
		);
	}
}
add_action( 'admin_enqueue_scripts', 'r8t_admin_assets' );

/**
 * Salva treinamentos e configurações (admin-post.php).
 *
 * @return void
 */
function r8t_handle_save() {
	if ( ! current_user_can( r8t_manage_capability() ) ) {
		wp_die( esc_html__( 'Você não tem permissão para fazer isso.', 'r8-treinamentos' ), 403 );
	}

	check_admin_referer( 'r8t_save', 'r8t_nonce' );

	// phpcs:disable WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- sanitizado em r8t_sanitize_*.
	$trainings = isset( $_POST['r8t_trainings'] ) ? wp_unslash( (array) $_POST['r8t_trainings'] ) : array();
	$settings  = isset( $_POST['r8t_settings'] ) ? wp_unslash( (array) $_POST['r8t_settings'] ) : array();
	// phpcs:enable

	$trainings = r8t_sanitize_trainings( array_values( $trainings ) );

	update_option( 'r8t_trainings', $trainings, false );
	update_option( 'r8t_settings', r8t_sanitize_settings( $settings ), false );
	r8t_sync_training_media( $trainings );

	wp_safe_redirect( add_query_arg( 'updated', '1', r8t_manage_url() ) );
	exit;
}
add_action( 'admin_post_r8t_save', 'r8t_handle_save' );

/**
 * Tela "Treinamentos": player + lista de módulos.
 *
 * @return void
 */
function r8t_render_view_page() {
	if ( ! current_user_can( r8t_view_capability() ) ) {
		wp_die( esc_html__( 'Você não tem permissão para acessar esta página.', 'r8-treinamentos' ) );
	}

	$settings  = r8t_get_settings();
	$trainings = r8t_get_trainings();
	$can_edit  = current_user_can( r8t_manage_capability() );
	$button    = r8t_resolve_url( $settings['button_url'] );

	$active = $trainings ? $trainings[0] : null;
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$requested = isset( $_GET['module'] ) ? sanitize_key( wp_unslash( $_GET['module'] ) ) : '';
	foreach ( $trainings as $training ) {
		if ( $training['id'] === $requested ) {
			$active = $training;
			break;
		}
	}
	?>
	<div class="wrap r8t-wrap">
		<div class="r8t-header">
			<div>
				<h1 class="r8t-title"><?php echo esc_html( $settings['page_title'] ); ?></h1>
				<?php if ( '' !== $settings['subtitle'] ) : ?>
					<p class="r8t-subtitle"><?php echo esc_html( $settings['subtitle'] ); ?></p>
				<?php endif; ?>
			</div>
			<div class="r8t-header-actions">
				<?php if ( '' !== $button && '' !== $settings['button_label'] ) : ?>
					<a href="<?php echo esc_url( $button ); ?>" class="button r8t-btn"><?php echo esc_html( $settings['button_label'] ); ?></a>
				<?php endif; ?>
				<?php if ( $can_edit ) : ?>
					<a href="<?php echo esc_url( r8t_manage_url() ); ?>" class="button"><?php esc_html_e( 'Gerenciar treinamentos', 'r8-treinamentos' ); ?></a>
				<?php endif; ?>
			</div>
		</div>

		<?php if ( ! $active ) : ?>
			<div class="r8t-card r8t-empty">
				<span class="dashicons dashicons-video-alt3" aria-hidden="true"></span>
				<h2><?php esc_html_e( 'Nenhum treinamento cadastrado ainda.', 'r8-treinamentos' ); ?></h2>
				<?php if ( $can_edit ) : ?>
					<p><a href="<?php echo esc_url( r8t_manage_url() ); ?>" class="button r8t-btn"><?php esc_html_e( 'Cadastrar treinamentos', 'r8-treinamentos' ); ?></a></p>
				<?php else : ?>
					<p><?php esc_html_e( 'Os vídeos estarão disponíveis em breve.', 'r8-treinamentos' ); ?></p>
				<?php endif; ?>
			</div>
		<?php else : ?>
			<?php $player = r8t_video_html( $active['video'] ); ?>
			<div class="r8t-layout">
				<div class="r8t-card r8t-main">
					<div class="r8t-video">
						<?php if ( '' !== $player ) : ?>
							<?php echo $player; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escapado em r8t_video_html(). ?>
						<?php else : ?>
							<div class="r8t-video-placeholder">
								<span class="dashicons dashicons-video-alt3" aria-hidden="true"></span>
								<h3><?php echo esc_html( $active['title'] ); ?></h3>
								<?php if ( '' !== $active['video'] ) : ?>
									<p><a href="<?php echo esc_url( $active['video'] ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Abrir vídeo em nova aba', 'r8-treinamentos' ); ?></a></p>
								<?php elseif ( $can_edit ) : ?>
									<p><?php esc_html_e( 'Nenhum vídeo associado a este treinamento.', 'r8-treinamentos' ); ?></p>
									<a href="<?php echo esc_url( r8t_manage_url() ); ?>" class="button"><?php esc_html_e( 'Adicionar vídeo', 'r8-treinamentos' ); ?></a>
								<?php else : ?>
									<p><?php esc_html_e( 'O vídeo deste treinamento estará disponível em breve.', 'r8-treinamentos' ); ?></p>
								<?php endif; ?>
							</div>
						<?php endif; ?>
					</div>
					<div class="r8t-body">
						<h2><?php echo esc_html( $active['title'] ); ?></h2>
						<?php if ( '' !== $active['description'] ) : ?>
							<?php echo wp_kses_post( wpautop( esc_html( $active['description'] ) ) ); ?>
						<?php endif; ?>
					</div>
				</div>

				<nav class="r8t-card r8t-sidebar" aria-label="<?php esc_attr_e( 'Treinamentos', 'r8-treinamentos' ); ?>">
					<h2 class="r8t-sidebar-title"><?php esc_html_e( 'Treinamentos', 'r8-treinamentos' ); ?></h2>
					<ol class="r8t-modules">
						<?php foreach ( $trainings as $training ) : ?>
							<?php $is_active = $training['id'] === $active['id']; ?>
							<li>
								<a href="<?php echo esc_url( r8t_page_url( $training['id'] ) ); ?>" class="r8t-module<?php echo $is_active ? ' is-active' : ''; ?>"<?php echo $is_active ? ' aria-current="page"' : ''; ?>>
									<span class="r8t-module-title"><?php echo esc_html( $training['title'] ); ?></span>
									<?php if ( '' !== $training['video'] ) : ?>
										<span class="r8t-badge"><?php esc_html_e( 'Disponível', 'r8-treinamentos' ); ?></span>
									<?php else : ?>
										<span class="r8t-badge is-soon"><?php esc_html_e( 'Em breve', 'r8-treinamentos' ); ?></span>
									<?php endif; ?>
								</a>
							</li>
						<?php endforeach; ?>
					</ol>
				</nav>
			</div>
		<?php endif; ?>
	</div>
	<?php
}

/**
 * Uma linha (treinamento) do formulário de gerenciamento.
 *
 * @param string|int $index    Índice no array do formulário.
 * @param array      $training Dados do treinamento.
 * @return void
 */
function r8t_render_manage_row( $index, $training ) {
	$name = 'r8t_trainings[' . $index . ']';
	?>
	<li class="r8t-row">
		<span class="r8t-row-handle dashicons dashicons-menu" title="<?php esc_attr_e( 'Arraste para reordenar', 'r8-treinamentos' ); ?>" aria-hidden="true"></span>
		<input type="hidden" name="<?php echo esc_attr( $name ); ?>[id]" value="<?php echo esc_attr( $training['id'] ?? '' ); ?>">
		<div class="r8t-row-fields">
			<label>
				<span><?php esc_html_e( 'Título', 'r8-treinamentos' ); ?></span>
				<input type="text" class="widefat" name="<?php echo esc_attr( $name ); ?>[title]" value="<?php echo esc_attr( $training['title'] ?? '' ); ?>" required>
			</label>
			<label>
				<span><?php esc_html_e( 'Descrição', 'r8-treinamentos' ); ?></span>
				<textarea class="widefat" rows="3" name="<?php echo esc_attr( $name ); ?>[description]"><?php echo esc_textarea( $training['description'] ?? '' ); ?></textarea>
			</label>
			<label>
				<span><?php esc_html_e( 'Vídeo (arquivo da biblioteca, .mp4 ou link do YouTube/Vimeo)', 'r8-treinamentos' ); ?></span>
				<span class="r8t-video-input">
					<input type="url" class="widefat r8t-video-url" name="<?php echo esc_attr( $name ); ?>[video]" value="<?php echo esc_attr( $training['video'] ?? '' ); ?>" placeholder="https://">
					<button type="button" class="button r8t-media"><?php esc_html_e( 'Biblioteca', 'r8-treinamentos' ); ?></button>
				</span>
			</label>
		</div>
		<div class="r8t-row-actions">
			<button type="button" class="button-link r8t-move" data-dir="up" aria-label="<?php esc_attr_e( 'Mover para cima', 'r8-treinamentos' ); ?>"><span class="dashicons dashicons-arrow-up-alt2"></span></button>
			<button type="button" class="button-link r8t-move" data-dir="down" aria-label="<?php esc_attr_e( 'Mover para baixo', 'r8-treinamentos' ); ?>"><span class="dashicons dashicons-arrow-down-alt2"></span></button>
			<button type="button" class="button-link button-link-delete r8t-remove"><?php esc_html_e( 'Remover', 'r8-treinamentos' ); ?></button>
		</div>
	</li>
	<?php
}

/**
 * Tela "Gerenciar": cadastro dos treinamentos e configurações.
 *
 * @return void
 */
function r8t_render_manage_page() {
	if ( ! current_user_can( r8t_manage_capability() ) ) {
		wp_die( esc_html__( 'Você não tem permissão para acessar esta página.', 'r8-treinamentos' ) );
	}

	$trainings = r8t_get_trainings();
	$settings  = r8t_get_settings();
	?>
	<div class="wrap r8t-wrap r8t-manage">
		<h1 class="wp-heading-inline"><?php esc_html_e( 'Gerenciar treinamentos', 'r8-treinamentos' ); ?></h1>
		<a href="<?php echo esc_url( r8t_page_url() ); ?>" class="page-title-action"><?php esc_html_e( 'Ver Central', 'r8-treinamentos' ); ?></a>
		<hr class="wp-header-end">

		<?php // phpcs:ignore WordPress.Security.NonceVerification.Recommended ?>
		<?php if ( isset( $_GET['updated'] ) ) : ?>
			<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Treinamentos salvos.', 'r8-treinamentos' ); ?></p></div>
		<?php endif; ?>

		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="r8t_save">
			<?php wp_nonce_field( 'r8t_save', 'r8t_nonce' ); ?>

			<h2><?php esc_html_e( 'Treinamentos', 'r8-treinamentos' ); ?></h2>
			<p class="description"><?php esc_html_e( 'Arraste para mudar a ordem. Treinamentos sem título são ignorados ao salvar.', 'r8-treinamentos' ); ?></p>

			<ol class="r8t-rows" id="r8t-rows" data-next-index="<?php echo esc_attr( count( $trainings ) ); ?>">
				<?php
				foreach ( $trainings as $index => $training ) {
					r8t_render_manage_row( $index, $training );
				}
				?>
			</ol>

			<p><button type="button" class="button" id="r8t-add"><span class="dashicons dashicons-plus-alt2" aria-hidden="true"></span> <?php esc_html_e( 'Adicionar treinamento', 'r8-treinamentos' ); ?></button></p>

			<template id="r8t-row-template">
				<?php r8t_render_manage_row( '__INDEX__', array() ); ?>
			</template>

			<h2><?php esc_html_e( 'Aparência e textos', 'r8-treinamentos' ); ?></h2>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><label for="r8t-accent"><?php esc_html_e( 'Cor de destaque', 'r8-treinamentos' ); ?></label></th>
					<td><input type="text" id="r8t-accent" class="r8t-color" name="r8t_settings[accent]" value="<?php echo esc_attr( $settings['accent'] ); ?>" data-default-color="<?php echo esc_attr( r8t_default_settings()['accent'] ); ?>"></td>
				</tr>
				<tr>
					<th scope="row"><label for="r8t-page-title"><?php esc_html_e( 'Título da Central', 'r8-treinamentos' ); ?></label></th>
					<td><input type="text" id="r8t-page-title" class="regular-text" name="r8t_settings[page_title]" value="<?php echo esc_attr( $settings['page_title'] ); ?>"></td>
				</tr>
				<tr>
					<th scope="row"><label for="r8t-subtitle"><?php esc_html_e( 'Subtítulo', 'r8-treinamentos' ); ?></label></th>
					<td><input type="text" id="r8t-subtitle" class="large-text" name="r8t_settings[subtitle]" value="<?php echo esc_attr( $settings['subtitle'] ); ?>"></td>
				</tr>
				<tr>
					<th scope="row"><label for="r8t-widget-text"><?php esc_html_e( 'Texto do widget no Painel', 'r8-treinamentos' ); ?></label></th>
					<td><textarea id="r8t-widget-text" class="large-text" rows="2" name="r8t_settings[widget_text]"><?php echo esc_textarea( $settings['widget_text'] ); ?></textarea></td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Botão de atalho', 'r8-treinamentos' ); ?></th>
					<td>
						<fieldset class="r8t-inline-fields">
							<label><span><?php esc_html_e( 'Texto', 'r8-treinamentos' ); ?></span>
								<input type="text" class="regular-text" name="r8t_settings[button_label]" value="<?php echo esc_attr( $settings['button_label'] ); ?>" placeholder="<?php esc_attr_e( 'Ex.: Gerenciar páginas', 'r8-treinamentos' ); ?>"></label>
							<label><span><?php esc_html_e( 'Link', 'r8-treinamentos' ); ?></span>
								<input type="text" class="regular-text" name="r8t_settings[button_url]" value="<?php echo esc_attr( $settings['button_url'] ); ?>" placeholder="edit.php?post_type=page"></label>
						</fieldset>
						<p class="description"><?php esc_html_e( 'Opcional. Aparece no topo da Central. Caminhos sem "https://" apontam para o painel (wp-admin).', 'r8-treinamentos' ); ?></p>
					</td>
				</tr>
			</table>

			<?php submit_button( __( 'Salvar treinamentos', 'r8-treinamentos' ) ); ?>
		</form>
	</div>
	<?php
}
