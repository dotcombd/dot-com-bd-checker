<?php
/**
 * Plugin Name: BD Domain Checker
 * Description: Simple BD (.bd & .বাংলা) Domain Availability Checker with AJAX.
 * Version: 2.0
 * Author: DOT.COM.BD
 */

if (!defined('ABSPATH')) exit;

/**
 * ✅ AJAX Handler: ডোমেইন অ্যাভেইলেবল কিনা চেক করবে এবং JSON list রিটার্ন করবে
 */
function bd_domain_checker_ajax() {
    if (!isset($_POST['domain'])) {
        wp_send_json_error(['message' => '❌ No domain received']);
    }

    $domain = sanitize_text_field($_POST['domain']);
    $clean_domain = preg_replace('/^https?:\/\//', '', $domain);
    $clean_domain = preg_replace('/^www\./', '', $clean_domain);

    // বেস নাম বের করি (example)
    $base_name = preg_replace('/\..*$/', '', $clean_domain);

    // সব এক্সটেনশন
    $extensions = [
        '.com.bd', '.edu.bd', '.gov.bd', '.net.bd',
        '.org.bd', '.ac.bd', '.mil.bd', '.info.bd', '.বাংলা'
    ];

    $results = [];

    foreach($extensions as $ext){
        $check_domain = $base_name.$ext;

        // ✅ DNS Lookup
        $has_records = false;
        if (function_exists('dns_get_record')) {
            $records = @dns_get_record($check_domain, DNS_A + DNS_AAAA + DNS_CNAME);
            if ($records && count($records) > 0) {
                $has_records = true;
            }
        }
        if (!$has_records && function_exists('checkdnsrr')) {
            if (checkdnsrr($check_domain, "A") || checkdnsrr($check_domain, "MX")) {
                $has_records = true;
            }
        }

        // ✅ Available true হলে রেজিস্টার হয়নি
        $results[] = [
            'domain'    => $check_domain,
            'available' => !$has_records,
            'price'     => 800 // এখানে Future-এ Dashboard থেকে কন্ট্রোল করতে পারবেন
        ];
    }

    wp_send_json_success(['list' => $results]);
}
add_action('wp_ajax_bd_domain_checker', 'bd_domain_checker_ajax');
add_action('wp_ajax_nopriv_bd_domain_checker', 'bd_domain_checker_ajax');

/**
 * ✅ শর্টকোড - ফ্রন্টএন্ডে ফর্ম দেখাবে
 */
function bd_domain_checker_shortcode() {
    ob_start(); ?>
    
    <div class="bd-domain-checker-wrapper">
        <h2 class="bd-domain-title">BD Domain Checker</h2>
        
        <div class="bd-domain-form">
            <input type="text" id="bd-domain-input" placeholder="Enter domain name">
            <select id="bd-domain-ext">
                <option value=".com.bd">.com.bd</option>
                <option value=".edu.bd">.edu.bd</option>
                <option value=".gov.bd">.gov.bd</option>
                <option value=".net.bd">.net.bd</option>
                <option value=".org.bd">.org.bd</option>
                <option value=".ac.bd">.ac.bd</option>
                <option value=".mil.bd">.mil.bd</option>
                <option value=".info.bd">.info.bd</option>
                <option value=".বাংলা">.বাংলা</option>
            </select>
            <button id="bd-domain-submit">Search</button>
        </div>
        
        <div id="bd-domain-result"></div>

        <p class="bd-domain-welcome">
            Welcome to the World of <span class="bangla">.বাংলা</span> & <span class="bd">.bd</span> Domain Service
        </p>
    </div>

    <?php return ob_get_clean();
}
add_shortcode('bd_domain_checker', 'bd_domain_checker_shortcode');

/**
 * ✅ CSS + JS লোড করা
 */
function bd_checker_assets() {
    wp_enqueue_style('bd-checker-style', plugin_dir_url(__FILE__).'style.css');
    wp_enqueue_script('bd-checker-js', plugin_dir_url(__FILE__).'checker.js', ['jquery'], '2.0', true);
    wp_localize_script('bd-checker-js', 'bdAjax', [
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce'   => wp_create_nonce('bd_checker_nonce')
    ]);
}
add_action('wp_enqueue_scripts', 'bd_checker_assets');
