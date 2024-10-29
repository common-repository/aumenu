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
	
	class aumenu_widget extends WP_Widget {
		/**
		 * Sets up the widgets name etc
		 */
		public function __construct() {
			$widget_ops = array( 
				'classname' => 'wp_widget_aumenu',
				'description' => 'AuMenu',
			);
			parent::__construct( 'wp_widget_aumenu', 'AuMenu', $widget_ops );
		}
	
		/**
		 * Outputs the content of the widget
		 *
		 * @param array $args
		 * @param array $instance
		 */
		public function widget( $args, $instance ) {
			add_action('wp_enqueue_scripts', 'aumenu_css_js');
			
			$public_key = get_option('aumenu_public_key');
			$secret_key = get_option('aumenu_secret_key');
			
			
			$widget_section = isset($instance['aumenu_establishments_sections']) && strlen($instance['aumenu_establishments_sections']) > 0 ? $instance['aumenu_establishments_sections'] : null;
			$pages_settings_show_image = isset($instance['aumenu_establishments_show_image']) && strlen($instance['aumenu_establishments_show_image']) > 0 ? $instance['aumenu_establishments_show_image'] : null;
			
			$pages_settings_type = null;
			$pages_settings = null;
			$post = get_post($instance['aumenu_establishments_id']);
			if ($post) {
				$pages_settings_id = get_post_meta($post->ID, '_establishments_id', true);
				$pages_settings_type = get_post_meta($post->ID, '_establishments_type', true);
			}
			
			if ($pages_settings_id) {
				$establishments_id = $pages_settings_id;
			} else {
			     $establishments_id = null;
			}
			
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
									echo '<div class="aumenu_beers_widget_section" id="aumenu_beers_widget_section'.($key+1).'">';
									if (strlen($section->name) > 0) {
										echo '<h2>'.$section->name.'</h2>';
									}
									echo '<table class="aumenu_beers_widget_content">';
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
								echo '<div class="aumenu_taps_widget_section" id="aumenu_taps_widget_section'.($key+1).'">';
								if (strlen($section->name) > 0) {
									echo '<h2>'.$section->name.'</h2>';
								}
								echo '<table class="aumenu_taps_widget_content">';
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
									echo '<div class="aumenu_bottles_widget_section" id="aumenu_bottles_widget_section'.($key+1).'">';
									if (strlen($section->name) > 0) {
										echo '<h2>'.$section->name.'</h2>';
									}
									echo '<table class="aumenu_bottles_widget_content">';
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
								echo '<div class="aumenu_foods_widget_section" id="aumenu_foods_widget_section'.($key+1).'">';
								if (strlen($food_cat->name) > 0) {
									echo '<h2>'.$food_cat->name.'</h2>';
								}
								echo '<table class="aumenu_foods_widget_content">';
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
		}
	
		/**
		 * Outputs the options form on admin
		 *
		 * @param array $instance The widget options
		 */
		public function form($instance) {
			$type = 'products';
			$args=array(
			  'post_type' => 'aumenu',
			  'post_status' => 'publish',
			  'posts_per_page' => -1,
			  'caller_get_posts'=> 1
			);
			
			$my_query = null;
			$my_query = new WP_Query($args);

			$establishments_id = null;
			$aumenu_establishments_sections = null;
			if ($instance) {
				//print_r($instance);
			    $establishments_id = esc_attr($instance['aumenu_establishments_id']);
			    $aumenu_establishments_sections = esc_attr($instance['aumenu_establishments_sections']);
			    $establishments_show_image = esc_attr($instance['aumenu_establishments_show_image']);
			     
			    $public_key = get_option('aumenu_public_key');
				$secret_key = get_option('aumenu_secret_key');
				
				$aumenu = new AuMenuSDK();
				$aumenu->setLanguage(aumenu_get_language());
				$aumenu->getToken($public_key, $secret_key);
				
				$v_establishment = get_post_meta($establishments_id, '_establishments_id', true);
				$page_type = get_post_meta($establishments_id, '_establishments_type', true);
				if ($page_type == 'taps') {
					$sections = json_decode($aumenu->getTapsSections($v_establishment));
				} else if ($page_type == 'foods') {
					$sections = json_decode($aumenu->getFoodsSections($v_establishment));
				} else if ($page_type == 'beers') {
					$sections = json_decode($aumenu->getBeersSections($v_establishment));
				} else if ($page_type == 'bottles') {
					$sections = json_decode($aumenu->getBottlesSections($v_establishment));
				}
				
/*
				print_r($establishments_id);
				print_r('<br/>');
				print_r($page_type);
				print_r('<br/>');
				print_r($aumenu_establishments_sections);
				print_r('<br/>');
				print_r($v_establishment);
				print_r('<br/>');
				print_r($sections);
*/
			}
	?>
			<p>
			<label for="<?php echo $this->get_field_id('aumenu_establishments_id'); ?>">Page :
			<select name="<?php echo $this->get_field_name('aumenu_establishments_id'); ?>" id="aumenu_establishments_id_<?= $this->id ?>" class="aumenu_establishments_id widefat">
				<option></option>
			<?php
				if ($my_query->have_posts()) {
					while ($my_query->have_posts()) : $my_query->the_post(); $post = get_post();
			?>
						<option value="<?= $post->ID ?>" id="<?= $post->ID ?>"<?= $establishments_id == $post->ID ? ' selected="selected"' : '' ?>><?php the_title(); ?></option>
			<?php
					endwhile;
				}
				wp_reset_query();
			?>
			</select>
			</label>
			<br/>
			<label for="<?php echo $this->get_field_id('aumenu_establishments_sections'); ?>">Sections :
			<select name="<?php echo $this->get_field_name('aumenu_establishments_sections'); ?>" id="aumenu_establishments_sections_<?= $this->id ?>" class="aumenu_establishments_sections widefat">
				<option><?= __('All', 'wp_aumenu') ?></option>
			<?php
				if ($sections) {
					foreach($sections->data as $section) {
			?>
						<option value="<?= $section->id ?>" id="<?= $section->id ?>"<?= $aumenu_establishments_sections == $section->id ? ' selected="selected"' : '' ?>><?= $section->name ?></option>
			<?php
					}
				}
			?>
			</select>
			</label>
			<br/>
			<label for="<?php echo $this->get_field_name('aumenu_establishments_show_image'); ?>"><?= __('Show images', 'wp_aumenu') ?>
				<input type="checkbox" name="<?php echo $this->get_field_name('aumenu_establishments_show_image'); ?>" id="<?php echo $this->get_field_name('aumenu_establishments_show_image'); ?>" <?= $establishments_show_image ? 'checked="checked"' : '' ?> />
			</label>
			</p>
			
			<script type="text/javascript">
				jQuery(document).ready(function($) {
					$("select.aumenu_establishments_id").change(function() {
						var section_field = $(this).parent().parent().find('select.aumenu_establishments_sections');
						section_field.empty();
				        var obj = $(this).val();
				        $.post("<?php echo get_home_url(); ?>/wp-admin/admin-ajax.php"/* aumenu_ajax_widget.ajax_url */, {
					        'action' : "aumenu_widget_sections",
				            'data' : obj
				        }, function(data) {
					        if (data) {
						        var json_data = JSON.parse(data);
						        
						        var field = "";
						        field += "<option value=\"\"><?= __('All', 'wp_aumenu') ?></option>";
						        $.each(json_data.data, function(key, value) {
							        field += "<option value=\""+value.id+"\">"+value.name+"</option>";
						        });
						        
						        section_field.html(field);
							}
				        });
				    });
				});
			</script>
	<?php
		}
	
		/**
		 * Processing widget options on save
		 *
		 * @param array $new_instance The new options
		 * @param array $old_instance The previous options
		 */
		public function update($new_instance, $old_instance) {
		  	$instance = array();
		  	print_r($new_instance);
			$instance['aumenu_establishments_id'] = ( ! empty( $new_instance['aumenu_establishments_id'] ) ) ? strip_tags( $new_instance['aumenu_establishments_id'] ) : '';
		  	$instance['aumenu_establishments_sections'] = ( ! empty( $new_instance['aumenu_establishments_sections'] ) ) ? strip_tags( $new_instance['aumenu_establishments_sections'] ) : '';
		  	
		  	$instance['aumenu_establishments_show_image'] = ( ! empty( $new_instance['aumenu_establishments_show_image'] ) ) ? strip_tags( $new_instance['aumenu_establishments_show_image'] ) : '';
		  	
			return $instance;
		}
	}
	
	add_action('widgets_init', function() {
		register_widget('aumenu_widget');
	});
?>