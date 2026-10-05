<?php
/**
 * Meta tags de SEO, Open Graph, Twitter Card e Schema.org (JSON-LD).
 *
 * Política: com o Rank Math ativo, **ele** é a fonte de verdade do
 * <head> — o tema não compete. Sem o plugin, tudo aqui volta a valer, gerado
 * com dados que o tema já tem (ACF, Customizer, wp_get_document_title).
 *
 * Sem essa guarda o front sai com duas `description`, dois blocos Open Graph /
 * Twitter e duas Organization em JSON-LD.
 *
 * @package Started_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Há um plugin de SEO cuidando do <head>?
 *
 * A checagem é por constante/classe do plugin, não
 * por caminho de arquivo, para continuar valendo se ele for movido ou
 * renomeado.
 *
 * @return bool
 */
function starter_plugin_seo_ativo() {
	return defined( 'RANK_MATH_VERSION' ) || class_exists( 'RankMath' );
}

/**
 * Gera a meta description da página atual.
 *
 * Prioridade:
 *  - Home:     campo ACF `hero_subtitulo`
 *  - Singular: excerpt → 30 primeiras palavras do conteúdo
 *  - Fallback: tagline do site (Configurações → Tagline)
 *
 * @return string Texto sem HTML, máximo 160 caracteres.
 */
function starter_meta_description() {
	$desc = '';

	if ( is_front_page() && function_exists( 'starter_campo' ) ) {
		$desc = (string) ( starter_campo( 'hero_subtitulo' ) ?? '' );
	} elseif ( is_singular() ) {
		global $post;
		$desc = $post->post_excerpt
			? $post->post_excerpt
			: wp_trim_words( wp_strip_all_tags( $post->post_content ?? '' ), 30, '' );
	}

	if ( ! $desc ) {
		$desc = get_bloginfo( 'description' );
	}

	return mb_substr( wp_strip_all_tags( $desc ), 0, 160 );
}

/**
 * Retorna a URL absoluta da imagem Open Graph.
 *
 * Prioridade:
 *  - Imagem destacada do post/página (singular)
 *  - Logo claro salvo no Customizer
 *
 * @return string URL da imagem, ou string vazia.
 */
function starter_og_image_url() {
	if ( is_singular() && has_post_thumbnail() ) {
		$src = wp_get_attachment_image_url( (int) get_post_thumbnail_id(), 'large' );
		if ( $src ) {
			return $src;
		}
	}

	$logo_id = absint( get_theme_mod( 'starter_logo_claro' ) ?? 0 );
	if ( $logo_id ) {
		$src = wp_get_attachment_image_url( $logo_id, 'large' );
		if ( $src ) {
			return $src;
		}
	}

	return '';
}

/**
 * Imprime meta description, Open Graph e Twitter Card no <head>.
 *
 * Hookado em wp_head com prioridade 5 (antes dos scripts do tema). Só imprime
 * quando não há plugin de SEO ativo — é o fallback do tema.
 *
 * @return void
 */
function starter_print_seo_meta() {
	if ( starter_plugin_seo_ativo() ) {
		return;
	}

	$desc     = starter_meta_description();
	$og_image = starter_og_image_url();
	$og_url   = is_singular() ? (string) get_permalink() : home_url( '/' );
	$artigos  = array_keys( array_intersect( starter_schema_tipos_cpt(), array( 'Article' ) ) );
	$og_type  = is_singular( array_merge( array( 'post' ), $artigos ) ) ? 'article' : 'website';
	$titulo   = wp_get_document_title();

	// Locale do idioma atual (Polylang ou fallback pt_BR).
	$locale = function_exists( 'pll_current_language' )
		? (string) pll_current_language( 'locale' )
		: get_locale();

	if ( $desc ) {
		printf( '<meta name="description" content="%s">' . "\n", esc_attr( $desc ) );
	}

	// Open Graph.
	printf( '<meta property="og:type" content="%s">' . "\n", esc_attr( $og_type ) );
	printf( '<meta property="og:title" content="%s">' . "\n", esc_attr( $titulo ) );
	printf( '<meta property="og:url" content="%s">' . "\n", esc_url( $og_url ) );
	printf( '<meta property="og:site_name" content="%s">' . "\n", esc_attr( get_bloginfo( 'name' ) ) );
	printf( '<meta property="og:locale" content="%s">' . "\n", esc_attr( $locale ) );

	if ( $desc ) {
		printf( '<meta property="og:description" content="%s">' . "\n", esc_attr( $desc ) );
	}

	if ( $og_image ) {
		printf( '<meta property="og:image" content="%s">' . "\n", esc_url( $og_image ) );
		// Dimensões mínimas recomendadas para WhatsApp/Facebook/LinkedIn.
		printf( '<meta property="og:image:width" content="1200">' . "\n" );
		printf( '<meta property="og:image:height" content="630">' . "\n" );
	}

	// og:locale:alternate — versões traduzidas disponíveis (Polylang).
	if ( function_exists( 'pll_the_languages' ) ) {
		$linguas = pll_the_languages( array( 'raw' => 1 ) );
		foreach ( $linguas as $lingua ) {
			if ( ! empty( $lingua['current_lang'] ) ) {
				continue;
			}
			if ( ! empty( $lingua['locale'] ) ) {
				printf( '<meta property="og:locale:alternate" content="%s">' . "\n", esc_attr( $lingua['locale'] ) );
			}
		}
	}

	// Canonical — aponta para a URL canônica da página atual.
	printf( '<link rel="canonical" href="%s">' . "\n", esc_url( $og_url ) );

	// Twitter / X Card.
	$twitter_card = $og_image ? 'summary_large_image' : 'summary';
	printf( '<meta name="twitter:card" content="%s">' . "\n", esc_attr( $twitter_card ) );
	printf( '<meta name="twitter:title" content="%s">' . "\n", esc_attr( $titulo ) );

	if ( $desc ) {
		printf( '<meta name="twitter:description" content="%s">' . "\n", esc_attr( $desc ) );
	}

	if ( $og_image ) {
		printf( '<meta name="twitter:image" content="%s">' . "\n", esc_url( $og_image ) );
	}
}
add_action( 'wp_head', 'starter_print_seo_meta', 5 );

/**
 * Imprime JSON-LD de Organization na home.
 *
 * Inclui redes sociais registradas no Customizer (sameAs), o logo e a URL
 * canônica — base para rich snippets de marca. Como a meta acima, só sai
 * quando não há plugin de SEO cuidando do grafo.
 *
 * @return void
 */
function starter_print_schema_org() {
	if ( starter_plugin_seo_ativo() || ! is_front_page() ) {
		return;
	}

	$logo_id  = absint( get_theme_mod( 'starter_logo_claro' ) ?? 0 );
	$logo_url = $logo_id ? (string) wp_get_attachment_image_url( $logo_id, 'large' ) : '';

	$schema = array(
		'@context' => 'https://schema.org',
		'@type'    => 'Organization',
		'name'     => get_bloginfo( 'name' ),
		'url'      => home_url( '/' ),
	);

	if ( $logo_url ) {
		$schema['logo'] = $logo_url;
	}

	// Redes sociais do Customizer → sameAs.
	if ( function_exists( 'starter_social_networks' ) ) {
		$same_as = array();
		foreach ( array_keys( starter_social_networks() ) as $slug ) {
			$url = (string) ( get_theme_mod( 'starter_social_' . $slug ) ?? '' );
			if ( $url ) {
				$same_as[] = $url;
			}
		}
		if ( $same_as ) {
			$schema['sameAs'] = $same_as;
		}
	}

	printf(
		'<script type="application/ld+json">%s</script>' . "\n",
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE )
	);
}
add_action( 'wp_head', 'starter_print_schema_org', 6 );

/**
 * Tipo Schema.org de cada CPT público (slug do post type => @type).
 *
 * Fonte única para o JSON-LD dos singles. Ex.: 'starter_case' => 'Article',
 * 'starter_servico' => 'Service'. Post type fora da lista não gera JSON-LD.
 *
 * @return array<string,string>
 */
function starter_schema_tipos_cpt() {
	return array(
		// 'starter_exemplo' => 'Article',
	);
}

/**
 * Imprime JSON-LD para singles dos CPTs públicos (starter_schema_tipos_cpt()).
 *
 * Só age quando não há plugin de SEO ativo. Usa dados disponíveis no post
 * sem depender de campos ACF específicos para não quebrar se os campos mudarem.
 *
 * - Article → publisher Organization
 * - demais (Service, Product…) → provider Organization
 *
 * @return void
 */
function starter_print_schema_cpt() {
	if ( starter_plugin_seo_ativo() ) {
		return;
	}

	$tipos = starter_schema_tipos_cpt();

	if ( ! $tipos || ! is_singular( array_keys( $tipos ) ) ) {
		return;
	}

	$tipo      = $tipos[ get_post_type() ];
	$descricao = '';

	global $post;
	if ( $post->post_excerpt ) {
		$descricao = wp_strip_all_tags( $post->post_excerpt );
	} elseif ( $post->post_content ) {
		$descricao = wp_trim_words( wp_strip_all_tags( $post->post_content ), 30, '' );
	}

	$organizacao = array(
		'@type' => 'Organization',
		'name'  => get_bloginfo( 'name' ),
		'url'   => home_url( '/' ),
	);

	$schema = array(
		'@context' => 'https://schema.org',
		'@type'    => $tipo,
		( 'Article' === $tipo ? 'headline' : 'name' ) => wp_strip_all_tags( get_the_title() ),
		'url'      => (string) get_permalink(),
		( 'Article' === $tipo ? 'publisher' : 'provider' ) => $organizacao,
	);

	if ( $descricao ) {
		$schema['description'] = $descricao;
	}

	if ( has_post_thumbnail() ) {
		$imagem_url = wp_get_attachment_image_url( (int) get_post_thumbnail_id(), 'large' );
		if ( $imagem_url ) {
			$schema['image'] = $imagem_url;
		}
	}

	printf(
		'<script type="application/ld+json">%s</script>' . "\n",
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE )
	);
}
add_action( 'wp_head', 'starter_print_schema_cpt', 6 );

/**
 * Fallback de alt text para campos ACF de imagem sem alt preenchido.
 *
 * Quando o cliente faz upload sem preencher o campo "Texto alternativo" na
 * biblioteca de mídia, wp_get_attachment_image() retorna alt="". Este filtro
 * preenche o alt com o título do anexo (derivado do nome do arquivo) como
 * último recurso — apenas quando nenhum alt foi armazenado no banco.
 *
 * Não interfere com imagens explicitamente decorativas (alt="" passado pelo
 * tema nos atributos), pois nesses casos o alt já está no array $attr.
 *
 * @param array      $attr       Atributos da tag <img>.
 * @param WP_Post    $attachment Post do anexo.
 * @return array Atributos com alt preenchido se estava vazio.
 */
function starter_alt_fallback( $attr, $attachment ) {
	// alt="" passado explicitamente pelo tema = decorativa. Respeita.
	if ( array_key_exists( 'alt', $attr ) ) {
		return $attr;
	}

	// alt do banco também vazio → usa o título do anexo como fallback.
	if ( empty( $attr['alt'] ) ) {
		$titulo = trim( get_the_title( $attachment->ID ) );
		if ( $titulo ) {
			$attr['alt'] = $titulo;
		}
	}

	return $attr;
}
add_filter( 'wp_get_attachment_image_attributes', 'starter_alt_fallback', 20, 2 );


/**
 * Imprime JSON-LD BreadcrumbList para posts do blog e singles de CPT.
 *
 * Só age quando não há plugin de SEO ativo. Gera a trilha correspondente
 * ao breadcrumb visual exibido pelo tema:
 * - Blog (singular):          Início › Blog › Título
 * - CPT com archive (single): Início › {Nome do archive} › Título
 *
 * @return void
 */
function starter_print_schema_breadcrumb() {
	if ( starter_plugin_seo_ativo() || ! is_singular() || is_page() || is_attachment() ) {
		return;
	}

	$itens = array(
		array(
			'@type'    => 'ListItem',
			'position' => 1,
			'name'     => get_bloginfo( 'name' ),
			'item'     => home_url( '/' ),
		),
	);

	$post_type = get_post_type();

	if ( 'post' === $post_type ) {
		$blog_page_id = (int) get_option( 'page_for_posts' );
		$meio_nome    = __( 'Blog', 'started-theme' );
		$meio_url     = $blog_page_id ? get_permalink( $blog_page_id ) : home_url( '/blog/' );
	} else {
		$meio_nome = post_type_archive_title( '', false );
		$meio_url  = get_post_type_archive_link( $post_type );
	}

	if ( $meio_url && $meio_nome ) {
		$itens[] = array(
			'@type'    => 'ListItem',
			'position' => 2,
			'name'     => $meio_nome,
			'item'     => $meio_url,
		);
	}

	$itens[] = array(
		'@type'    => 'ListItem',
		'position' => count( $itens ) + 1,
		'name'     => wp_strip_all_tags( get_the_title() ),
		'item'     => (string) get_permalink(),
	);

	$schema = array(
		'@context'        => 'https://schema.org',
		'@type'           => 'BreadcrumbList',
		'itemListElement' => $itens,
	);

	printf(
		'<script type="application/ld+json">%s</script>' . "\n",
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE )
	);
}
add_action( 'wp_head', 'starter_print_schema_breadcrumb', 6 );

/* ==========================================================================
   Sitemap nativo do WordPress — ajustes de qualidade de indexação
   Só age quando o Rank Math não está ativo (ele substitui o sitemap inteiro).
   ========================================================================== */

/**
 * Remove post types de conteúdo raso do sitemap nativo.
 *
 * - attachment: página de anexo não tem conteúdo próprio; indexá-la dilui a
 *   autoridade do domínio e raramente gera tráfego qualificado.
 *
 * @param WP_Post_Type[] $post_types Mapa de post types candidatos ao sitemap.
 * @return WP_Post_Type[]
 */
function starter_sitemap_filtrar_post_types( $post_types ) {
	if ( starter_plugin_seo_ativo() ) {
		return $post_types;
	}

	unset( $post_types['attachment'] );

	return $post_types;
}
add_filter( 'wp_sitemaps_post_types', 'starter_sitemap_filtrar_post_types' );

/**
 * Remove taxonomias sem conteúdo semântico do sitemap nativo.
 *
 * - post_format: raramente usado; quando vazio, a URL retorna 404 ou lista vazia.
 *
 * @param WP_Taxonomy[] $taxonomies Mapa de taxonomias candidatas ao sitemap.
 * @return WP_Taxonomy[]
 */
function starter_sitemap_filtrar_taxonomias( $taxonomies ) {
	if ( starter_plugin_seo_ativo() ) {
		return $taxonomies;
	}

	unset( $taxonomies['post_format'] );

	return $taxonomies;
}
add_filter( 'wp_sitemaps_taxonomies', 'starter_sitemap_filtrar_taxonomias' );


/**
 * Redireciona, com 301, URLs de conteúdo aposentado para o sucessor canônico.
 *
 * Não é um sistema de redirects — é um mapa curto e explícito, para os poucos
 * casos em que uma URL já publicada deixou de existir e tem um destino óbvio.
 * Se a lista crescer, o lugar disso passa a ser um plugin de redirects.
 *
 * @return void
 */
function starter_redirecionar_urls_aposentadas() {
	if ( is_admin() ) {
		return;
	}

	// Caminho antigo => caminho novo (com barra final). Ex.: '/servico/x/' => '/servicos/x/'.
	$mapa = array();

	if ( ! $mapa ) {
		return;
	}

	$caminho = trailingslashit( wp_parse_url( $_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH ) ?? '' );

	if ( isset( $mapa[ $caminho ] ) ) {
		wp_safe_redirect( home_url( $mapa[ $caminho ] ), 301 );
		exit;
	}
}
add_action( 'template_redirect', 'starter_redirecionar_urls_aposentadas' );
