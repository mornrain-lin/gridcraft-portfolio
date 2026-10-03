/**
 * Gridcraft Portfolio Customizer 实时预览脚本。
 */
( function ( $ ) {
	'use strict';

	if ( ! window.wp || ! window.wp.customize ) {
		return;
	}

	var api = window.wp.customize;

	api( 'blogname', function ( value ) {
		value.bind( function ( to ) {
			$( '.gc-brand__title' ).text( to );
		} );
	} );
} )( window.jQuery );
