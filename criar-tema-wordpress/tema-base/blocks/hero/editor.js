/**
 * starter/hero — editor (JS puro, sem build; ver inc/blocks.php).
 *
 * Edição inline via RichText; imagem de fundo via MediaUpload no painel
 * lateral. As classes visuais são as mesmas do render.php (blocks.css).
 */
( function ( wp ) {
	'use strict';

	var el = wp.element.createElement;
	var Fragment = wp.element.Fragment;
	var __ = wp.i18n.__;
	var useBlockProps = wp.blockEditor.useBlockProps;
	var RichText = wp.blockEditor.RichText;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var MediaUpload = wp.blockEditor.MediaUpload;
	var MediaUploadCheck = wp.blockEditor.MediaUploadCheck;
	var PanelBody = wp.components.PanelBody;
	var Button = wp.components.Button;

	wp.blocks.registerBlockType( 'starter/hero', {
		edit: function ( props ) {
			var atts = props.attributes;
			var set = props.setAttributes;
			var blockProps = useBlockProps( {
				className: 'starter-bl-hero starter-bl-hero--editor',
			} );

			return el(
				Fragment,
				null,
				el(
					InspectorControls,
					null,
					el(
						PanelBody,
						{ title: __( 'Imagem de fundo', 'started-theme' ), initialOpen: true },
						el(
							MediaUploadCheck,
							null,
							el( MediaUpload, {
								onSelect: function ( media ) {
									set( { imagemId: media.id, imagemUrl: media.url } );
								},
								allowedTypes: [ 'image' ],
								value: atts.imagemId,
								render: function ( ctl ) {
									return el(
										Button,
										{ variant: 'secondary', onClick: ctl.open },
										atts.imagemId
											? __( 'Trocar imagem', 'started-theme' )
											: __( 'Escolher imagem', 'started-theme' )
									);
								},
							} )
						),
						atts.imagemId
							? el(
									Button,
									{
										variant: 'link',
										isDestructive: true,
										onClick: function () {
											set( { imagemId: 0, imagemUrl: '' } );
										},
									},
									__( 'Remover imagem', 'started-theme' )
							  )
							: null
					)
				),
				el(
					'section',
					blockProps,
					el( 'div', {
						className: 'starter-bl-hero-bg',
						style: atts.imagemUrl
							? { backgroundImage: 'url(' + atts.imagemUrl + ')' }
							: null,
						'aria-hidden': 'true',
					} ),
					el( 'div', { className: 'starter-bl-hero-overlay', 'aria-hidden': 'true' } ),
					el(
						'div',
						{ className: 'starter-bl-container starter-bl-hero-conteudo' },
						el(
							'div',
							null,
							el( RichText, {
								tagName: 'span',
								className: 'starter-bl-kicker starter-bl-kicker--claro',
								value: atts.badge,
								allowedFormats: [],
								placeholder: __( 'Badge (ex.: Campanha)', 'started-theme' ),
								onChange: function ( v ) {
									set( { badge: v } );
								},
							} )
						),
						el( RichText, {
							tagName: 'h1',
							className: 'starter-bl-hero-titulo',
							value: atts.titulo,
							allowedFormats: [],
							placeholder: __( 'Título da landing', 'started-theme' ),
							onChange: function ( v ) {
								set( { titulo: v } );
							},
						} ),
						el( RichText, {
							tagName: 'p',
							className: 'starter-bl-hero-subtitulo',
							value: atts.subtitulo,
							allowedFormats: [],
							placeholder: __( 'Subtítulo (opcional)', 'started-theme' ),
							onChange: function ( v ) {
								set( { subtitulo: v } );
							},
						} )
					)
				)
			);
		},
		save: function () {
			return null;
		},
	} );
} )( window.wp );
