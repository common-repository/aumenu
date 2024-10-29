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
			$public_key = get_option('aumenu_public_key');
			$secret_key = get_option('aumenu_secret_key');
				
			$aumenu = new AuMenuSDK();
			$aumenu->setLanguage(aumenu_get_language());
			$aumenu->getToken($public_key, $secret_key);
			
			$post = get_post();
			$page_type = get_post_meta($post->ID, '_establishments_type', true);
			$pages_settings_beers = get_post_meta($post->ID, '_establishments_id', true);
			$establishment_name_clean = "";
			if ($page_type == "beers") {
				$establishment = json_decode($aumenu->getEstablishment($pages_settings_beers));
				$establishment_name_clean = $establishment->data->name_clean;
			} else if ($page_type = "taps" || $page_type = "bottles") {
				$establishment = json_decode($aumenu->getEstablishment($pages_settings_beers));
				$establishment_name_clean = $establishment->data->establishments_id;
			}
			
			
			$v_establishment = isset($wp_query->query_vars['establishment']) ? $wp_query->query_vars['establishment'] : (isset($_GET['establishment']) ? $_GET['establishment'] : (strlen($establishment_name_clean) > 0 ? $establishment_name_clean : false));
			$v_beer = isset($wp_query->query_vars['beer']) ? $wp_query->query_vars['beer'] : (isset($_GET['beer']) ? $_GET['beer'] : false);
			
			
			$beer = json_decode($aumenu->getBeer($v_beer, $v_establishment));
			
			if ($beer) {
?>
			<h1 class="aumenu_page_beer_name"><?= $beer->data->name ?></h1>
<?php if (strlen($beer->data->type) > 0) { ?>
			<div class="aumenu_page_beer_type"><?= $beer->data->type ?></div>
<?php } ?>

<?php if (strlen($beer->data->images) > 0) { ?>
			<div class="aumenu_page_beer_image"><img src="<?= $beer->data->images ?>"/></div>
<?php } ?>
			
<?php if (strlen($beer->data->abv) > 0 || strlen($beer->data->ibu) > 0 || strlen($beer->data->srm) > 0) { ?>
			<ul class="aumenu_page_beer_metric">
<?php if (strlen($beer->data->abv) > 0) { ?>
				<li class="aumenu_page_beer_apv"><label><?= __('abv', 'wp_aumenu') ?></label><?= $beer->data->abv ?> %</li>
<?php } ?>
<?php if (strlen($beer->data->ibu) > 0) { ?>
				<li class="aumenu_page_beer_ibu"><label><?= __('ibu', 'wp_aumenu') ?></label><?= $beer->data->ibu ?></li>
<?php } ?>
<?php if (strlen($beer->data->srm) > 0) { ?>
				<li class="aumenu_page_beer_srm"><label><?= __('srm', 'wp_aumenu') ?></label><?= $beer->data->srm ?></li>
<?php } ?>
			</ul>
<?php } ?>

<?php if (strlen($beer->data->description) > 0) { ?>
			<div class="aumenu_page_beer_desc"><label><?= __('description', 'wp_aumenu') ?></label><?= $beer->data->description ?></div>
<?php } ?>
<?php if (strlen($beer->data->ingredients) > 0) { ?>
			<div class="aumenu_page_beer_ingredients"><label><?= __('ingredients', 'wp_aumenu') ?></label><?= $beer->data->ingredients ?></div>
<?php } ?>
			<div class="aumenu_page_beer_extra">
<?php if (strlen($beer->data->glassware_id) > 0) { ?>
				<div class="aumenu_page_beer_glassware" style="background-image: url(<?= $beer->data->glassware->images ?>?c=ffcc33);" title="<?= $beer->data->glassware->name ?>"></div>
<?php } ?>

<?php
	if ($beer->data->bottles && count($beer->data->bottles) > 0) {
		foreach($beer->data->bottles as $bottle) {
?>
				<div class="aumenu_page_beer_bottle" style="background-image: url(<?= $bottle->images ?>?c=ffcc33);" title="<?= $bottle->name ?>"></div>
<?php
		}
	}
?>

<?php if (strlen($beer->data->special_edition) > 0) { ?>
				<div class="aumenu_page_beer_special_edition"><span><?= __('special edition', 'wp_aumenu') ?></span></div>
<?php } ?>	
			</div>
		
			<a href="<?= get_permalink(); ?>" class="aumenu_page_goback"><?= _e('back to beers page', 'wp_aumenu') ?></a>
<?php
			}
		endwhile;
?>