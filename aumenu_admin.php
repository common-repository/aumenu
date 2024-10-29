<?php
/**
* Name: AuMenu
* URI: https://www.aumenu.info
* Description: Display your taps and beers from AuMenu on your website!
* Version: 1.1
* Author: Hyper
* Author URI: http://www.atelierhyper.com
* License: AuMenu 2016
*/

	global $wpdb, $table_prefix;
?>

<?php
	$public_key = get_option('aumenu_public_key');
	$secret_key = get_option('aumenu_secret_key');
	$slug = get_option('aumenu_slug');
	
	$aumenu = new AuMenuSDK();
	$aumenu->setLanguage(aumenu_get_language());
	$aumenu->getToken($public_key, $secret_key);
	$user = json_decode($aumenu->getUser());
?>
<div class="wrap">
    <h2>AuMenu - <?= __('Settings', 'wp_aumenu') ?></h2>
	<br/>
	<?php if (isset($user) && !isset($user->error)) { ?>
    <div>
	    <label><?= __('Firstname', 'wp_aumenu') ?>:</label> <?= $user->data->firstname ?><br/>
	    <label><?= __('Lastname', 'wp_aumenu') ?>:</label> <?= $user->data->lastname ?><br/>
	    <label><?= __('Email', 'wp_aumenu') ?>:</label> <?= $user->data->email ?>
    </div><br/>
    <?php } ?>
    <form method="post">
	    <label><?= __('Public Key', 'wp_aumenu') ?>:</label> <input type="text" name="public_key" value="<?= $public_key ?>" /><br/>
	    <label><?= __('Secret Key', 'wp_aumenu') ?>:</label> <input type="text" name="secret_key" value="<?= $secret_key ?>" />
	    <br/><br/>
	    <label><?= __('Slug', 'wp_aumenu') ?>:</label> <input type="text" name="slug" value="<?= $slug ?>" />
		<?= submit_button(); ?>
    </form>
</div>