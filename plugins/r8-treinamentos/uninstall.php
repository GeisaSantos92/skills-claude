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
delete_option( 'r8t_version' );
delete_option( 'r8t_needs_owner' );
delete_post_meta_by_key( '_r8t_training' );

// Quem era Agência R8 volta a ser administrador antes de a função sumir.
foreach ( get_users( array( 'role' => 'agenciar8', 'fields' => 'ID' ) ) as $r8t_user_id ) {
	( new WP_User( $r8t_user_id ) )->set_role( 'administrator' );
}
remove_role( 'agenciar8' );

global $wpdb;
// Cache de oEmbed dos vídeos (r8t_oembed_*).
$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '\_transient\_r8t\_oembed\_%' OR option_name LIKE '\_transient\_timeout\_r8t\_oembed\_%'" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
