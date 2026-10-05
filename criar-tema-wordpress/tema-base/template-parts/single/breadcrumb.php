<?php
/**
 * Post interna — Breadcrumb: Início › Blog › Título do post.
 *
 * @package Started_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$starter_titulo = get_the_title();
$starter_blog   = get_post_type_archive_link( 'post' );

if ( ! $starter_blog ) {
	$starter_blog_page = get_option( 'page_for_posts' );
	$starter_blog      = $starter_blog_page ? get_permalink( $starter_blog_page ) : home_url( '/' );
}
?>

<nav class="post-breadcrumb" aria-label="<?php esc_attr_e( 'Localização', 'started-theme' ); ?>">
	<div class="post-breadcrumb__inner">

		<a
			class="post-breadcrumb__home"
			href="<?php echo esc_url( home_url( '/' ) ); ?>"
			aria-label="<?php esc_attr_e( 'Início', 'started-theme' ); ?>"
		>
			<?php echo starter_icone( 'casa', 20 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- starter_icone retorna SVG estático do tema. ?>
		</a>

		<?php echo starter_icone( 'chevron-direita', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- starter_icone retorna SVG estático do tema. ?>

		<a
			class="post-breadcrumb__link"
			href="<?php echo esc_url( $starter_blog ); ?>"
		>
			<?php esc_html_e( 'Blog', 'started-theme' ); ?>
		</a>

		<?php echo starter_icone( 'chevron-direita', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- starter_icone retorna SVG estático do tema. ?>

		<span class="post-breadcrumb__atual">
			<?php echo esc_html( $starter_titulo ); ?>
		</span>

	</div>
</nav>
