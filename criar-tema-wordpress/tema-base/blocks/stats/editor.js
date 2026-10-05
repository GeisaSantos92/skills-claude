/**
 * starter/stats — editor (JS puro, sem build; ver inc/blocks.php).
 *
 * Itens repetíveis via InnerBlocks restrito a starter/stat-item: adicionar,
 * remover e reordenar usam a UI nativa do Gutenberg.
 */
( function ( wp ) {
	'use strict';

	var el = wp.element.createElement;
	var __ = wp.i18n.__;
	var useBlockProps = wp.blockEditor.useBlockProps;
	var useInnerBlocksProps = wp.blockEditor.useInnerBlocksProps;
	var InnerBlocks = wp.blockEditor.InnerBlocks;
	var RichText = wp.blockEditor.RichText;

	wp.blocks.registerBlockType( 'starter/stats', {
		edit: function ( props ) {
			var atts = props.attributes;
			var set = props.setAttributes;
			var blockProps = useBlockProps( { className: 'starter-bl-stats' } );
			var innerProps = useInnerBlocksProps(
				{ className: 'starter-bl-stats-grid' },
				{
					allowedBlocks: [ 'starter/stat-item' ],
					template: [
						[ 'starter/stat-item' ],
						[ 'starter/stat-item' ],
						[ 'starter/stat-item' ],
						[ 'starter/stat-item' ],
					],
				}
			);

			return el(
				'section',
				blockProps,
				el(
					'div',
					{ className: 'starter-bl-container' },
					el(
						'div',
						{ className: 'starter-bl-stats-header' },
						el(
							'div',
							null,
							el( RichText, {
								tagName: 'span',
								className: 'starter-bl-kicker starter-bl-kicker--claro',
								value: atts.badge,
								allowedFormats: [],
								placeholder: __( 'Badge (ex.: Números)', 'started-theme' ),
								onChange: function ( v ) {
									set( { badge: v } );
								},
							} )
						),
						el( RichText, {
							tagName: 'h2',
							className: 'starter-bl-stats-titulo',
							value: atts.titulo,
							allowedFormats: [],
							placeholder: __( 'Título da faixa de números', 'started-theme' ),
							onChange: function ( v ) {
								set( { titulo: v } );
							},
						} )
					),
					el( 'div', innerProps )
				)
			);
		},
		save: function () {
			return el( InnerBlocks.Content );
		},
	} );
} )( window.wp );
