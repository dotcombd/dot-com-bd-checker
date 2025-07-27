<?php
/**
 * Plugin Name: BD Domain Checker (Safe Version)
 * Description: Advanced BD Domain Checker with Admin Control Panel (Error Safe)
 * Version: 2.0
 * Author: DOT.COM.BD
 */

if ( ! defined('ABSPATH') ) exit; // Direct access blocked

/**
 * ✅ Safe Include Function
 */
function bdc_safe_include($file) {
    if ( file_exists($file) ) {
        include_once $file;
    } else {
        error_log("⚠️ BD Domain Checker: Missing file -> " . $file);
    }
}

/**
 * ✅ Load Admin Menu (Always Safe)
 */
add_action('admin_menu', function(){
    add_menu_page(
        'BD Domain Checker',
        'BD Domain Checker',
        'manage_options',
        'bd-domain-checker',
        'bdc_admin_dashboard_safe',
        'dashicons-admin-site-alt3',
        80
    );
});

/**
 * ✅ Admin Dashboard Callback
 */
function bdc_admin_dashboard_safe(){
    echo '<div class="wrap">';
    echo '<h1>✅ BD Domain Checker Loaded Successfully</h1>';
    echo '<p>If any module is missing, check <code>wp-content/debug.log</code></p>';
    echo '</div>';
}

/**
 * ✅ Try to Load Required Files (But Don’t Crash if Missing)
 */

// Settings Page
bdc_safe_include( plugin_dir_path(__FILE__) . 'includes/settings-page.php' );

// Ajax Handler
bdc_safe_include( plugin_dir_path(__FILE__) . 'includes/ajax-handler.php' );

// Shortcode Frontend
bdc_safe_include( plugin_dir_path(__FILE__) . 'templates/form-layout.php' );

// Assets Loader
add_action('wp_enqueue_scripts', function(){
    // CSS
    $css_file = plugin_dir_url(__FILE__) . 'assets/css/style.css';
    wp_enqueue_style('bdc-style', $css_file, [], '2.0');

    // JS
    $js_file = plugin_dir_url(__FILE__) . 'assets/js/script.js';
    wp_enqueue_script('bdc-js', $js_file, ['jquery'], '2.0', true);

    wp_localize_script('bdc-js', 'bdcAjax', [
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce'   => wp_create_nonce('bdc_nonce')
    ]);
});
