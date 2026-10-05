<?php
/**
 * Post interna — Matéria: título, subtítulo, data, imagem, conteúdo e tags.
 *
 * @package Started_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$starter_titulo = get_the_title();

/*
 * O lead é o resumo escrito à mão. Com get_the_excerpt() o WordPress monta um
 * resumo a partir do conteúdo quando o campo está vazio, e o subtítulo acaba
 * repetindo o primeiro parágrafo da matéria — no layout ele é um texto próprio.
 */
$starter_subtitulo = has_excerpt() ? get_the_excerpt() : '';
$starter_data      = get_the_date( 'j F Y' );
$starter_cats      = get_the_category();
?>

<section class="post-secao">
	<div class="post-secao__inner">

		<article <?php post_class( 'post-materia' ); ?>>

			<!-- Cabeçalho -->
			<header class="post-materia__heading">
				<h1 class="post-materia__titulo">
					<?php echo esc_html( $starter_titulo ); ?>
				</h1>
			</header>

			<?php if ( $starter_subtitulo ) : ?>
			<div class="post-materia__subtitulo-wrapper">
				<p class="post-materia__subtitulo">
					<?php echo esc_html( $starter_subtitulo ); ?>
				</p>
			</div>
			<?php endif; ?>

			<div class="post-materia__data-wrapper">
				<p class="post-materia__data">
					<?php
					printf(
						/* translators: %s: data de publicação */
						esc_html__( 'Publicado em: %s', 'started-theme' ),
						esc_html( $starter_data )
					);
					?>
				</p>
			</div>

			<!-- Corpo: imagem + texto -->
			<div class="post-materia__corpo">

				<?php if ( has_post_thumbnail() ) : ?>
				<div class="post-materia__imagem">
					<?php the_post_thumbnail( 'large', array( 'loading' => 'eager' ) ); ?>
				</div>
				<?php endif; ?>

				<div class="post-materia__texto">
					<?php the_content(); ?>
				</div>

			</div>

			<!-- Tags / categorias -->
			<?php if ( $starter_cats ) : ?>
			<div class="post-materia__tags">
				<span class="post-materia__tags-label">
					<?php esc_html_e( 'Assuntos', 'started-theme' ); ?>
				</span>
				<div class="post-materia__tags-lista">
					<?php foreach ( $starter_cats as $starter_cat ) : ?>
						<div class="blog-tag">
							<span><?php echo esc_html( mb_strtoupper( $starter_cat->name ) ); ?></span>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
			<?php endif; ?>

		</article>

	</div>
</section>
