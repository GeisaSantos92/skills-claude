<?php
/**
 * Remove os dados do plugin ao excluí-lo pelo painel.
 *
 * @package R8_Treinamentos
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

delete_option( 'r8t_trainings' );
delete_option( 'r8t_settings' );

global $wpdb;
// Cache de oEmbed dos vídeos (r8t_oembed_*).
$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '\_transient\_r8t\_oembed\_%' OR option_name LIKE '\_transient\_timeout\_r8t\_oembed\_%'" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
