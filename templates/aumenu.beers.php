<?php
/**
* Plugin Name: AuMenu
* Plugin URI: https://www.aumenu.info
* Description: Display your taps and beers from AuMenu on your website!
* Version: 1.1
* Author: Hyper
* Author URI: http://www.atelierhyper.com
* License: AuMenu 2016
*/
?>
<?php
		while ( have_posts() ) : the_post();
?>
		<h1 class="aumenu_page_title"><?= the_title() ?></h1>
		<div class="aumenu_page_thumbnail"><?php the_post_thumbnail() ?></div>
		<div class="aumenu_page_content"><?= the_content() ?></div>	
<?php
			$public_key = get_option('aumenu_public_key');
			$secret_key = get_option('aumenu_secret_key');
			
			$post = get_post();
			$pages_settings_beers = get_post_meta($post->ID, '_establishments_id', true);
			$pages_settings_show_image = get_post_meta($post->ID, '_establishments_show_image', true);
			
			$aumenu = new AuMenuSDK();
			$aumenu->setLanguage(aumenu_get_language());
			$aumenu->getToken($public_key, $secret_key);
			
			if ($pages_settings_beers) {
				$establishment = json_decode($aumenu->getEstablishment($pages_settings_beers));
				if ($establishment && isset($establishment->data)) {
					$beers = json_decode($aumenu->getBeers($pages_settings_beers));
					if ($beers && isset($beers->data)) {
						foreach($beers->data as $key => $section) {
							if ($section->data) {
								echo '<div class="aumenu_beers_section" id="aumenu_beers_section'.($key+1).'">';
								if (strlen($section->name) > 0) {
									echo '<h2>'.$section->name.'</h2>';
								}
								echo '<div class="aumenu_beers_content">';
								foreach($section->data as $beer) {
									$type = $beer->type;
	?>
									<div class="aumenu_beers_row">
									<?php if ($pages_settings_show_image) { ?>
										<div class="aumenu_beers_image"><a href="<?= get_permalink(); ?><?= $beer->name_clean ?>/"><img src="<?= $beer->images && strlen($beer->images) > 0 ? $beer->images : plugins_url('../images/beer_default.svg', __FILE__ ) ?>" /></a></div>
									<?php } ?>
										<div class="aumenu_beers_name"><a href="<?= get_permalink(); ?><?= $beer->name_clean ?>/"><?= $beer->name ?></a></div>
										<div class="aumenu_beers_type"><?= $type ?></div>
										<div class="aumenu_beers_abv"><?= $beer->abv ?> %</div>
										<div class="aumenu_beers_desc"><?= $beer->description ?></div>
									</div>
	<?php
								}
								echo '</div>';
								echo '</div>';
							}
						}
					}
				}
			}
		endwhile;
?>