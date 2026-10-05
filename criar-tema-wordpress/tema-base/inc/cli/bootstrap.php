<?php
/**
 * Bootstrap dos comandos WP-CLI do tema.
 *
 * Carregado por functions.php apenas quando WP_CLI está definido — inerte em
 * requisições web. Registre aqui os comandos custom do projeto (ex.: seeders
 * que convertem um template estático em conteúdo do WordPress).
 *
 * Padrão de organização:
 * - Uma classe por comando/etapa: inc/cli/class-starter-seed-*.php
 * - Registro: WP_CLI::add_command( 'starter_seed', 'Starter_Seed_Setup' );
 *
 * @package Started_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	return;
}

/*
 * Exemplo:
 *
 * require_once __DIR__ . '/class-starter-seed-setup.php';
 * WP_CLI::add_command( 'starter_seed', 'Starter_Seed_Setup' );
 */
