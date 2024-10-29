<?php
/*
Plugin Name:  AuMenu
Plugin URI:   https://www.aumenu.info
Description:  Display your taps and beers from AuMenu on your website!
Version:      1.1.5
Author:       Hyper
Author URI:   http://www.atelierhyper.com
License:      AuMenu 2016
License URI:  http://www.atelierhyper.com
Text Domain:  wp_aumenu
Domain Path:  /lang
*/



require_once(dirname( __FILE__ ).'/classes/aumenu_sdk/1.1/aumenu.php');
 
define('AUMENU_PATH', plugin_dir_path(__FILE__));
 
add_action('admin_menu', 'aumenu_admin_menu');
function aumenu_admin_menu() {
	add_menu_page(
		'AuMenu', 
		'AuMenu', 
		'manage_options', 
		'aumenu-settings', 
		'aumenu_admin'
	);
}
 
function aumenu_admin() {
	if (isset($_POST['public_key']) && isset($_POST['secret_key'])) {
        update_option('aumenu_public_key', $_POST['public_key']);
        update_option('aumenu_secret_key', $_POST['secret_key']);
    }
    if (isset($_POST['slug'])) {
    	update_option('aumenu_slug', $_POST['slug']);
	}
	
	require_once(dirname( __FILE__ ).'/aumenu_admin.php');
}

function aumenu_get_language() {
	$curLang = substr(get_bloginfo( 'language' ), 0, 2);
	
	if ($curLang == 'fr') {
		$curLang = 'fr';
	} else {
		$curLang = 'en';
	}
	
	return $curLang;
}


function aumenu_css_js() {
	wp_enqueue_style('aumenu_establishments_style', plugins_url('/css/aumenu.css', __FILE__ ));
	wp_register_script('aumenu_establishments_script', plugins_url('/js/aumenu.js', __FILE__ ), array('jquery'));
	wp_enqueue_script('aumenu_establishments_script');
	wp_localize_script('aumenu_establishments_script', 'aumenu_ajax_object', array(
        'ajax_url' => admin_url('admin-ajax.php')
    ));
}
add_action('wp_enqueue_scripts', 'aumenu_css_js');
add_action('admin_enqueue_scripts', 'aumenu_css_js');



add_action('wp_ajax_aumenu_widget_sections', 'aumenu_widget_sections');
function aumenu_widget_sections() {
	$public_key = get_option('aumenu_public_key');
	$secret_key = get_option('aumenu_secret_key');
	
	$aumenu = new AuMenuSDK();
	$aumenu->setLanguage(aumenu_get_language());
	$aumenu->getToken($public_key, $secret_key);
	
/*
	$post = get_post($_POST['data']);
	print_r($post);
*/
	
	$v_establishment = get_post_meta($_POST['data'], '_establishments_id', true);
	//print_r($v_establishment);
	//$v_establishment = isset($wp_query->query_vars['establishment']) ? $wp_query->query_vars['establishment'] : (isset($_GET['establishment']) ? $_GET['establishment'] : false);
	
	//print_r($v_establishment);
	
	$page_type = get_post_meta($_POST['data'], '_establishments_type', true);
	if ($page_type == 'taps') {
		$sections = $aumenu->getTapsSections($v_establishment);
	} else if ($page_type == 'foods') {
		$sections = $aumenu->getFoodsSections($v_establishment);
	} else if ($page_type == 'beers') {
		$sections = $aumenu->getBeersSections($v_establishment);
	} else if ($page_type == 'bottles') {
		$sections = $aumenu->getBottlesSections($v_establishment);
	}
	
	print_r($sections);
	
	die();
}
require_once(dirname( __FILE__ ).'/widget/aumenu_widget.php');

/*
add_action('wp_ajax_nopriv_post_love_add_love', 'post_love_add_love' );
function post_love_add_love() {
	return 'banane';
}
*/



/**
 * Register a post type.
 */
function aumenu_custom_init() {
	//flush_rewrite_rules();
	
	$slug = get_option('aumenu_slug');
	
	load_textdomain('wp_aumenu', dirname( __FILE__ ).'/lang/aumenu-'.aumenu_get_language().'.mo');
	
	$labels = array(
		'name'               => _x( 'AuMenu', 'post type general name' ),
		'singular_name'      => _x( 'aumenu', 'post type singular name' ),
		'add_new'            => _x( 'Add New', 'aumenu' ),
		'add_new_item'       => __( 'Add New Page' ),
		'edit_item'          => __( 'Edit Page' ),
		'new_item'           => __( 'New Page' ),
		'all_items'          => __( 'All Page' ),
		'view_item'          => __( 'View Page' ),
		'search_items'       => __( 'Search Page' ),
		'not_found'          => __( 'No page found' ),
		'not_found_in_trash' => __( 'No page found in the Trash' ), 
		'parent_item_colon'  => '',
		'menu_name'          => 'AuMenu'
	);
	$args = array(
		'labels'        => $labels,
		'description'   => 'AuMenu page creator',
		'public'        => true,
		'rewrite'		=> true,
		'show_ui'		=> true,
		'_builtin'		=> false,
		'capability_type' => 'post',
		'supports'      => array('title', 'editor', 'thumbnail'),
		'has_archive'   => false,
		'rewrite' => false//array('slug' => $slug, 'with_front' => true),
	);
    register_post_type('aumenu', $args);

    add_rewrite_tag("%establishment%", '(.*?)', 'establishment=');
    add_rewrite_tag("%beer%", '(.*?)', 'beer=');
    
	add_rewrite_rule('^aumenu/(.*?)/(.*?)/(.*?)/?$', 'index.php?aumenu=$matches[1]&establishment=$matches[3]&beer=$matches[2]', 'top');
	add_rewrite_rule('^'.$slug.'/(.*?)/(.*?)/(.*?)/?$', 'index.php?aumenu=$matches[1]&establishment=$matches[3]&beer=$matches[2]', 'top');
	add_rewrite_rule('^aumenu/(.*?)/(.*?)/?$', 'index.php?aumenu=$matches[1]&beer=$matches[2]', 'top');
	add_rewrite_rule('^'.$slug.'/(.*?)/(.*?)/?$', 'index.php?aumenu=$matches[1]&beer=$matches[2]', 'top');
	add_rewrite_rule('^aumenu/(.*?)/?$', 'index.php?aumenu=$matches[1]', 'top');
	add_rewrite_rule('^'.$slug.'/(.*?)/?$', 'index.php?aumenu=$matches[1]', 'top');
    
	flush_rewrite_rules();
}
add_action('init', 'aumenu_custom_init');

function query_vars($query_vars ) {
    $query_vars[] = 'establishment';
    $query_vars[] = 'beer';
    return $query_vars;
}
add_filter('query_vars', 'query_vars');


function aumenu_post_link($post_link, $post_id, $leavename = false) {
	$post = get_post($post_id);
	$page_type = get_post_meta($post->ID, '_establishments_type', true);
	$slug = get_option('aumenu_slug') !== false && strlen(get_option('aumenu_slug')) > 0 ? get_option('aumenu_slug') : $post->post_type;
	if ($post->post_type == 'aumenu') {
		return home_url("/".$slug."/".$post->post_name."/");
	}
	return $post_link;
}
add_filter('post_link', 'aumenu_post_link', 10, 3);

function aumenu_post_type_link($post_link, $post_id, $leavename = false, $sample = false) {
	$post = get_post($post_id);
	//print_r($post);
	//$page_type = get_post_meta($post->ID, '_establishments_type', true);
	$slug = get_option('aumenu_slug') !== false && strlen(get_option('aumenu_slug')) > 0 ? get_option('aumenu_slug') : $post->post_type;
	if ($post->post_type == 'aumenu') {
		return home_url("/".$slug."/".$post->post_name."/");
	}
	return $post_link;
}
add_filter('post_type_link', /* array($this,  */'aumenu_post_type_link'/* ) */, 10, 3);



function add_page_beer_template($single_template) {
	global $wp_query, $post;
	
	$page_type = get_post_meta($post->ID, '_establishments_type', true);
	
	
	$file = explode('-', basename($single_template, ".php"));
	
	$path_file = dirname( __FILE__ ).'/templates/single.php';
	if (isset($file[1]) && $file[1] == 'aumenu') {
		$path_file = $single_template;
	}
	
	$v_establishment = isset($wp_query->query_vars['establishment']) ? $wp_query->query_vars['establishment'] : (isset($_GET['establishment']) ? $_GET['establishment'] : false);
	$v_beer = isset($wp_query->query_vars['beer']) ? $wp_query->query_vars['beer'] : (isset($_GET['beer']) ? $_GET['beer'] : false);
	
	if ($page_type == 'taps') {
		if ($wp_query->query['post_type'] == 'aumenu' && /* $v_establishment &&  */$v_beer) {
			return $path_file;
		} else if ($wp_query->query['post_type'] == 'aumenu') {
			return $path_file;
		}
	} else if ($page_type == 'foods') {
		return $path_file;
	} else if ($page_type == 'beers') {
		if ($wp_query->query['post_type'] == 'aumenu' && /* $v_establishment &&  */$v_beer) {
			return $path_file;
		} else if ($wp_query->query['post_type'] == 'aumenu') {
			return $path_file;
		}
	} else if ($page_type == 'bottles') {
		if ($wp_query->query['post_type'] == 'aumenu' && /* $v_establishment &&  */$v_beer) {
			return $path_file;
		} else if ($wp_query->query['post_type'] == 'aumenu') {
			return $path_file;
		}
	}
	
	return $single_template;
}

add_filter( 'template_include', 'add_page_beer_template');




function add_aumenu_metaboxes() {
	add_meta_box('aumenu_establishments_id', 'AuMenu', 'aumenu_establishments_id', 'aumenu', 'normal', 'high');
}
add_action( 'add_meta_boxes', 'add_aumenu_metaboxes' );

function aumenu_establishments_id() {
	global $post;
	
	$public_key = get_option('aumenu_public_key');
	$secret_key = get_option('aumenu_secret_key');
	
	$aumenu = new AuMenuSDK();
	$aumenu->setLanguage(aumenu_get_language());
	$aumenu->getToken($public_key, $secret_key);
	$user = json_decode($aumenu->getUser());
	
	
	
	echo '<input type="hidden" name="aumenu_nonce" id="aumenu_nonce" value="' . 
	wp_create_nonce( plugin_basename(__FILE__) ) . '" />';
	
	
	$establishments_type = get_post_meta($post->ID, '_establishments_type', true);
	$establishments_show_image = get_post_meta($post->ID, '_establishments_show_image', true);
	
	$types_array = null;
	if (isset($user) && isset($user->data) && isset($user->data->breweries)) {
		$types_array['beers'] = __('Beers', 'wp_aumenu');
		if (!$establishments_type) { $establishments_type = 'beers'; }
	}
	if (isset($user) && isset($user->data) && isset($user->data->establishments)) {
		$types_array['taps'] = __('Taps', 'wp_aumenu');
		$types_array['bottles'] = __('Bottles', 'wp_aumenu');
		$types_array['foods'] = __('Foods', 'wp_aumenu');
		if (!$establishments_type) { $establishments_type = 'taps'; }
	}
?>
	<p>
	<select name="_establishments_type">
	<?php
		if (isset($types_array)) {
			foreach($types_array as $key => $row) {
				echo '<option value="' . $key . '"', $establishments_type == $key ? ' selected="selected"' : '', '>', $row, '</option>';
			}
		}
	?>
	</select>
<?php
	
	
	$establishments_id = get_post_meta($post->ID, '_establishments_id', true);
	
	$datas = null;
	if ($establishments_type && $establishments_type == 'beers') {
		if (isset($user) && isset($user->data) && isset($user->data->breweries)) {
			$datas = $user->data->breweries;
		}
	} else if ($establishments_type && ($establishments_type == 'taps' || $establishments_type == 'foods' || $establishments_type == 'bottles')) {
		if (isset($user) && isset($user->data) && isset($user->data->establishments)) {
			$datas = $user->data->establishments;
		}
	} else {
		if (isset($user) && isset($user->data) && isset($user->data->breweries)) {
			$datas = $user->data->breweries;
		}
	}
	
	
?>
	<select name="_establishments_id">
	<?php
		if ($datas) {
			foreach($datas as $row) {
				echo '<option value="' . $row->id . '"', $establishments_id == $row->id ? ' selected="selected"' : '', '>', $row->name, '</option>';
			}
		}
	?>
	</select>
	</p>
	
	<p>
	<label for="_establishments_show_image"><?= __('Show images', 'wp_aumenu') ?>
		<input type="checkbox" name="_establishments_show_image" id="_establishments_show_image" <?= $establishments_show_image ? 'checked="checked"' : '' ?> />
	</label>
	</p>
<?php
}
function aumenu_admin_post_type_change() {
	$establishments_type = $_POST['obj'];
	
    $public_key = get_option('aumenu_public_key');
	$secret_key = get_option('aumenu_secret_key');
	
	$aumenu = new AuMenuSDK();
	$aumenu->setLanguage(aumenu_get_language());
	$aumenu->getToken($public_key, $secret_key);
	$user = json_decode($aumenu->getUser());
    
    $datas = null;
	if ($establishments_type && $establishments_type == 'beers') {
		if (isset($user) && isset($user->data) && isset($user->data->breweries)) {
			$datas = $user->data->breweries;
		}
	} else if ($establishments_type && ($establishments_type == 'taps' || $establishments_type == 'foods' || $establishments_type == 'bottles')) {
		if (isset($user) && isset($user->data) && isset($user->data->establishments)) {
			$datas = $user->data->establishments;
		}
	} else {
		if (isset($user) && isset($user->data) && isset($user->data->breweries)) {
			$datas = $user->data->breweries;
		}
	}
    
    echo json_encode($datas);
    die();
}
add_action( 'wp_ajax_change', 'aumenu_admin_post_type_change' );

function wpt_save_aumenu_meta($post_id, $post) {
	if ( !wp_verify_nonce( $_POST['aumenu_nonce'], plugin_basename(__FILE__) )) {
		return $post->ID;
	}

	if ( !current_user_can( 'edit_post', $post->ID ))
		return $post->ID;

	$events_meta['_establishments_id'] = $_POST['_establishments_id'];
	$events_meta['_establishments_type'] = $_POST['_establishments_type'];
	$events_meta['_establishments_show_image'] = $_POST['_establishments_show_image'];
	
	
	foreach ($events_meta as $key => $value) {
		if( $post->post_type == 'revision' ) return;
		$value = implode(',', (array)$value);
		if(get_post_meta($post->ID, $key, FALSE)) {
			update_post_meta($post->ID, $key, $value);
		} else {
			add_post_meta($post->ID, $key, $value);
		}
		if(!$value) delete_post_meta($post->ID, $key);
	}

}
add_action('save_post', 'wpt_save_aumenu_meta', 1, 2);


function aumenu_shortcode($atts = [], $content = null, $tag = '') {
	$output = "";
    
    $atts = array_change_key_case((array)$atts, CASE_LOWER);
    $a = shortcode_atts(array('id' => null), $atts);
	
	$public_key = get_option('aumenu_public_key');
	$secret_key = get_option('aumenu_secret_key');
	
	
	$widget_section = isset($instance['aumenu_establishments_sections']) && strlen($instance['aumenu_establishments_sections']) > 0 ? $instance['aumenu_establishments_sections'] : null;
	$pages_settings_show_image = isset($instance['aumenu_establishments_show_image']) && strlen($instance['aumenu_establishments_show_image']) > 0 ? $instance['aumenu_establishments_show_image'] : null;
	
	$pages_settings_type = null;
	$pages_settings = null;
	$post = get_post($a['id']);
	if ($post) {
		$pages_settings_id = get_post_meta($post->ID, '_establishments_id', true);
		$pages_settings_type = get_post_meta($post->ID, '_establishments_type', true);
	}
	
	if ($pages_settings_id) {
		$establishments_id = $pages_settings_id;
	} else {
	     $establishments_id = null;
	}
	
	ob_start();
	
	if ($establishments_id) {
		$aumenu = new AuMenuSDK();
		$aumenu->setLanguage(aumenu_get_language());
		$aumenu->getToken($public_key, $secret_key);
		
		if ($pages_settings_type == 'beers') {
			$establishment = json_decode($aumenu->getEstablishment($establishments_id));
			if ($establishment && isset($establishment->data)) {
				$beers = json_decode($aumenu->getBeers($establishments_id));
				if ($beers && isset($beers->data)) {
					foreach($beers->data as $key => $section) {
						if ($section->data && (!$widget_section || $widget_section == $section->id)) {
							echo '<div class="aumenu_beers_shortcode_section" id="aumenu_beers_shortcode_section'.($key+1).'">';
							if (strlen($section->name) > 0) {
								echo '<h2>'.$section->name.'</h2>';
							}
							echo '<table class="aumenu_beers_shortcode_content">';
							foreach($section->data as $beer) {
								$type = $beer->type ? $beer->type : '';
?>
								<tr class="aumenu_beers_row">
								<?php if ($pages_settings_show_image) { ?>
									<td class="aumenu_beers_image">
										<a href="<?= $permalink_post ?>">
											<img src="<?= $beer->images && strlen($beer->images) > 0 ? $beer->images : plugins_url('../images/beer_default.svg', __FILE__ ) ?>" />
										</a>
									</td>
								<?php } ?>
									<td class="aumenu_beers_name"><a href="<?= get_permalink($post->ID); ?><?= $beer->name_clean ?>/"><?= $beer->name ?></a><span><?= $type ?></span></td>
									<td class="aumenu_beers_abv"><?= $beer->abv ?> %</td>
								</tr>
<?php
							}
							echo '</table>';
							echo '</div>';
						}
					}
				}
			}
		} else if ($pages_settings_type == 'taps') {
			$establishment = json_decode($aumenu->getEstablishment($establishments_id));
			$taps = json_decode($aumenu->getTaps($establishments_id));
			if ($taps && isset($taps->data)) {
				foreach($taps->data as $section) {
					if ($section->data && (!$widget_section || $widget_section == $section->id)) {
						echo '<div class="aumenu_taps_shortcode_section" id="aumenu_taps_shortcode_section'.($key+1).'">';
						if (strlen($section->name) > 0) {
							echo '<h2>'.$section->name.'</h2>';
						}
						echo '<table class="aumenu_taps_shortcode_content">';
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
									<a href="<?= $permalink_post ?>">
										<img src="<?= $tap->beer->images && strlen($tap->beer->images) > 0 ? $tap->beer->images : plugins_url('../images/beer_default.svg', __FILE__ ) ?>" />
									</a>
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
		} else if ($pages_settings_type == 'bottles') {
			$establishment = json_decode($aumenu->getEstablishment($establishments_id));
			if ($establishment && isset($establishment->data)) {
				$bottles = json_decode($aumenu->getBottles($establishments_id));
				if ($bottles && isset($bottles->data)) {
					foreach($bottles->data as $key => $section) {
						if ($section->data && (!$widget_section || $widget_section == $section->id)) {
							echo '<div class="aumenu_bottles_shortcode_section" id="aumenu_bottles_shortcode_section'.($key+1).'">';
							if (strlen($section->name) > 0) {
								echo '<h2>'.$section->name.'</h2>';
							}
							echo '<table class="aumenu_bottles_shortcode_content">';
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
											<a href="<?= $permalink_post ?>">
												<img src="<?= $bottle->beer->images && strlen($bottle->beer->images) > 0 ? $bottle->beer->images : plugins_url('../images/beer_default.svg', __FILE__ ) ?>" />
											</a>
										</td>
									<?php } ?>
										<td class="aumenu_bottles_name"><a href="<?= $permalink_post ?>"><?= $bottle->beer->name ?></a><span><?= $bottle->beer->type ?></span></td>
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
		} else if ($pages_settings_type == 'foods') {
			$foods = json_decode($aumenu->getFoods($establishments_id));
			if ($foods && isset($foods->data)) {
				foreach($foods->data as $food_cat) {
					if (!$widget_section || $widget_section == $food_cat->id) {
						echo '<div class="aumenu_foods_shortcode_section" id="aumenu_foods_shortcode_section'.($key+1).'">';
						if (strlen($food_cat->name) > 0) {
							echo '<h2>'.$food_cat->name.'</h2>';
						}
						echo '<table class="aumenu_foods_shortcode_content">';
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
						echo '</div>';
					}
				}
			}
		}
	}
	
	$output = ob_get_contents();
	ob_end_clean();
    
    return $output;
}
add_shortcode('aumenu', 'aumenu_shortcode');


?>