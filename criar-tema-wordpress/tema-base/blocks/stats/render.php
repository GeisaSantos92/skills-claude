<?php
/**
 * starter/stats — render no front.
 *
 * Exemplo do padrão pai/filho: os filhos (starter/stat-item) chegam já
 * renderizados em $content; este arquivo só monta a moldura da seção.
 *
 * @package Started_Theme
 *
 * @var array  $attributes Atributos do bloco.
 * @var string $content    Filhos renderizados (InnerBlocks).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$badge  = $attributes['badge'] ?? '';
$titulo = $attributes['titulo'] ?? '';
?>
<section class="starter-bl-stats">
	<div class="starter-bl-container">
		<?php if ( $badge || $titulo ) : ?>
		<div class="starter-bl-stats-header">
			<?php if ( $badge ) : ?>
			<span class="starter-bl-kicker starter-bl-kicker--claro"><?php echo wp_kses_post( $badge ); ?></span>
			<?php endif; ?>
			<?php if ( $titulo ) : ?>
			<h2 class="starter-bl-stats-titulo"><?php echo wp_kses_post( $titulo ); ?></h2>
			<?php endif; ?>
		</div>
		<?php endif; ?>

		<div class="starter-bl-stats-grid">
			<?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput -- filhos já renderizados/escapados pelo Core. ?>
		</div>
	</div>
</section>
