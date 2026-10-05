<?php
/**
 * Home — hero (seção de exemplo).
 *
 * Molde para as demais seções: lê o ACF com starter_campo() (sempre com
 * valor padrão), sai cedo quando não há conteúdo e escapa tudo na saída.
 *
 * @package Started_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$eyebrow   = starter_campo( 'hero_eyebrow' );
$titulo    = starter_campo( 'hero_titulo' );
$subtitulo = starter_campo( 'hero_subtitulo' );
$imagem    = starter_campo_imagem( 'hero_imagem', 'large', array( 'loading' => 'eager', 'fetchpriority' => 'high' ) );

if ( ! $titulo ) {
	return;
}
?>

<section class="hero">
	<div class="container hero__inner">
		<div class="hero__texto">
			<?php if ( $eyebrow ) : ?>
				<span class="eyebrow eyebrow--pill"><?php echo esc_html( $eyebrow ); ?></span>
			<?php endif; ?>

			<h1 class="hero__titulo"><?php echo starter_enfase( $titulo ); // phpcs:ignore WordPress.Security.EscapeOutput -- escapado em starter_enfase(). ?></h1>

			<?php if ( $subtitulo ) : ?>
				<p class="hero__subtitulo"><?php echo esc_html( $subtitulo ); ?></p>
			<?php endif; ?>

			<div class="hero__acoes">
				<?php starter_botao( 'hero_botao' ); ?>
				<?php starter_botao( 'hero_botao_secundario', 'botao botao--contorno', '' ); ?>
			</div>
		</div>

		<?php if ( $imagem ) : ?>
			<div class="hero__midia">
				<?php echo $imagem; // phpcs:ignore WordPress.Security.EscapeOutput -- wp_get_attachment_image escapa internamente. ?>
			</div>
		<?php endif; ?>
	</div>
</section>
