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
			$pages_settings_bottles = get_post_meta($post->ID, '_establishments_id', true);
			$pages_settings_show_image = get_post_meta($post->ID, '_establishments_show_image', true);
			
			$aumenu = new AuMenuSDK();
			$aumenu->setLanguage(aumenu_get_language());
			$aumenu->getToken($public_key, $secret_key);
			
			if ($pages_settings_bottles) {
				$establishment = json_decode($aumenu->getEstablishment($pages_settings_bottles));
				if ($establishment && isset($establishment->data)) {
					$bottles = json_decode($aumenu->getBottles($pages_settings_bottles));
					if ($bottles && isset($bottles->data)) {
						foreach($bottles->data as $key => $section) {
							if ($section->data) {
								echo '<div class="aumenu_bottles_section" id="aumenu_bottles_section'.($key+1).'">';
								if (strlen($section->name) > 0) {
									echo '<h2>'.$section->name.'</h2>';
								}
								echo '<table class="aumenu_bottles_content">';
								foreach($section->data as $bottle) {
									if (isset($bottle->beer)) {
										$permalink_post = get_permalink($post->ID).$bottle->beer->name_clean.'/'.$bottle->beer->establishment_name_clean.'/';
										if ($bottle->beer->establishment_name_clean == $establishment->data->establishments_id) {
											$permalink_post = get_permalink($post->ID).$bottle->beer->name_clean.'/';
										}
?>
										<tr class="aumenu_bottles_row">
										<?php if ($pages_settings_show_image) { ?>
											<td class="aumenu_bottles_image">
												<a href="<?= get_permalink(); ?><?= $bottle->beer->name_clean ?>/"><img src="<?= $bottle->beer->images && strlen($bottle->beer->images) > 0 ? $bottle->beer->images : plugins_url('../images/beer_default.svg', __FILE__ ) ?>" /></a>
											</td>
										<?php } ?>
											<td class="aumenu_bottles_name">
												<a href="<?= $permalink_post ?>"><?= $bottle->beer->name ?></a>
												<span><?= $bottle->beer->full_name ?> - <?= $bottle->beer->type ?></span>
											</td>
											<td class="aumenu_bottles_abv"><?= $bottle->beer->abv ?> %<?= $bottle->price_sold > 0 ? '<span>'.$bottle->price_sold.' $</span>' : '' ?></td>
										</tr>
<?php
									} else {
?>
										<tr class="aumenu_bottles_row aumenu_bottles_custom">
											<td colspan="<?= $pages_settings_show_image ? 3 : 2 ?>" class="aumenu_bottles_text"><?= $bottle->text ?></td>
										</tr>
<?php
									}
								}
								echo '</table>';
								echo '</div>';
							}
						}
					}
				}
			}
		endwhile;
?>