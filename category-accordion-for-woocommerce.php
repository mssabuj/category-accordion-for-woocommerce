<?php
/*
Plugin Name: Category Accordion for WooCommerce
Plugin URI: https://www.themepoints.com/woo-accordion
Description: WooCommerce Category Accordions plugin allows you to list WooCommerce product categories and subcategories into an accordion with expand/collapse option.
Version: 1.0.0
Author: Themepoints
Author URI: https://themepoints.com
License: GPL v2 or later
Text Domain: category-accordion-for-woocommerce
*/

// Prevent direct access to this file
if ( ! defined( 'ABSPATH' ) ) {
    die( "Can't load this file directly" );
}

// Enqueue frontend scripts and styles
function tpcafw_enqueue_scripts() {
    // Register and enqueue the style
    wp_register_style('tp-woo-category-accordion', plugin_dir_url(__FILE__) . 'css/tp-woo-category-accordion.css', array(), '1.0.0', 'all');
    wp_enqueue_style('tp-woo-category-accordion');
    
    // Register and enqueue front-end script
    wp_register_script('tp-woo-category-accordion', plugin_dir_url(__FILE__) . 'js/tp-woo-category-accordion.js', array('jquery', 'jquery-ui-accordion'), '1.0.0', true);
    wp_enqueue_script('tp-woo-category-accordion');
}
add_action('wp_enqueue_scripts', 'tpcafw_enqueue_scripts');

// Enqueue admin scripts and styles
function tpcafw_enqueue_admin_scripts($hook) {
    // Only load the script on the Widgets admin page
    if ( 'widgets.php' != $hook ) {
        return;
    }

    // Enqueue color picker and custom admin JS
    wp_enqueue_script('wp-color-picker');
    wp_enqueue_script('tp-category-accordion-admin', plugin_dir_url(__FILE__) . 'js/category-accordion-widget.js', array('jquery', 'wp-color-picker'), '1.0.0', true);
}
add_action('admin_enqueue_scripts', 'tpcafw_enqueue_admin_scripts');

// WooCommerce activation check
function tpcafw_activation_notice() {
    if ( !is_plugin_active('woocommerce/woocommerce.php') ) {
        printf(
            '<div class="error"><p>%s</p></div>',
            esc_html__('Error: The Category Accordion for WooCommerce plugin requires WooCommerce to be activated. Please activate WooCommerce to use this plugin.', 'category-accordion-for-woocommerce')
        );
    }
}
add_action('admin_notices', 'tpcafw_activation_notice');

// Include additional files
require_once plugin_dir_path(__FILE__) . 'inc/category-accordion-for-woocommerce-widgets.php';
