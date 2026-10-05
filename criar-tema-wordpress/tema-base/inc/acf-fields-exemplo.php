<?php
/**
 * Campos ACF do CPT de exemplo (registrados via código — nunca pelo painel).
 *
 * Duplique junto com inc/cpt-exemplo.php. Chaves (`group_*`, `field_*`) são
 * únicas no site inteiro e não mudam depois de publicadas: o valor salvo no
 * banco é ligado à chave.
 *
 * @package Started_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registra o grupo de campos do CPT de exemplo.
 *
 * @return void
 */
function starter_acf_fields_exemplo() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group(
		array(
			'key'            => 'group_starter_exemplo',
			'title'          => 'Dados do Exemplo',
			'location'       => array(
				array(
					array(
						'param'    => 'post_type',
						'operator' => '==',
						'value'    => 'starter_exemplo',
					),
				),
			),
			'menu_order'     => 0,
			'position'       => 'normal',
			'hide_on_screen' => array( 'the_content', 'excerpt', 'custom_fields', 'discussion', 'comments' ),
			'fields'         => array(
				array(
					'key'          => 'field_exemplo_resumo',
					'label'        => 'Resumo',
					'name'         => 'exemplo_resumo',
					'type'         => 'textarea',
					'rows'         => 3,
					'new_lines'    => '',
					'instructions' => 'Texto curto exibido no card.',
				),
				array(
					'key'           => 'field_exemplo_link',
					'label'         => 'Link',
					'name'          => 'exemplo_link',
					'type'          => 'link',
					'return_format' => 'array',
				),
			),
		)
	);
}
add_action( 'acf/init', 'starter_acf_fields_exemplo' );
