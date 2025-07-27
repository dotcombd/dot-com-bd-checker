<?php
if (!defined('ABSPATH')) exit;

add_action('wp_ajax_bdc_check_domain', 'bdc_check_domain');
add_action('wp_ajax_nopriv_bdc_check_domain', 'bdc_check_domain');

function bdc_check_domain(){
    check_ajax_referer('bdc_adv_nonce', 'security');

    $name = sanitize_text_field($_POST['name'] ?? '');
    if(!$name){
        wp_send_json_error(['message'=>'❌ Please enter a domain name']);
    }

    // এক্সটেনশন সেটিংস থেকে প্রাইস নেবে
    $ext_settings = get_option('bdc_adv_extensions', [
        ['ext'=>'.com.bd','price'=>800,'enabled'=>true],
        ['ext'=>'.net.bd','price'=>700,'enabled'=>true],
        ['ext'=>'.org.bd','price'=>700,'enabled'=>true],
        ['ext'=>'.বাংলা','price'=>1000,'enabled'=>true]
    ]);

    $results = [];
    foreach($ext_settings as $ext_data){
        if(!$ext_data['enabled']) continue;

        $ext = $ext_data['ext'];
        $price = $ext_data['price'];
        $domain = $name.$ext;

        $has_records = false;
        if(function_exists('dns_get_record')){
            $records = @dns_get_record($domain, DNS_A + DNS_AAAA + DNS_CNAME + DNS_MX);
            if($records && count($records)>0) $has_records = true;
        }
        if(!$has_records && function_exists('checkdnsrr')){
            if(checkdnsrr($domain,"A")||checkdnsrr($domain,"MX")) $has_records = true;
        }

        $results[] = [
            'domain'=>$domain,
            'available'=> !$has_records,
            'price'=>$price
        ];
    }

    wp_send_json_success(['results'=>$results]);
}
