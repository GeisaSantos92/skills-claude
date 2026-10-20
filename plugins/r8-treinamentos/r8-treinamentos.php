<?php
/**
 * Plugin Name:       R8 Treinamentos
 * Description:       Central de Treinamentos no painel: vídeos tutoriais para o cliente aprender a gerenciar o site, com widget no Dashboard e tela de cadastro dos módulos.
 * Version:           1.1.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Agência R8
 * Author URI:        https://agenciar8.com.br/
 * License:           GPL-2.0-or-later
 * Text Domain:       r8-treinamentos
 *
 * @package R8_Treinamentos
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'R8T_VERSION', '1.1.0' );
define( 'R8T_FILE', __FILE__ );
define( 'R8T_DIR', plugin_dir_path( __FILE__ ) );
define( 'R8T_URL', plugin_dir_url( __FILE__ ) );

require_once R8T_DIR . 'includes/data.php';
require_once R8T_DIR . 'includes/access.php';
require_once R8T_DIR . 'includes/video.php';
require_once R8T_DIR . 'includes/admin-pages.php';
require_once R8T_DIR . 'includes/dashboard-widget.php';

register_activation_hook( __FILE__, 'r8t_install' );
