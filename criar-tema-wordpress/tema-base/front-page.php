<?php
/**
 * Página inicial.
 *
 * Só orquestra: cada seção do layout vira um template-part em
 * template-parts/home/. Todo o conteúdo vem do ACF da própria página inicial
 * (inc/acf-fields-home.php) ou de CPTs — nada é fixo aqui.
 *
 * @package Started_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main id="primary" class="site-home">
	<?php
	// Uma entrada por seção, na ordem do layout. Cada uma some sozinha se vazia.
	$starter_secoes = array(
		'hero',
	);

	foreach ( $starter_secoes as $starter_secao ) {
		get_template_part( 'template-parts/home/' . $starter_secao );
	}
	?>
</main>

<?php
get_footer();
