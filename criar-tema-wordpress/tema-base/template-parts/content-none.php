<?php
/**
 * Template Part: mensagem de "nenhum conteúdo encontrado".
 *
 * @package Started_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<section class="no-results">
	<h2 class="no-results__title"><?php esc_html_e( 'Nada encontrado', 'started-theme' ); ?></h2>

	<?php if ( is_search() ) : ?>
	<p><?php esc_html_e( 'Nenhum resultado para a sua busca. Tente outras palavras.', 'started-theme' ); ?></p>
	<?php else : ?>
	<p><?php esc_html_e( 'Ainda não há conteúdo publicado aqui.', 'started-theme' ); ?></p>
	<?php endif; ?>
</section>
