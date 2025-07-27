<?php
/**
 * Plugin Name: BD Domain Checker Pro
 * Description: Advanced BD (.bd & .বাংলা) Domain Checker with individual pricing, enable/disable TLD, Buy Now control.
 * Version: 3.0
 * Author: DOT.COM.BD
 */

if (!defined('ABSPATH')) exit;

// ✅ Default settings on activation
register_activation_hook(__FILE__, function(){
    $defaults = [
        '.com.bd' => ['enabled'=>1,'price'=>800],
        '.net.bd' => ['enabled'=>1,'price'=>850],
        '.org.bd' => ['enabled'=>1,'price'=>750],
        '.edu.bd' => ['enabled'=>0,'price'=>1000],
        '.gov.bd' => ['enabled'=>0,'price'=>1200],
        '.info.bd'=> ['enabled'=>1,'price'=>700],
        '.বাংলা'  => ['enabled'=>1,'price'=>950]
    ];
    if(!get_option('bdc_tlds')) update_option('bdc_tlds',$defaults);
    if(!get_option('bdc_buy_text')) update_option('bdc_buy_text','Buy Now');
    if(!get_option('bdc_buy_url')) update_option('bdc_buy_url','#');
});

// ✅ Include files
require_once plugin_dir_path(__FILE__).'includes/ajax-handler.php';
require_once plugin_dir_path(__FILE__).'includes/settings-page.php';

// ✅ Shortcode
add_shortcode('bd_domain_checker', function(){
    ob_start();
    include plugin_dir_path(__FILE__).'templates/form-layout.php';
    return ob_get_clean();
});

// ✅ Load CSS+JS
add_action('wp_enqueue_scripts', function(){
    wp_enqueue_style('bdc-style', plugin_dir_url(__FILE__).'assets/css/style.css');
    wp_enqueue_script('bdc-js', plugin_dir_url(__FILE__).'assets/js/script.js',['jquery'],false,true);
    wp_localize_script('bdc-js','bdAjax',[
        'ajaxurl'=>admin_url('admin-ajax.php'),
        'nonce'=>wp_create_nonce('bdc_nonce')
    ]);
});
