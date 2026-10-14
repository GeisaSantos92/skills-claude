/* R8 Treinamentos — tela Gerenciar: adicionar, remover, reordenar e escolher vídeo. */
( function ( $ ) {
	'use strict';

	var $rows = $( '#r8t-rows' );
	var template = document.getElementById( 'r8t-row-template' );
	var nextIndex = parseInt( $rows.data( 'next-index' ), 10 ) || 0;
	var l10n = window.r8tManage || {};

	$( '.r8t-color' ).wpColorPicker();

	$rows.sortable( {
		handle: '.r8t-row-handle',
		placeholder: 'r8t-row-placeholder',
		axis: 'y'
	} );

	$( '#r8t-add' ).on( 'click', function () {
		var html = template.innerHTML.replace( /__INDEX__/g, 'n' + nextIndex++ );
		var $row = $( html.trim() );
		$rows.append( $row );
		$row.find( 'input[type="text"]' ).first().trigger( 'focus' );
	} );

	$rows.on( 'click', '.r8t-remove', function () {
		if ( window.confirm( l10n.confirmRemove ) ) {
			$( this ).closest( '.r8t-row' ).remove();
		}
	} );

	$rows.on( 'click', '.r8t-move', function () {
		var $row = $( this ).closest( '.r8t-row' );
		if ( 'up' === $( this ).data( 'dir' ) ) {
			$row.prev( '.r8t-row' ).before( $row );
		} else {
			$row.next( '.r8t-row' ).after( $row );
		}
		$( this ).trigger( 'focus' );
	} );

	$rows.on( 'click', '.r8t-media', function ( event ) {
		event.preventDefault();
		var $input = $( this ).closest( '.r8t-video-input' ).find( '.r8t-video-url' );
		var frame = wp.media( {
			title: l10n.mediaTitle,
			button: { text: l10n.mediaButton },
			library: { type: 'video' },
			multiple: false
		} );

		frame.on( 'select', function () {
			$input.val( frame.state().get( 'selection' ).first().toJSON().url );
		} );

		frame.open();
	} );
}( jQuery ) );
