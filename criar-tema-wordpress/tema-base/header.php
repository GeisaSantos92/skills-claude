<?php
/**
 * Header do tema.
 *
 * Logo (Customizer), menu principal (menu "principal", com dropdowns), seletor
 * de idioma do Polylang e os dois botões de ação — todos configuráveis no
 * Customizer → Opções do Tema. Nenhum item é fixo no código.
 *
 * Assets enfileirados em inc/enqueue.php — nenhum <link>/<script> aqui.
 *
 * @package Started_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link" href="#primary"><?php esc_html_e( 'Pular para o conteúdo', 'started-theme' ); ?></a>

<header class="site-header">
	<div class="site-header__inner">

		<a
			class="site-header__brand"
			href="<?php echo esc_url( home_url( '/' ) ); ?>"
			aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>"
		>
			<?php starter_logo( 'escuro', 176 ); ?>
		</a>

		<nav
			id="site-nav"
			class="site-nav"
			aria-label="<?php esc_attr_e( 'Menu principal', 'started-theme' ); ?>"
		>
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'principal',
					'container'      => false,
					'menu_class'     => 'site-nav__list',
					'fallback_cb'    => false,
					'walker'         => new Starter_Walker_Nav_Menu(),
				)
			);
			?>

			<div class="site-nav__acoes-mobile">
				<?php starter_header_acoes(); ?>
			</div>
		</nav>

		<div class="site-header__acoes">
			<?php starter_seletor_idiomas(); ?>
			<?php starter_header_acoes(); ?>
		</div>

		<button
			class="site-header__toggle"
			data-nav-toggle
			aria-controls="site-nav"
			aria-expanded="false"
			aria-label="<?php esc_attr_e( 'Abrir menu', 'started-theme' ); ?>"
		>
			<span class="site-header__toggle-bar" aria-hidden="true"></span>
			<span class="site-header__toggle-bar" aria-hidden="true"></span>
			<span class="site-header__toggle-bar" aria-hidden="true"></span>
		</button>

	</div>
</header>
