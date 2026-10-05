<?php
/**
 * Footer do tema.
 *
 * Estrutura:
 * - Bloco CTA (título + botão), sobreposto à transição para o rodapé (Customizer).
 * - Rodapé escuro: colunas de links (menu "rodape", onde cada item de 1º
 *   nível é o título de uma coluna) e, abaixo delas, a barra com o logo à
 *   esquerda e os ícones sociais à direita (Customizer).
 * - Faixa da agência (template-parts/rodape-agencia.php), com o copyright
 *   do cliente e o selo da Agência R8.
 * - Botões flutuantes: voltar ao topo e WhatsApp (Customizer).
 * - Banner de cookies (template-parts/cookie-banner.php).
 *
 * @package Started_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$cta_titulo = starter_mod_traduzido( 'starter_cta_titulo' );
$cta_texto  = starter_mod_traduzido( 'starter_cta_botao_texto' );
$cta_url    = get_theme_mod( 'starter_cta_botao_url' ) ?? '';
$whatsapp   = get_theme_mod( 'starter_whatsapp_url' ) ?? '';

$sociais = array();
foreach ( starter_social_networks() as $rede => $rotulo ) {
	$url = get_theme_mod( 'starter_social_' . $rede ) ?? '';
	if ( $url ) {
		$sociais[ $rede ] = array( $rotulo, $url );
	}
}
?>

<?php if ( $cta_titulo ) : ?>
<section class="footer-cta">
	<div class="footer-cta__inner">
		<div class="footer-cta__caixa">
			<h2 class="footer-cta__titulo"><?php echo nl2br( esc_html( $cta_titulo ) ); ?></h2>

			<?php if ( $cta_texto && $cta_url ) : ?>
				<a class="botao botao--branco" href="<?php echo esc_url( $cta_url ); ?>">
					<?php echo esc_html( $cta_texto ); ?>
					<?php echo starter_icone( 'seta-direita' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- starter_icone retorna SVG estático do tema. ?>
				</a>
			<?php endif; ?>
		</div>
	</div>
</section>
<?php endif; ?>

<footer class="site-footer">
	<div class="site-footer__inner">

		<?php if ( has_nav_menu( 'rodape' ) ) : ?>
		<div class="site-footer__links">
			<nav aria-label="<?php esc_attr_e( 'Links do rodapé', 'started-theme' ); ?>">
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'rodape',
						'container'      => false,
						'menu_class'     => 'site-footer__grid',
						'fallback_cb'    => false,
						'depth'          => 3,
					)
				);
				?>
			</nav>
		</div>
		<?php endif; ?>

		<div class="site-footer__top">

			<div class="site-footer__brand-wrap">
				<a class="site-footer__brand" href="<?php echo esc_url( home_url( '/' ) ); ?>">
					<?php starter_logo( 'claro', 200 ); ?>
				</a>
			</div>

			<?php if ( $sociais ) : ?>
			<div class="site-footer__sociais">
				<?php foreach ( $sociais as $rede => list( $rotulo, $url ) ) : ?>
				<a
					class="site-footer__social-link"
					href="<?php echo esc_url( $url ); ?>"
					target="_blank"
					rel="noopener noreferrer"
					aria-label="<?php echo esc_attr( $rotulo ); ?>"
				>
					<?php echo starter_social_icon( $rede ); // phpcs:ignore WordPress.Security.EscapeOutput -- SVG gerado internamente. ?>
				</a>
				<?php endforeach; ?>
			</div>
			<?php endif; ?>

		</div>

		<?php if ( has_nav_menu( 'rodape_legal' ) ) : ?>
		<nav class="site-footer__legal" aria-label="<?php esc_attr_e( 'Links legais', 'started-theme' ); ?>">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'rodape_legal',
					'container'      => false,
					'menu_class'     => 'site-footer__legal-lista',
					'fallback_cb'    => false,
					'depth'          => 1,
				)
			);
			?>
		</nav>
		<?php endif; ?>
	</div>
</footer>

<?php get_template_part( 'template-parts/rodape-agencia' ); ?>

<button
	class="voltar-ao-topo"
	type="button"
	data-voltar-ao-topo
	aria-label="<?php esc_attr_e( 'Voltar ao topo', 'started-theme' ); ?>"
>
	<?php echo starter_icone( 'chevron-cima', 28 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- starter_icone retorna SVG estático do tema. ?>
</button>

<?php if ( $whatsapp ) : ?>
<a
	class="whatsapp-flutuante"
	href="<?php echo esc_url( $whatsapp ); ?>"
	target="_blank"
	rel="noopener noreferrer"
	aria-label="<?php esc_attr_e( 'Falar no WhatsApp', 'started-theme' ); ?>"
>
	<?php echo starter_icone( 'whatsapp', 32 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- starter_icone retorna SVG estático do tema. ?>
</a>
<?php endif; ?>

<?php get_template_part( 'template-parts/cookie-banner' ); ?>

<?php wp_footer(); ?>
</body>
</html>
