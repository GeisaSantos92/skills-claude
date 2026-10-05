<?php
/**
 * Tamanhos de imagem customizados do tema.
 *
 * Registra tamanhos específicos para cada contexto do layout, evitando que o
 * WordPress sirva imagens genéricas (medium/large) maiores ou menores que o
 * necessário. Após alterar esses valores, rodar:
 *
 *   ./bin/wp media regenerate --yes
 *
 * Contextos e dimensões (baseados no design system e CSS do tema):
 *
 * | Handle                | Uso                                     | Dimensão     |
 * |-----------------------|-----------------------------------------|--------------|
 * | starter-blog-card     | Thumbnail nos cards da listagem do blog | 600 × 400 px |
 * | starter-blog-destaque | Imagem do post em destaque no topo      | 1200 × 630px |
 * | starter-logo          | Logos de clientes/parceiros (esteiras)  | 320 × 90 px  |
 *
 * Adicione aqui os tamanhos que o layout do projeto pedir — nunca use o
 * tamanho "full" em grids.
 *
 * @package Started_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registra os tamanhos de imagem customizados.
 *
 * @return void
 */
function starter_register_image_sizes() {
	// Cards da listagem do blog (grid de artigos).
	add_image_size( 'starter-blog-card', 600, 400, true );

	// Post em destaque no topo da página do blog — proporção OG-friendly.
	add_image_size( 'starter-blog-destaque', 1200, 630, true );

	// Logos de clientes/parceiros: margem de ~10% sobre o tamanho exibido.
	// Sem crop (false) para preservar proporção do logo.
	add_image_size( 'starter-logo', 320, 90, false );
}
add_action( 'after_setup_theme', 'starter_register_image_sizes' );
