<?php
/**
 * Blocos Gutenberg do tema — seções para landing pages (opcional).
 *
 * Caminho sem build: blocos server-rendered registrados via block.json +
 * render.php, com editor.js escrito à mão (wp.element.createElement, sem JSX).
 * Cada bloco vive em blocks/<nome>/; os estilos compartilhados entre front e
 * editor ficam em assets/css/blocks.css.
 *
 * Este módulo é OPCIONAL: o require em functions.php vem comentado. Ative-o
 * quando o projeto precisar de landing pages montadas pelo editor. As demais
 * páginas continuam 100% PHP/ACF — ver docs/blocks.md.
 *
 * @package Started_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Lista central dos blocos do tema (nomes dos diretórios em blocks/).
 *
 * Para criar um bloco novo: duplique um diretório de blocks/, ajuste
 * block.json/render.php/editor.js e adicione o nome aqui.
 *
 * @return string[]
 */
function starter_blocks_lista(): array {
	return array( 'hero', 'stats', 'stat-item', 'secao-texto' );
}

/**
 * Carrega o CSS do site dentro do canvas do editor.
 *
 * Sem isso o Gutenberg renderiza os blocos "pelados": o CSS do tema e dos
 * blocos só existiria no front.
 *
 * @return void
 */
function starter_blocks_editor_styles() {
	add_theme_support( 'editor-styles' );
	add_editor_style(
		array(
			'assets/css/theme.css',
			'assets/css/blocks.css',
			'assets/css/blocks-editor.css',
		)
	);
}
add_action( 'after_setup_theme', 'starter_blocks_editor_styles' );

/**
 * Registra o script de editor e o block type de cada bloco.
 *
 * O editor.js é JS puro (wp.element.createElement) — por isso o registro
 * manual com dependências explícitas em vez do par file:./index.js +
 * index.asset.php que a toolchain de build geraria.
 *
 * @return void
 */
function starter_blocks_register() {
	foreach ( starter_blocks_lista() as $bloco ) {
		wp_register_script(
			"starter-block-{$bloco}",
			get_theme_file_uri( "/blocks/{$bloco}/editor.js" ),
			array( 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-i18n' ),
			starter_asset_version( "/blocks/{$bloco}/editor.js" ),
			true
		);

		register_block_type( get_theme_file_path( "/blocks/{$bloco}" ) );
	}
}
add_action( 'init', 'starter_blocks_register' );

/**
 * Enfileira no front o CSS compartilhado dos blocos.
 *
 * @return void
 */
function starter_blocks_enqueue_front() {
	wp_enqueue_style(
		'starter-blocks',
		get_theme_file_uri( '/assets/css/blocks.css' ),
		array( 'starter-theme' ),
		starter_asset_version( '/assets/css/blocks.css' )
	);
}
add_action( 'wp_enqueue_scripts', 'starter_blocks_enqueue_front' );

/**
 * Adiciona a categoria "Seções do tema" no topo do inserter.
 *
 * @param array $categorias Categorias registradas.
 * @return array
 */
function starter_blocks_categoria( array $categorias ): array {
	array_unshift(
		$categorias,
		array(
			'slug'  => 'starter-secoes',
			'title' => __( 'Seções do tema', 'started-theme' ),
		)
	);

	return $categorias;
}
add_filter( 'block_categories_all', 'starter_blocks_categoria' );

/**
 * Allowlist de blocos nas páginas com o template landing (canvas).
 *
 * Blocos nativos soltos no canvas renderizam sem o container/tipografia do
 * tema — por isso o inserter fica restrito às seções do tema + um conjunto
 * mínimo de nativos, pensados para viver dentro do bloco "Seção de texto".
 *
 * @param bool|string[]           $permitidos Blocos permitidos até aqui.
 * @param WP_Block_Editor_Context $contexto   Contexto do editor.
 * @return bool|string[]
 */
function starter_blocks_allowlist( $permitidos, $contexto ) {
	if ( empty( $contexto->post ) || 'page' !== $contexto->post->post_type ) {
		return $permitidos;
	}

	if ( 'page-templates/landing.php' !== get_page_template_slug( $contexto->post ) ) {
		return $permitidos;
	}

	$secoes = array_map(
		static function ( $bloco ) {
			return "starter/{$bloco}";
		},
		starter_blocks_lista()
	);

	return array_merge(
		$secoes,
		array(
			'core/paragraph',
			'core/heading',
			'core/list',
			'core/list-item',
			'core/image',
			'core/spacer',
			'core/embed',
		)
	);
}
add_filter( 'allowed_block_types_all', 'starter_blocks_allowlist', 10, 2 );
