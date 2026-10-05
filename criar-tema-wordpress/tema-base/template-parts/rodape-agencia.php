<?php
/**
 * Template Part: faixa de rodapé da agência (assinatura da Agência R8).
 *
 * Renderizada DEPOIS do <footer> do site (ver footer.php) — é uma faixa
 * independente, não substitui o rodapé. Traz o copyright do cliente e os links
 * legais à esquerda e o selo de autoria da agência à direita.
 *
 * Estilos: bloco ".rodape-agencia" em assets/css/theme.css.
 *
 * @package Started_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$starter_copyright = sprintf(
	/* translators: 1: nome do site, 2: ano atual. */
	esc_html__( '© %1$s %2$s. Todos os direitos reservados.', 'started-theme' ),
	'<strong>' . esc_html( get_bloginfo( 'name' ) ) . '</strong>',
	esc_html( gmdate( 'Y' ) )
);
?>

<div class="rodape-agencia">
	<div class="container">
		<div class="rodape-agencia__flex">

			<div class="rodape-agencia__conteudo">
				<p><?php echo $starter_copyright; // phpcs:ignore WordPress.Security.EscapeOutput -- partes escapadas acima. ?></p>

				</div>

			<div class="rodape-agencia__logos">
				<a href="https://agenciar8.com.br" target="_blank" rel="noopener noreferrer"
					title="<?php esc_attr_e( 'Design e desenvolvimento', 'started-theme' ); ?>">
					<img src="https://agenciar8.com.br/footerlogos/agenciar8.png"
						alt="<?php esc_attr_e( 'Logo Agência R8', 'started-theme' ); ?>"
						width="33" height="34" loading="lazy" decoding="async">
				</a>
			</div>

		</div>
	</div>
</div>
