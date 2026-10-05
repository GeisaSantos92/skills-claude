<?php
/**
 * Banner de cookies — cartão flutuante no canto inferior esquerdo.
 *
 * Informativo, com um clique: o texto, o rótulo do link e o do botão vêm do
 * Customizer (traduzidos pelo Polylang). Quem esconde e lembra a escolha é o
 * theme.js, via localStorage — sem cookie e sem plugin de CMP.
 *
 * O canto esquerdo é proposital: o WhatsApp e o "voltar ao topo" ficam à
 * direita.
 *
 * @package Started_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$starter_texto = starter_mod_traduzido( 'starter_cookies_texto' );

if ( ! $starter_texto ) {
	return;
}

$starter_botao      = starter_mod_traduzido( 'starter_cookies_botao' );
$starter_link_texto = starter_mod_traduzido( 'starter_cookies_link_texto' );
$starter_link_url   = starter_url_privacidade();
?>

<aside
	class="cookie-banner"
	data-cookie-banner
	hidden
	aria-label="<?php esc_attr_e( 'Aviso de cookies', 'started-theme' ); ?>"
>
	<p class="cookie-banner__texto">
		<?php echo esc_html( $starter_texto ); ?>
		<?php if ( $starter_link_texto && $starter_link_url ) : ?>
			<a class="cookie-banner__link" href="<?php echo esc_url( $starter_link_url ); ?>"><?php echo esc_html( $starter_link_texto ); ?></a>.
		<?php endif; ?>
	</p>

	<?php if ( $starter_botao ) : ?>
		<button class="cookie-banner__botao botao botao--primario" type="button" data-cookie-aceitar>
			<?php echo esc_html( $starter_botao ); ?>
		</button>
	<?php endif; ?>
</aside>
