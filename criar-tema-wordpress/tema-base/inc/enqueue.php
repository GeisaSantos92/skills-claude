<?php
/**
 * Enfileiramento de assets (CSS/JS).
 *
 * Regras (ver docs/code-standards.md):
 * - Nada de <link>/<script> no HTML: tudo entra por wp_enqueue_*.
 * - Cache-busting por filemtime() (versão automática por arquivo).
 * - Caminhos via get_theme_file_uri()/get_theme_file_path().
 * - Handles prefixados com `starter-`.
 * - CSS/JS por contexto: a home carrega front-page.css só em is_front_page().
 *
 * Fontes são auto-hospedadas (assets/fonts/*.woff2): zero requisição a CDN.
 *
 * @package Started_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Retorna a versão de cache-busting de um asset (filemtime), com fallback.
 *
 * @param string $relative Caminho relativo à raiz do tema.
 * @return string|false Timestamp de modificação, ou a versão do tema como fallback.
 */
function starter_asset_version( $relative ) {
	$path = get_theme_file_path( $relative );

	if ( file_exists( $path ) ) {
		return (string) filemtime( $path );
	}

	return wp_get_theme()->get( 'Version' );
}

/**
 * Enfileira os estilos e scripts do tema.
 *
 * @return void
 */
function starter_enqueue_assets() {
	// @font-face das fontes auto-hospedadas.
	wp_enqueue_style(
		'starter-fonts',
		get_theme_file_uri( '/assets/css/fonts.css' ),
		array(),
		starter_asset_version( '/assets/css/fonts.css' )
	);

	// Tokens, reset, chrome e componentes globais.
	wp_enqueue_style(
		'starter-theme',
		get_theme_file_uri( '/assets/css/theme.css' ),
		array( 'starter-fonts' ),
		starter_asset_version( '/assets/css/theme.css' )
	);

	// Seções da página inicial.
	if ( is_front_page() ) {
		wp_enqueue_style(
			'starter-front-page',
			get_theme_file_uri( '/assets/css/front-page.css' ),
			array( 'starter-theme' ),
			starter_asset_version( '/assets/css/front-page.css' )
		);
	}

	// Listagem do blog (home.php — índice de posts nativos).
	if ( is_home() ) {
		wp_enqueue_style(
			'starter-blog',
			get_theme_file_uri( '/assets/css/blog.css' ),
			array( 'starter-theme' ),
			starter_asset_version( '/assets/css/blog.css' )
		);
	}

	// Página interna de post do blog.
	if ( is_singular( 'post' ) ) {
		wp_enqueue_style(
			'starter-single',
			get_theme_file_uri( '/assets/css/single.css' ),
			array( 'starter-theme' ),
			starter_asset_version( '/assets/css/single.css' )
		);
	}

	/*
	 * CSS por página: um arquivo por page-{slug}.php, enfileirado só nela.
	 * starter_e_pagina() também casa a tradução da página (Polylang), que
	 * tem slug próprio.
	 *
	 * if ( starter_e_pagina( 'contato' ) ) {
	 *     wp_enqueue_style(
	 *         'starter-contato',
	 *         get_theme_file_uri( '/assets/css/page-contato.css' ),
	 *         array( 'starter-theme' ),
	 *         starter_asset_version( '/assets/css/page-contato.css' )
	 *     );
	 * }
	 */

	// Comportamentos vanilla (menu, submenus, acordeão, cookies).
	wp_enqueue_script(
		'starter-theme',
		get_theme_file_uri( '/assets/js/theme.js' ),
		array(),
		starter_asset_version( '/assets/js/theme.js' ),
		true
	);
}
add_action( 'wp_enqueue_scripts', 'starter_enqueue_assets' );

/**
 * Adiciona o atributo `defer` a todos os scripts enfileirados pelo tema.
 *
 * Todos os handles com prefixo `starter-` recebem defer. Scripts já no
 * footer sem defer ainda podem bloquear o parser em conexões lentas; com defer
 * o browser baixa e executa após o parsing do HTML — melhora FCP/LCP.
 *
 * @param string $tag    HTML da tag <script>.
 * @param string $handle Handle do script.
 * @return string HTML modificado.
 */
function starter_defer_scripts( $tag, $handle ) {
	if ( ! str_starts_with( $handle, 'starter-' ) ) {
		return $tag;
	}

	if ( str_contains( $tag, ' defer' ) ) {
		return $tag;
	}

	return str_replace( ' src=', ' defer src=', $tag );
}
add_filter( 'script_loader_tag', 'starter_defer_scripts', 10, 2 );

/**
 * Pré-carrega as fontes acima da dobra (evita FOUT no conteúdo inicial).
 *
 * Liste aqui só as fontes do texto corrido e do H1 — preload demais compete
 * com a imagem do LCP.
 *
 * @return void
 */
function starter_preload_fonts() {
	$fontes = array(
		'/assets/fonts/inter-400_700-latin.woff2', // corpo do texto e títulos
	);

	foreach ( $fontes as $fonte ) {
		if ( ! file_exists( get_theme_file_path( $fonte ) ) ) {
			continue;
		}

		printf(
			'<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n",
			esc_url( get_theme_file_uri( $fonte ) )
		);
	}
}
add_action( 'wp_head', 'starter_preload_fonts', 1 );
