<?php

/**
 * Plugin Name: GoLearn SaasOptics
 * Version: 1.0.0
 * Description: Integration imellem woocommerce og SaasOptics
 * Author: GoLearn
 * Author URI: https://www.golearn.dk/
 * Requires at least: 5.0
 * Tested up to: 5.7
 *
 * Text Domain: gl_saasoptics
 * Domain Path: /lang/
 *
 * @package WordPress
 * @author Bo Gunnarson
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
	exit;
}

define('BOILERPLATE_FILE', __FILE__);
define('BOILERPLATE_VERSION', '1.0.0');

// Load language early
load_plugin_textdomain('gl_saasoptics', false, dirname(plugin_basename(__FILE__)) . '/lang/');

// Initialize Plugin
require_once(__DIR__ . '/includes/init.php');

// Add plugin action links
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'my_plugin_action_links');
function my_plugin_action_links($links)
{
	$button_style = 'background: slategrey;color: #fff;padding: 1px 5px 3px;border-radius: 3px;';

	$links[] = '<a href="' . esc_url(admin_url('admin.php?page=gl-saasoptics-settings')) . '">' . __('Settings', 'gl_saasoptics') . '</a>';

	return $links;
}

// Load classes
new BoilerplatePostTypes();
new BoilerplateTaxonomies();
new BoilerplateMetaBoxes();
new BoilerplateSettings();
new BoilerplateBlocks();
$saas_optics = new SaasOptics();
$saas_optics->init();