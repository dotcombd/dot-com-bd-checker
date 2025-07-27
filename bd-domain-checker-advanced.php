<?php
/**
 * Plugin Name: BD Domain Checker Advanced
 * Description: Advanced BD (.bd & .বাংলা) Domain Availability Checker with Dashboard Control
 * Version: 2.0
 * Author: DOT.COM.BD
 */

if (!defined('ABSPATH')) exit;

// ✅ Define constants
define('BDC_ADV_PATH', plugin_dir_path(__FILE__));
define('BDC_ADV_URL', plugin_dir_url(__FILE__));

// ✅ Autoload required files
require_once BDC_ADV_PATH . 'includes/settings-handler.php';
require_once BDC_ADV_PATH . 'includes/ajax-handler.php';

// ✅ Activation hook → create logs table + default settings
register_activation_hook(__FILE__, 'bdc_adv_activate');
function bdc_adv_activate(){
    global $wpdb;
    $table_name = $wpdb->prefix . 'bd_checker_logs';
    $charset = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id BIGINT AUTO_INCREMENT PRIMARY KEY,
        domain_name VARCHAR(255) NOT NULL,
        ip_address VARCHAR(50),
        checked_at DATETIME DEFAULT CURRENT_TIMESTAMP
    ) $charset;";
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);

    // ✅ Default settings
    $default_ext = [
        ['ext'=>'.com.bd','price'=>800,'enabled'=>true],
        ['ext'=>'.net.bd','price'=>700,'enabled'=>true],
        ['ext'=>'.org.bd','price'=>700,'enabled'=>true],
        ['ext'=>'.বাংলা','price'=>1000,'enabled'=>true]
    ];
    add_option('bdc_adv_extensions', $default_ext);
    add_option('bdc_adv_welcome', 'Welcome to BD Domain Checker!');
    add_option('bdc_adv_available_msg', '✅ {domain} is available for {price} BDT/year');
    add_option('bdc_adv_taken_msg', '❌ {domain} is already registered');
}

// ✅ Admin Menu
add_action('admin_menu', function(){
    add_menu_page(
        'BD Domain Checker',
        'BD Domain Checker',
        'manage_options',
        'bdc-advanced',
        'bdc_adv_admin_page',
        'dashicons-search',
        30
    );
});

// ✅ Admin Page UI
function bdc_adv_admin_page(){
    require_once BDC_ADV_PATH . 'admin/settings-page.php';
}

// ✅ Load frontend assets
add_action('wp_enqueue_scripts', function(){
    wp_enqueue_style('bdc-adv-style', BDC_ADV_URL.'assets/css/style.css');
    wp_enqueue_script('bdc-adv-js', BDC_ADV_URL.'assets/js/checker.js', ['jquery'], false, true);

    wp_localize_script('bdc-adv-js', 'bdcAjax', [
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('bdc_adv_nonce')
    ]);
});

// ✅ Frontend Shortcode
add_shortcode('bd_domain_checker_advanced', function(){
    ob_start();
    include BDC_ADV_PATH . 'templates/form-layout.php';
    return ob_get_clean();
});
