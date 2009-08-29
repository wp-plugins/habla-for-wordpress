<?php
/*
Plugin Name: Olark for WordPress
Plugin URI: http://www.jamesdimick.com/creations/olark-for-wordpress/
Description: A plugin that allows website authors to easily place a <a href="http://www.olark.com/">Olark</a> live help widget on their WordPress website.
Version: 2.0
Author: James Dimick
Author URI: http://www.jamesdimick.com/

=== VERSION HISTORY ===
  04.28.09 - v1.0 - The first version
  08.28.09 - v2.0 - Updated the plugin to reflect the brand change from Hab.la to Olark

=== LEGAL INFORMATION ===
  Copyright (C) 2009 James Dimick <mail@jamesdimick.com> - www.jamesdimick.com

  This program is free software: you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation, either version 3 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program. If not, see <http://www.gnu.org/licenses/>.
*/

$plugurldir = get_option('siteurl').'/'.PLUGINDIR.'/olark-for-wordpress/';
$ofw_domain = 'OlarkForWordpress';
load_plugin_textdomain($ofw_domain, 'wp-content/plugins/olark-for-wordpress');
add_action('init', 'ofw_init');
add_action('wp_footer', 'ofw_insert');
add_action('admin_notices', 'ofw_admin_notice');
add_filter('plugin_action_links', 'ofw_plugin_actions', 10, 2);

function ofw_init() {
	if(function_exists('current_user_can') && current_user_can('manage_options')) add_action('admin_menu', 'ofw_add_settings_page');
}

function ofw_insert() {
	global $current_user;
	if(get_option('ofwID')) {
		get_currentuserinfo();
		echo("\n\n<!-- Start Olark Code <http://www.olark.com/> -->\n<script type=\"text/javascript\" src=\"http://static.olark.com/js/wc.js\"></script>\n<script type=\"text/javascript\">\n\t");
		if(isset($current_user) && $current_user->ID !== 0) {
			echo("config = new wc_config();\n\tconfig.vars['force_nickname'] = '".$current_user->display_name."';\n\twc_init('".get_option('ofwID')."', config);");
		} else {
			echo("wc_init('".get_option('ofwID')."');");
		}
		echo("\n</script>\n<!-- End Olark Code <http://www.olark.com/> -->\n\n");
	}
}

function ofw_admin_notice() {
	if(!get_option('ofwID')) echo('<div class="error"><p><strong>'.sprintf(__('Olark for WordPress is disabled. Please go to the <a href="%s">plugin page</a> and enter a valid account ID to enable it.' ), admin_url('options-general.php?page=olark-for-wordpress')).'</strong></p></div>');
}

function ofw_plugin_actions($links, $file) {
	static $this_plugin;
	if(!$this_plugin) $this_plugin = plugin_basename(__FILE__);
	if($file == $this_plugin && function_exists('admin_url')) {
		$settings_link = '<a href="'.admin_url('options-general.php?page=olark-for-wordpress').'">'.__('Settings', $ofw_domain).'</a>';
		array_unshift($links, $settings_link);
	}
	return($links);
}

function ofw_add_settings_page() {
	function ofw_settings_page() {
		global $ofw_domain, $plugurldir; ?>
		<div class="wrap">
			<?php screen_icon() ?>
			<h2><?php _e('Olark for WordPress', $ofw_domain) ?></h2>
			<div class="metabox-holder meta-box-sortables ui-sortable pointer">
				<div class="postbox" style="float:left;width:30em;margin-right:20px">
					<h3 class="hndle"><span><?php _e('Olark Account ID', $ofw_domain) ?></span></h3>
					<div class="inside" style="padding: 0 10px">
						<form method="post" action="options.php">
							<p style="text-align:center"><?php wp_nonce_field('update-options') ?><a href="http://www.olark.com/" title="<?php _e('Chat with your website&rsquo;s visitors using your favorite IM client', $ofw_domain) ?>"><img src="<?php echo($plugurldir) ?>olark.png" height="132" width="244" alt="<?php _e('Olark Logo', $ofw_domain) ?>" /></a></p>
							<p><label for="ofwID"><?php printf(__('Enter your %1$sChat with your website&rsquo;s visitors using your favorite IM client%2$sOlark%3$s account ID below to activate the plugin.', $ofw_domain), '<strong><a href="http://www.olark.com/" title="', '">', '</a></strong>') ?></label><br /><input type="text" name="ofwID" id="ofwID" value="<?php echo(get_option('ofwID')) ?>" style="width:100%" /></p>
							<p class="submit" style="padding:0"><input type="hidden" name="action" value="update" /><input type="hidden" name="page_options" value="ofwID" /><input type="submit" name="ofwSubmit" id="ofwSubmit" value="<?php _e('Save My ID', $ofw_domain) ?>" class="button-primary" /> <small class="nonessential"><?php _e('Entering an incorrect ID will result in an error!', $ofw_domain) ?></small></p>
							<p style="font-size:smaller;color:#999239;background-color:#ffffe0;padding:0.4em 0.6em !important;border:1px solid #e6db55;-moz-border-radius:3px;-khtml-border-radius:3px;-webkit-border-radius:3px;border-radius:3px"><?php printf(__('Don&rsquo;t have an account? No problem! %1$sRegister for a free Olark account today!%2$sRegister for a <strong>FREE</strong> Olark account right now!%3$s Start chatting with your site visitors today!', $ofw_domain), '<a href="http://www.olark.com/portal/wizard" title="', '">', '</a>') ?></p>
						</form>
					</div>
				</div>
				<div class="postbox" style="float:left;width:30em">
					<h3 class="hndle"><span><?php _e('More Information', $ofw_domain) ?></span></h3>
					<div class="inside" style="padding: 0 10px">
						<p><?php printf(__('Go to %1$sChat with your website&rsquo;s visitors using your favorite IM client%2$sthe official Olark website%3$s and login to your account to %4$scustomize your chat widget%5$s. There you can customize several aspects of your widget including the colors, the various text messages that appear, and even the position of the widget on the screen. You can also find extensive documentation on how to customize the widget further.', $ofw_domain), '<a href="http://www.olark.com/" title="', '">', '</a>', '<a href="http://www.olark.com/customize">', '</a>') ?></p>
						<p><?php printf(__('Also, if you would like to get more extensive support, chat with others about Olark, or even to help support Olark, please visit %1$sChat with your website&rsquo;s visitors using your favorite IM client%2$sthe official Olark website%3$s.', $ofw_domain), '<a href="http://www.olark.com/" title="', '">', '</a>') ?></p>
					</div>
				</div>
			</div>
		</div>
	<?php }
	add_submenu_page('options-general.php', __('Olark for WordPress', $ofw_domain), __('Olark for WordPress', $ofw_domain), 'manage_options', 'olark-for-wordpress', 'ofw_settings_page');
}
?>