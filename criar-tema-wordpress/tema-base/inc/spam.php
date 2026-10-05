<?php
/**
 * Proteção anti-spam básica para o formulário Contact Form 7.
 *
 * Implementa dois controles sem depender de plugin ou CAPTCHA externo:
 *
 * 1. Verificação de referer — rejeita envios cujo HTTP_REFERER não pertença
 *    ao domínio do site, bloqueando POSTs diretos de scripts externos.
 *
 * 2. Rate limit por IP — máximo 5 envios por hora por endereço IP,
 *    usando Transients API. Protege contra ataques de envio em massa.
 *
 * Para proteção mais robusta em produção, complementar com Cloudflare
 * Turnstile (plugin Simple Cloudflare Turnstile + [turnstile] no form).
 *
 * @package Started_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WPCF7_Submission' ) ) {
	return; // CF7 não está ativo.
}

define( 'STARTER_CF7_MAX_ENVIOS', 5 );
define( 'STARTER_CF7_JANELA_SEG', HOUR_IN_SECONDS );

/**
 * Retorna o IP do solicitante sem confiar em cabeçalhos de proxy.
 *
 * @return string IP sanitizado.
 */
function starter_spam_obter_ip() {
	return preg_replace( '/[^0-9a-fA-F:.]/', '', (string) ( $_SERVER['REMOTE_ADDR'] ?? '' ) );
}

/**
 * Chave de transient para rate limit de envio de formulário.
 *
 * @param string $ip IP do solicitante.
 * @return string
 */
function starter_spam_chave( $ip ) {
	return 'starter_cf7_' . md5( $ip );
}

/**
 * Verifica spam antes de processar o envio do CF7.
 *
 * @param bool $spam true quando já identificado como spam.
 * @return bool
 */
function starter_cf7_verificar_spam( $spam ) {
	if ( $spam ) {
		return true;
	}

	// 1. Referer deve pertencer ao domínio do site.
	$referer = wp_get_referer();
	if ( ! $referer ) {
		return true;
	}

	$host_site    = wp_parse_url( home_url(), PHP_URL_HOST );
	$host_referer = wp_parse_url( $referer, PHP_URL_HOST );

	if ( $host_site !== $host_referer ) {
		return true;
	}

	// 2. Rate limit: máximo STARTER_CF7_MAX_ENVIOS por hora por IP.
	$ip     = starter_spam_obter_ip();
	$chave  = starter_spam_chave( $ip );
	$envios = (int) get_transient( $chave );

	if ( $envios >= STARTER_CF7_MAX_ENVIOS ) {
		return true;
	}

	return false;
}
add_filter( 'wpcf7_spam', 'starter_cf7_verificar_spam' );

/**
 * Incrementa o contador de envios por IP após envio válido.
 *
 * @return void
 */
function starter_cf7_registrar_envio() {
	$ip    = starter_spam_obter_ip();
	$chave = starter_spam_chave( $ip );
	set_transient( $chave, (int) get_transient( $chave ) + 1, STARTER_CF7_JANELA_SEG );
}
add_action( 'wpcf7_mail_sent', 'starter_cf7_registrar_envio' );
