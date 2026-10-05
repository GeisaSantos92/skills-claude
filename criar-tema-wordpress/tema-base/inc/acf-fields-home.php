<?php
/**
 * Grupo ACF da página inicial.
 *
 * Todo o texto da home é editável aqui — nada de conteúdo fixo no template.
 * Uma aba por seção, na ordem do front-page.php. As listas que crescem vêm de
 * CPTs; aqui ficam só os textos das seções e os conjuntos de tamanho fixo
 * (ex.: 3 métricas), em campos numerados — o ACF Free não tem Repeater.
 *
 * Local: página definida como "Página inicial" em Configurações → Leitura
 * (inc/polylang.php estende a regra para as traduções da home).
 *
 * @package Started_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Helper: campo de texto simples.
 *
 * @param string $key   Sufixo da chave (field_home_{key}).
 * @param string $label Rótulo.
 * @param string $name  Nome do campo.
 * @param array  $extra Sobrescritas.
 * @return array
 */
function starter_acf_text( $key, $label, $name, $extra = array() ) {
	return array_merge(
		array(
			'key'   => 'field_home_' . $key,
			'label' => $label,
			'name'  => $name,
			'type'  => 'text',
		),
		$extra
	);
}

/**
 * Helper: aba do grupo ACF.
 *
 * @param string $key   Sufixo da chave.
 * @param string $label Rótulo da aba.
 * @return array
 */
function starter_acf_tab( $key, $label ) {
	return array(
		'key'       => 'field_home_tab_' . $key,
		'label'     => $label,
		'name'      => '',
		'type'      => 'tab',
		'placement' => 'left',
	);
}

/**
 * Helper: campo de link (retorna array com url/title/target).
 *
 * @param string $key   Sufixo da chave.
 * @param string $label Rótulo.
 * @param string $name  Nome do campo.
 * @return array
 */
function starter_acf_link( $key, $label, $name ) {
	return array(
		'key'           => 'field_home_' . $key,
		'label'         => $label,
		'name'          => $name,
		'type'          => 'link',
		'return_format' => 'array',
	);
}

/**
 * Registra o grupo de campos da home.
 *
 * @return void
 */
function starter_acf_fields_home() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	$fields = array();

	/* --- 1. HERO --------------------------------------------------------- */
	$fields[] = starter_acf_tab( 'hero', '1 · Hero' );
	$fields[] = starter_acf_text( 'hero_eyebrow', 'Selo acima do título', 'hero_eyebrow' );
	$fields[] = starter_acf_text(
		'hero_titulo',
		'Título',
		'hero_titulo',
		array( 'instructions' => 'Envolva um trecho em *asteriscos* para destacá-lo na cor da marca.' )
	);
	$fields[] = array(
		'key'       => 'field_home_hero_subtitulo',
		'label'     => 'Subtítulo',
		'name'      => 'hero_subtitulo',
		'type'      => 'textarea',
		'rows'      => 2,
		'new_lines' => '',
	);
	$fields[] = starter_acf_link( 'hero_botao', 'Botão principal', 'hero_botao' );
	$fields[] = starter_acf_link( 'hero_botao_secundario', 'Botão secundário', 'hero_botao_secundario' );
	$fields[] = array(
		'key'           => 'field_home_hero_imagem',
		'label'         => 'Imagem',
		'name'          => 'hero_imagem',
		'type'          => 'image',
		'return_format' => 'id',
		'preview_size'  => 'medium',
	);

	/* --- 2. PRÓXIMA SEÇÃO ------------------------------------------------ */
	// $fields[] = starter_acf_tab( 'secao', '2 · Nome da seção' );
	// ...

	acf_add_local_field_group(
		array(
			'key'            => 'group_starter_home',
			'title'          => 'Conteúdo da Página Inicial',
			'location'       => array(
				array(
					array(
						'param'    => 'page_type',
						'operator' => '==',
						'value'    => 'front_page',
					),
				),
			),
			'menu_order'     => 0,
			'position'       => 'normal',
			'style'          => 'default',
			'hide_on_screen' => array( 'the_content', 'excerpt', 'discussion', 'comments', 'author', 'format' ),
			'fields'         => $fields,
		)
	);
}
add_action( 'acf/init', 'starter_acf_fields_home' );
