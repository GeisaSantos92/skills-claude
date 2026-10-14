<?php
/**
 * Widget "Central de Treinamentos" no Dashboard.
 *
 * @package R8_Treinamentos
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registra o widget para quem pode assistir aos treinamentos.
 *
 * @return void
 */
function r8t_register_dashboard_widget() {
	if ( ! current_user_can( r8t_view_capability() ) ) {
		return;
	}

	wp_add_dashboard_widget(
		'r8t_dashboard',
		r8t_get_settings()['page_title'],
		'r8t_render_dashboard_widget'
	);
}
add_action( 'wp_dashboard_setup', 'r8t_register_dashboard_widget' );

/**
 * Conteúdo do widget: texto, até 5 atalhos para treinamentos e botão da Central.
 *
 * @return void
 */
function r8t_render_dashboard_widget() {
	$settings  = r8t_get_settings();
	$trainings = r8t_get_trainings();
	$total     = count( $trainings );
	?>
	<div class="r8t-dash">
		<?php if ( $total ) : ?>
			<span class="r8t-dash-badge">
				<?php
				/* translators: %d: quantidade de treinamentos. */
				echo esc_html( sprintf( _n( '%d treinamento disponível', '%d treinamentos disponíveis', $total, 'r8-treinamentos' ), $total ) );
				?>
			</span>
		<?php endif; ?>

		<?php if ( '' !== $settings['widget_text'] ) : ?>
			<p class="r8t-dash-desc"><?php echo esc_html( $settings['widget_text'] ); ?></p>
		<?php endif; ?>

		<?php if ( $total ) : ?>
			<ul class="r8t-dash-list">
				<?php foreach ( array_slice( $trainings, 0, 5 ) as $training ) : ?>
					<li><a href="<?php echo esc_url( r8t_page_url( $training['id'] ) ); ?>"><span class="dashicons dashicons-controls-play" aria-hidden="true"></span><?php echo esc_html( $training['title'] ); ?></a></li>
				<?php endforeach; ?>
			</ul>
			<a href="<?php echo esc_url( r8t_page_url() ); ?>" class="button button-primary r8t-btn"><?php esc_html_e( 'Acessar Treinamentos', 'r8-treinamentos' ); ?></a>
		<?php elseif ( current_user_can( r8t_manage_capability() ) ) : ?>
			<a href="<?php echo esc_url( r8t_manage_url() ); ?>" class="button r8t-btn"><?php esc_html_e( 'Cadastrar treinamentos', 'r8-treinamentos' ); ?></a>
		<?php else : ?>
			<p class="r8t-dash-desc"><?php esc_html_e( 'Os vídeos estarão disponíveis em breve.', 'r8-treinamentos' ); ?></p>
		<?php endif; ?>
	</div>
	<?php
}
