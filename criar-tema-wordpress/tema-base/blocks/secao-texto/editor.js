/**
 * starter/secao-texto — editor (JS puro, sem build; ver inc/blocks.php).
 */
( function ( wp ) {
	'use strict';

	var el = wp.element.createElement;
	var useBlockProps = wp.blockEditor.useBlockProps;
	var useInnerBlocksProps = wp.blockEditor.useInnerBlocksProps;
	var InnerBlocks = wp.blockEditor.InnerBlocks;

	wp.blocks.registerBlockType( 'starter/secao-texto', {
		edit: function () {
			var blockProps = useBlockProps( { className: 'starter-bl-texto' } );
			var innerProps = useInnerBlocksProps(
				{ className: 'entry__content' },
				{ template: [ [ 'core/paragraph' ] ] }
			);

			return el(
				'section',
				blockProps,
				el(
					'div',
					{ className: 'starter-bl-container' },
					el( 'div', innerProps )
				)
			);
		},
		save: function () {
			return el( InnerBlocks.Content );
		},
	} );
} )( window.wp );
