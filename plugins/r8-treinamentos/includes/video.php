<?php
/**
 * Renderização do player.
 *
 * Aceita arquivo de vídeo (biblioteca de mídia ou URL direta) e links de
 * YouTube/Vimeo (via oEmbed do WordPress, com cache em transient).
 *
 * @package R8_Treinamentos
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * HTML do player para a URL informada, ou string vazia se não houver como exibir.
 *
 * @param string $url URL do vídeo.
 * @return string
 */
function r8t_video_html( $url ) {
	$url = trim( (string) $url );

	if ( '' === $url ) {
		return '';
	}

	$path     = (string) wp_parse_url( $url, PHP_URL_PATH );
	$filetype = wp_check_filetype( $path );

	if ( ! empty( $filetype['type'] ) && 0 === strpos( $filetype['type'], 'video/' ) ) {
		return sprintf(
			'<video controls preload="metadata"><source src="%1$s" type="%2$s">%3$s</video>',
			esc_url( $url ),
			esc_attr( $filetype['type'] ),
			esc_html__( 'Seu navegador não suporta a reprodução de vídeos em HTML5.', 'r8-treinamentos' )
		);
	}

	$cache_key = 'r8t_oembed_' . md5( $url );
	$embed     = get_transient( $cache_key );

	if ( false === $embed ) {
		$embed = (string) wp_oembed_get( $url );
		set_transient( $cache_key, $embed, $embed ? WEEK_IN_SECONDS : HOUR_IN_SECONDS );
	}

	// HTML do oEmbed vem de provedores da lista do próprio WordPress (mesmo tratamento do core).
	return $embed;
}
