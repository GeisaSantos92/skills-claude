<?php
/**
 * Blog — índice de postagens nativas do WordPress.
 *
 * Exibido quando Settings > Reading "Posts page" aponta para esta URL.
 * Usa a query principal (ajustada por starter_blog_query() em
 * inc/blog-filters.php) para paginar nativamente: na 1ª página, o post mais
 * recente vira destaque e os demais formam o grid; da 2ª página em diante,
 * só grid.
 *
 * @package Started_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$starter_post_destaque = null;
$starter_posts_grade   = array();

if ( have_posts() ) {
	while ( have_posts() ) {
		the_post();

		if ( ! is_paged() && ! $starter_post_destaque ) {
			$starter_post_destaque = get_post();
			continue;
		}

		$starter_posts_grade[] = get_post();
	}
}
?>

<main id="primary" class="site-blog">
	<?php
	if ( $starter_post_destaque ) {
		get_template_part( 'template-parts/blog/destaque', null, array( 'post' => $starter_post_destaque ) );
	}

	get_template_part( 'template-parts/blog/cards', null, array( 'posts' => $starter_posts_grade ) );

	get_template_part(
		'template-parts/pagination',
		null,
		array(
			'current_page' => max( 1, (int) get_query_var( 'paged' ) ),
			'total_pages'  => (int) $GLOBALS['wp_query']->max_num_pages,
		)
	);
	?>
</main>

<?php
get_footer();
