<?php
/**
 * starter/secao-texto — render no front.
 *
 * Reproduz o corpo do page.php (container + entry__content) como bloco: dá
 * aos blocos nativos um contexto estilizado dentro do template canvas.
 *
 * @package Started_Theme
 *
 * @var string $content Filhos renderizados (InnerBlocks).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<section class="starter-bl-texto">
	<div class="starter-bl-container">
		<div class="entry__content">
			<?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput -- filhos já renderizados/escapados pelo Core. ?>
		</div>
	</div>
</section>
