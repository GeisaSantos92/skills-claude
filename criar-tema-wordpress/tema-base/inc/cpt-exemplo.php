<?php
/**
 * CPT de exemplo — molde para os Custom Post Types do projeto.
 *
 * Conteúdos que se repetem no site (e que o cliente precisa manter sozinho)
 * viram CPT — nunca campo repetidor gigante nem HTML fixo no template.
 * Como o ACF Free não tem Repeater, TODA lista que pode crescer vira CPT.
 *
 * Para criar um CPT novo: duplique este arquivo (inc/cpt-{nome}.php), troque
 * `starter_exemplo` pelo slug do CPT, ajuste labels/suportes, registre em
 * functions.php e rode `./bin/wp rewrite flush`.
 *
 * Remova este arquivo (e o require) quando o projeto tiver os próprios CPTs.
 *
 * @package Started_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'starter_cpt_labels' ) ) {
	/**
	 * Monta o array de labels de um CPT a partir do singular/plural.
	 *
	 * @param string $singular Nome no singular (ex.: 'Depoimento').
	 * @param string $plural   Nome no plural (ex.: 'Depoimentos').
	 * @param string $genero   'm' (masculino) ou 'f' (feminino), para as concordâncias.
	 * @return array<string,string>
	 */
	function starter_cpt_labels( $singular, $plural, $genero = 'm' ) {
		$novo   = ( 'f' === $genero ) ? 'Nova' : 'Novo';
		$o      = ( 'f' === $genero ) ? 'a' : 'o';
		$nenhum = ( 'f' === $genero ) ? 'Nenhuma' : 'Nenhum';

		return array(
			'name'               => $plural,
			'singular_name'      => $singular,
			'menu_name'          => $plural,
			'add_new'            => sprintf( '%s %s', $novo, $singular ),
			'add_new_item'       => sprintf( 'Adicionar %s %s', $novo, $singular ),
			'edit_item'          => sprintf( 'Editar %s', $singular ),
			'new_item'           => sprintf( '%s %s', $novo, $singular ),
			'view_item'          => sprintf( 'Ver %s', $singular ),
			'view_items'         => sprintf( 'Ver %s', $plural ),
			'search_items'       => sprintf( 'Buscar %s', $plural ),
			'not_found'          => sprintf( '%s %s encontrad%s.', $nenhum, $singular, $o ),
			'not_found_in_trash' => sprintf( '%s %s na lixeira.', $nenhum, $singular ),
			'all_items'          => sprintf( 'Tod%ss %s', $o, $plural ),
			'archives'           => sprintf( 'Arquivo de %s', $plural ),
		);
	}
}

/**
 * Registra o CPT de exemplo.
 *
 * Listagem interna (só aparece dentro de outras páginas): public false,
 * sem archive. Para ter /exemplos/ e single próprio, use public true,
 * has_archive 'exemplos' e rewrite array( 'slug' => 'exemplo' ) — e crie
 * archive-starter_exemplo.php / single-starter_exemplo.php.
 *
 * @return void
 */
function starter_register_cpt_exemplo() {
	register_post_type(
		'starter_exemplo',
		array(
			'labels'        => starter_cpt_labels( 'Exemplo', 'Exemplos' ),
			'public'        => false,
			'show_ui'       => true,
			'show_in_menu'  => true,
			'show_in_rest'  => true,
			'menu_icon'     => 'dashicons-screenoptions',
			'menu_position' => 21,
			'supports'      => array( 'title', 'thumbnail', 'page-attributes' ),
			'has_archive'   => false,
			'rewrite'       => false,
		)
	);
}
add_action( 'init', 'starter_register_cpt_exemplo' );
