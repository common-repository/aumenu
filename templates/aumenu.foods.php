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
			$pages_settings = get_post_meta($post->ID, '_establishments_id', true);
			
			
			
			
			//$pages_settings_beers = get_option('aumenu_pages_settings_beers');
				
			$aumenu = new AuMenuSDK();
			$aumenu->setLanguage(aumenu_get_language());
			$aumenu->getToken($public_key, $secret_key);
			
			$foods = json_decode($aumenu->getFoods($pages_settings));
			if ($foods && isset($foods->data)) {
				foreach($foods->data as $key => $food_cat) {
					echo '<div class="aumenu_page_foods_section" id="aumenu_page_foods_section'.($key+1).'">';
					if (strlen($food_cat->name) > 0) {
						echo '<h2>'.$food_cat->name.'</h2>';
					}
					echo '<table class="aumenu_page_foods">';
?>
					<tbody>
<?php
					foreach($food_cat->data as $food) {
?>
						<tr class="aumenu_page_foods_row">
							<td class="aumenu_page_foods_name"<?= $food->price == 0 ? ' colspan="2"' : '' ?>><?= $food->name ?><span class="aumenu_page_foods_desc"><?= $food->description ?></span></td>
<?php
						if ($food->price > 0) {
?>
							<td class="aumenu_page_foods_price"><?= $food->price > 0 ? $food->price.' $' : '' ?></td>
<?php
						}
?>
						</tr>
<?php
					}
?>
					</tbody>
<?php
					echo '</table>';
				}
			}
		endwhile;
?>