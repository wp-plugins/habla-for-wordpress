<?php
/*
Plugin Name: Hab.la for WordPress
Plugin URI: http://www.jamesdimick.com/creations/habla-for-wordpress/
Description: A plugin that allows website authors to easily place a <a href="http://www.hab.la/">Hab.la</a> live help widget on their WordPress website.
Version: 1.0
Author: James Dimick
Author URI: http://www.jamesdimick.com/

=== VERSION HISTORY ===
  04.28.09 - v1.0 - The first version

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

$plugurldir = get_option('siteurl').'/'.PLUGINDIR.'/habla-for-wordpress/';
$hfw_domain = 'HablaForWordpress';
load_plugin_textdomain($hfw_domain, 'wp-content/plugins/habla-for-wordpress');
add_action('init', 'hfw_init');
add_action('wp_footer', 'hfw_insert');
add_action('admin_notices', 'hfw_admin_notice');
add_filter('plugin_action_links', 'hfw_plugin_actions', 10, 2);

function hfw_init() {
	if(function_exists('current_user_can') && current_user_can('manage_options')) add_action('admin_menu', 'hfw_add_settings_page');
}

function hfw_insert() {
	global $current_user;
	if(get_option('hfwID')) {
		get_currentuserinfo();
		echo("\n\n<!-- Start Hab.la Code <http://www.hab.la/> -->\n<script type=\"text/javascript\" src=\"http://static.hab.la/js/wc.js\"></script>\n<script type=\"text/javascript\">\n\t");
		if(isset($current_user) && $current_user->ID !== 0) {
			echo("config = new wc_config();\n\tconfig.vars['force_nickname'] = '".$current_user->display_name."';\n\twc_init('".get_option('hfwID')."', config);");
		} else {
			echo("wc_init('".get_option('hfwID')."');");
		}
		echo("\n</script>\n<!-- End Hab.la Code <http://www.hab.la/> -->\n\n");
	}
}

function hfw_admin_notice() {
	if(!get_option('hfwID')) echo('<div class="error"><p><strong>'.sprintf(__('Hab.la for WordPress is disabled. Please go to the <a href="%s">plugin page</a> and enter a valid account ID to enable it.' ), admin_url('options-general.php?page=habla-for-wordpress')).'</strong></p></div>');
}

function hfw_plugin_actions($links, $file) {
	static $this_plugin;
	if(!$this_plugin) $this_plugin = plugin_basename(__FILE__);
	if($file == $this_plugin && function_exists('admin_url')) {
		$settings_link = '<a href="'.admin_url('options-general.php?page=habla-for-wordpress').'">'.__('Settings', $hfw_domain).'</a>';
		array_unshift($links, $settings_link);
	}
	return($links);
}

function hfw_add_settings_page() {
	function hfw_settings_page() {
		global $hfw_domain, $plugurldir; ?>
		<div class="wrap">
			<?php screen_icon() ?>
			<h2><?php _e('Hab.la for WordPress', $hfw_domain) ?></h2>
			<div class="metabox-holder meta-box-sortables ui-sortable pointer">
				<div class="postbox" style="float:left;width:30em;margin-right:20px">
					<h3 class="hndle"><span><?php _e('Hab.la Account ID', $hfw_domain) ?></span></h3>
					<div class="inside" style="padding: 0 10px">
						<form method="post" action="options.php">
							<p><?php wp_nonce_field('update-options') ?><a href="http://www.hab.la/" title="<?php _e('Chat with your website&rsquo;s visitors using your favorite IM client', $hfw_domain) ?>"><img src="<?php echo($plugurldir) ?>habla_logo.gif" height="120" width="100%" alt="<?php _e('Hab.la Logo', $hfw_domain) ?>" /></a></p>
							<p><label for="hfwID"><?php printf(__('Enter your %1$sChat with your website&rsquo;s visitors using your favorite IM client%2$sHab.la%3$s account ID below to activate the plugin.', $hfw_domain), '<strong><a href="http://www.hab.la/" title="', '">', '</a></strong>') ?></label><br /><input type="text" name="hfwID" id="hfwID" value="<?php echo(get_option('hfwID')) ?>" style="width:100%" /></p>
							<p class="submit" style="padding:0"><input type="hidden" name="action" value="update" /><input type="hidden" name="page_options" value="hfwID" /><input type="submit" name="hfwSubmit" id="hfwSubmit" value="<?php _e('Save My ID', $hfw_domain) ?>" class="button-primary" /> <small class="nonessential"><?php _e('Entering an incorrect ID will result in an error!', $hfw_domain) ?></small></p>
							<p style="font-size:smaller;color:#999239;background-color:#ffffe0;padding:0.4em 0.6em !important;border:1px solid #e6db55;-moz-border-radius:3px;-khtml-border-radius:3px;-webkit-border-radius:3px;border-radius:3px"><?php printf(__('Don&rsquo;t have an account? No problem! %1$sRegister for a free Hab.la account today!%2$sRegister for a <strong>FREE</strong> Hab.la account right now!%3$s Start chatting with your site visitors today!', $hfw_domain), '<a href="http://www.hab.la/habla/signup" title="', '">', '</a>') ?></p>
						</form>
					</div>
				</div>
				<div class="postbox" style="float:left;width:30em">
					<h3 class="hndle"><span><?php _e('More Information', $hfw_domain) ?></span></h3>
					<div class="inside" style="padding: 0 10px">
						<p><?php printf(__('Go to %1$sChat with your website&rsquo;s visitors using your favorite IM client%2$sthe official Hab.la website%3$s and login to your account to %4$scustomize your chat widget%5$s. There you can customize several aspects of your widget including the colors, the various text messages that appear, and even the position of the widget on the screen. You can also find extensive documentation on how to customize the widget further.', $hfw_domain), '<a href="http://www.hab.la/" title="', '">', '</a>', '<a href="http://www.hab.la/customize">', '</a>') ?></p>
						<p><?php printf(__('Also, if you would like to get more extensive support, chat with others about Hab.la, or even to help support Hab.la, please visit %1$sChat with your website&rsquo;s visitors using your favorite IM client%2$sthe official Hab.la website%3$s.', $hfw_domain), '<a href="http://www.hab.la/" title="', '">', '</a>') ?></p>
					</div>
				</div>
			</div>
		</div>
	<?php }
	add_submenu_page('options-general.php', __('Hab.la for WordPress', $hfw_domain), __('Hab.la for WordPress', $hfw_domain), 'manage_options', 'habla-for-wordpress', 'hfw_settings_page');
}
?>