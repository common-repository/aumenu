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
			$pages_settings_show_image = get_post_meta($post->ID, '_establishments_show_image', true);
			
			$aumenu = new AuMenuSDK();
			$aumenu->setLanguage(aumenu_get_language());
			$aumenu->getToken($public_key, $secret_key);
		
			$establishment = json_decode($aumenu->getEstablishment($pages_settings));
			//print_r($establishment);
			$taps = json_decode($aumenu->getTaps($pages_settings));
			if ($taps && isset($taps->data)) {
				foreach($taps->data as $section) {
					if ($section->data) {
						echo '<div class="aumenu_taps_section" id="aumenu_taps_section'.($key+1).'">';
						if (strlen($section->name) > 0) {
							echo '<h2>'.$section->name.'</h2>';
						}
						echo '<table class="aumenu_page_taps">';
						foreach($section->data as $tap) {
							$line_no = $tap->is_cask ? 'CASK' : (!$tap->is_empty ? ($tap->line_no < 10 ? '0'.$tap->line_no : $tap->line_no).'<div class="line-options">'.($tap->is_seasonal ? '<div class="seasonal"></div>' : '').($tap->is_speciality ? '<div class="speciality"></div>' : '').'</div>' : ($tap->line_no < 10 ? '0'.$tap->line_no : $tap->line_no));
					
							if (isset($tap->beer)) {
								$beer_establishment = json_decode($aumenu->getEstablishment($tap->beer->establishment_id));
								
								$permalink_post = get_permalink($post->ID).$tap->beer->name_clean.'/'.$beer_establishment->data->name_clean.'/';
								if ($beer_establishment->data->name_clean == $establishment->data->establishments_id) {
									$permalink_post = get_permalink($post->ID).$tap->beer->name_clean.'/';
								}
?>
								<tr class="aumenu_page_taps_row">
									<td class="aumenu_page_taps_line_no"><?= $line_no ?></td>
								<?php if ($pages_settings_show_image) { ?>
									<td class="aumenu_page_taps_image">
										<a href="<?= get_permalink(); ?><?= $tap->beer->name_clean ?>/"><img src="<?= $tap->beer->images && strlen($tap->beer->images) > 0 ? $tap->beer->images : plugins_url('../images/beer_default.svg', __FILE__ ) ?>" /></a>
									</td>
								<?php } ?>
									<td class="aumenu_page_taps_name"><a href="<?= $permalink_post ?>"><?= $tap->beer->name ?></a><span><?= $tap->beer->full_name ?> - <?= $tap->beer->type ?></span></td>
									<td class="aumenu_page_taps_abv"><?= $tap->beer->abv ?> %</td>
								</tr>
<?php
							} else {
?>
								<tr class="aumenu_page_taps_custom">
									<td class="aumenu_page_taps_line_no"><?= $line_no ?></td>
									<td colspan="<?= $pages_settings_show_image ? 3 : 2 ?>" class="aumenu_page_taps_text"><?= $tap->text ?></td>
								</tr>
<?php
							}
						}
						echo '</table>';
						echo '</div>';
					}
				}
				echo '<div>'.__('Last updated', 'wp_aumenu').' : '.$taps->last_update.'</div>';
			}
		endwhile;
?>