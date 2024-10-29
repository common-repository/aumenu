<?php get_header(); ?>

<div class="wrap">
	<div id="primary" class="content-area" style="width: auto; float: none;">
		<main id="main" class="site-main" role="main">
			
			<?php
				global $wp_query, $post;
		
				$page_type = get_post_meta($post->ID, '_establishments_type', true);
				
				$v_establishment = isset($wp_query->query_vars['establishment']) ? $wp_query->query_vars['establishment'] : (isset($_GET['establishment']) ? $_GET['establishment'] : false);
				$v_beer = isset($wp_query->query_vars['beer']) ? $wp_query->query_vars['beer'] : (isset($_GET['beer']) ? $_GET['beer'] : false);
				
				if ($page_type == 'taps') {
					if ($wp_query->query['post_type'] == 'aumenu' && /* $v_establishment &&  */$v_beer) {
						include_once(AUMENU_PATH .'templates/aumenu.beer.php');
					} else if ($wp_query->query['post_type'] == 'aumenu') {
						include_once(AUMENU_PATH .'templates/aumenu.taps.php');
					}
				} else if ($page_type == 'foods') {
					include_once(AUMENU_PATH .'templates/aumenu.foods.php');
				} else if ($page_type == 'beers') {
					if ($wp_query->query['post_type'] == 'aumenu' && /* $v_establishment &&  */$v_beer) {
						include_once(AUMENU_PATH .'templates/aumenu.beer.php');
					} else if ($wp_query->query['post_type'] == 'aumenu') {
						include_once(AUMENU_PATH .'templates/aumenu.beers.php');
					}
				} else if ($page_type == 'bottles') {
					if ($wp_query->query['post_type'] == 'aumenu' && /* $v_establishment &&  */$v_beer) {
						include_once(AUMENU_PATH .'templates/aumenu.beer.php');
					} else if ($wp_query->query['post_type'] == 'aumenu') {
						include_once(AUMENU_PATH .'templates/aumenu.bottles.php');
					}
				}
			?>
			
		</main><!-- #main -->
	</div><!-- #primary -->
</div><!-- .wrap -->

<?php get_footer();
