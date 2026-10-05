<?php
/**
 * Started Theme — bootstrap do tema (maestro).
 *
 * Este arquivo NÃO contém lógica de negócio: apenas o setup mínimo do tema
 * (after_setup_theme) e o carregamento seguro dos módulos de `inc/`.
 * Cada responsabilidade vive no seu módulo. Ver docs/architecture.md.
 *
 * @package Started_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Carrega um módulo de `inc/` com segurança (checa existência antes do require).
 *
 * @param string $relative Caminho relativo à raiz do tema (ex.: '/inc/enqueue.php').
 * @return void
 */
function starter_require( $relative ) {
	$path = get_theme_file_path( $relative );

	if ( file_exists( $path ) ) {
		require $path;
	}
}

/**
 * Setup do tema: suportes, menus e text-domain.
 *
 * @return void
 */
function starter_setup() {
	// Deixa o WordPress gerenciar a tag <title> do documento.
	add_theme_support( 'title-tag' );

	// Imagens destacadas (thumbnails) nos posts/páginas e CPTs.
	add_theme_support( 'post-thumbnails' );

	// Marcação HTML5 semântica para os recursos gerados pelo Core.
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		)
	);

	// Embeds (YouTube, etc.) responsivos no conteúdo.
	add_theme_support( 'responsive-embeds' );

	// Feeds automáticos (RSS/Atom) no <head>.
	add_theme_support( 'automatic-feed-links' );

	// Menus de navegação: cabeçalho e rodapé (colunas + linha legal).
	register_nav_menus(
		array(
			'principal'    => __( 'Cabeçalho — Menu Principal', 'started-theme' ),
			'rodape'       => __( 'Rodapé — Colunas de Links', 'started-theme' ),
			'rodape_legal' => __( 'Rodapé — Links Legais (linha inferior)', 'started-theme' ),
		)
	);

	// Traduções do tema (arquivos .mo em /languages).
	load_theme_textdomain( 'started-theme', get_theme_file_path( '/languages' ) );
}
add_action( 'after_setup_theme', 'starter_setup' );

// Infraestrutura do tema.
starter_require( '/inc/image-sizes.php' );
starter_require( '/inc/enqueue.php' );
starter_require( '/inc/clean-head.php' );
starter_require( '/inc/customizer.php' );
starter_require( '/inc/analytics.php' );
starter_require( '/inc/seo.php' );
starter_require( '/inc/icons.php' );
starter_require( '/inc/template-tags.php' );
starter_require( '/inc/menu-walker.php' );
starter_require( '/inc/helpers.php' );
starter_require( '/inc/polylang.php' );
starter_require( '/inc/pagination.php' );
starter_require( '/inc/blog-filters.php' );
starter_require( '/inc/login.php' );
starter_require( '/inc/spam.php' );
starter_require( '/inc/smtp.php' );

// Conteúdo do projeto: CPTs e campos ACF (locais, via código).
// Para cada CPT: duplique os arquivos de exemplo, renomeie e ative aqui.
starter_require( '/inc/cpt-exemplo.php' );
starter_require( '/inc/acf-fields-exemplo.php' );
starter_require( '/inc/acf-fields-home.php' );

// Landing pages com blocos de conteúdo (opcional — ver docs/blocks.md).
// starter_require( '/inc/blocks.php' );

// Comandos WP-CLI: carregados apenas no contexto de linha de comando,
// inertes em produção/requisições web.
if ( defined( 'WP_CLI' ) && WP_CLI ) {
	starter_require( '/inc/cli/bootstrap.php' );
	starter_require( '/inc/cli/seed.php' );
}
