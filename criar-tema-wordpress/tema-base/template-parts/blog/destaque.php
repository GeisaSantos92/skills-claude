<?php
/**
 * Blog — Seção destaque: breadcrumb + post principal em evidência.
 *
 * @package Started_Theme
 *
 * @var array $args {
 *     @type WP_Post $post Post em destaque (o mais recente, só na 1ª página).
 * }
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$post_destaque = $args['post'] ?? null;

if ( ! $post_destaque ) {
	return;
}

$titulo         = get_the_title( $post_destaque );
$permalink      = get_permalink( $post_destaque );
$excerpt        = get_the_excerpt( $post_destaque );
$data           = get_the_date( 'j F Y', $post_destaque );
$categorias     = get_the_category( $post_destaque->ID );
$categoria      = ! empty( $categorias ) ? $categorias[0]->name : '';
$categoria_link = ! empty( $categorias ) ? get_category_link( $categorias[0]->term_id ) : '';
?>

<section class="blog-destaque">
	<div class="container">

		<!-- Breadcrumb -->
		<nav class="blog-destaque__breadcrumb" aria-label="<?php esc_attr_e( 'Localização', 'started-theme' ); ?>">
			<a
				class="blog-destaque__breadcrumb-home"
				href="<?php echo esc_url( home_url( '/' ) ); ?>"
				aria-label="<?php esc_attr_e( 'Início', 'started-theme' ); ?>"
			>
				<?php echo starter_icone( 'casa', 20 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- starter_icone retorna SVG estático do tema. ?>
			</a>
			<?php echo starter_icone( 'chevron-direita', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- starter_icone retorna SVG estático do tema. ?>
			<span class="blog-destaque__breadcrumb-atual">
				<?php esc_html_e( 'Blog', 'started-theme' ); ?>
			</span>
		</nav>

		<!-- Post em destaque -->
		<div class="blog-destaque__conteudo">
			<a
				class="blog-destaque__imagem-link"
				href="<?php echo esc_url( $permalink ); ?>"
				aria-label="<?php echo esc_attr( $titulo ); ?>"
			>
				<div class="blog-destaque__imagem">
					<?php echo starter_thumb( $post_destaque->ID, 'starter-blog-destaque', array( 'alt' => '' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- starter_thumb é wrapper de wp_get_attachment_image que escapa internamente. ?>
				</div>
			</a>

			<div class="blog-destaque__info">
				<?php if ( $categoria && $categoria_link ) : ?>
					<div class="blog-tag">
						<a href="<?php echo esc_url( $categoria_link ); ?>">
							<?php echo esc_html( mb_strtoupper( $categoria ) ); ?>
						</a>
					</div>
				<?php endif; ?>

				<a
					class="blog-destaque__info-link"
					href="<?php echo esc_url( $permalink ); ?>"
					aria-label="<?php echo esc_attr( $titulo ); ?>"
				>
					<h1 class="blog-destaque__titulo">
						<?php echo esc_html( $titulo ); ?>
					</h1>

					<p class="blog-destaque__data">
						<?php echo esc_html( $data ); ?>
					</p>

					<?php if ( $excerpt ) : ?>
						<p class="blog-destaque__excerpt">
							<?php echo esc_html( $excerpt ); ?>
						</p>
					<?php endif; ?>
				</a>
			</div>
		</div>

	</div>
</section>
