<?php
if (!defined('ABSPATH')) exit;

add_action('wp_ajax_bdc_check_domain','bdc_check_domain');
add_action('wp_ajax_nopriv_bdc_check_domain','bdc_check_domain');

function bdc_check_domain(){
    check_ajax_referer('bdc_nonce','security');

    $name = sanitize_text_field($_POST['name'] ?? '');
    if(!$name){
        wp_send_json_error(['message'=>'❌ Please enter a domain name']);
    }

    $tlds = get_option('bdc_tlds',[]);
    $buyText = get_option('bdc_buy_text','Buy Now');
    $buyURL = get_option('bdc_buy_url','#');

    $results = [];

    foreach($tlds as $ext=>$data){
        if(!$data['enabled']) continue;
        $domain = $name.$ext;
        $has_records = false;

        if(function_exists('dns_get_record')){
            $records = @dns_get_record($domain, DNS_A + DNS_AAAA + DNS_CNAME + DNS_MX);
            if($records && count($records)>0) $has_records = true;
        }
        if(!$has_records && function_exists('checkdnsrr')){
            if(checkdnsrr($domain,"A") || checkdnsrr($domain,"MX")) $has_records = true;
        }

        $results[] = [
            'domain'=>$domain,
            'available'=>!$has_records,
            'price'=>$data['price'],
            'buyText'=>$buyText,
            'buyURL'=>$buyURL
        ];
    }

    wp_send_json_success(['results'=>$results]);
}
