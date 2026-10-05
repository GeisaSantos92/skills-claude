<?php
/**
 * starter/hero — render no front.
 *
 * O visual vive em assets/css/blocks.css para o editor reusar as mesmas
 * classes. Imagem por attachment ID (a URL salva no atributo é só para o
 * preview do editor).
 *
 * @package Started_Theme
 *
 * @var array $attributes Atributos do bloco.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$badge     = $attributes['badge'] ?? '';
$titulo    = $attributes['titulo'] ?? '';
$subtitulo = $attributes['subtitulo'] ?? '';
$imagem_id = absint( $attributes['imagemId'] ?? 0 );
$imagem    = $imagem_id ? (string) wp_get_attachment_image_url( $imagem_id, 'full' ) : '';
?>
<section class="starter-bl-hero" aria-label="<?php esc_attr_e( 'Hero da página', 'started-theme' ); ?>">
	<div
		class="starter-bl-hero-bg"
		<?php if ( $imagem ) : ?>
		style="background-image:url('<?php echo esc_url( $imagem ); ?>');"
		<?php endif; ?>
		aria-hidden="true"
	></div>
	<div class="starter-bl-hero-overlay" aria-hidden="true"></div>

	<div class="starter-bl-container starter-bl-hero-conteudo">
		<?php if ( $badge ) : ?>
		<div>
			<span class="starter-bl-kicker starter-bl-kicker--claro"><?php echo wp_kses_post( $badge ); ?></span>
		</div>
		<?php endif; ?>

		<?php if ( $titulo ) : ?>
		<h1 class="starter-bl-hero-titulo"><?php echo wp_kses_post( $titulo ); ?></h1>
		<?php endif; ?>

		<?php if ( $subtitulo ) : ?>
		<p class="starter-bl-hero-subtitulo"><?php echo wp_kses_post( $subtitulo ); ?></p>
		<?php endif; ?>
	</div>
</section>
