<?php
if (!defined('ABSPATH')) exit;

add_action('wp_ajax_bdc_adv_check','bdc_adv_check');
add_action('wp_ajax_nopriv_bdc_adv_check','bdc_adv_check');

function bdc_adv_check(){
    check_ajax_referer('bdc_adv_nonce','security');

    $name = sanitize_text_field($_POST['name']??'');
    if(!$name) wp_send_json_error(['msg'=>'❌ No domain name!']);

    $exts = get_option('bdc_adv_extensions',[]);
    $avail_msg = get_option('bdc_adv_available_msg');
    $taken_msg = get_option('bdc_adv_taken_msg');

    $results=[];
    foreach($exts as $ext){
        if(!$ext['enabled']) continue;
        $domain=$name.$ext['ext'];

        // DNS Lookup
        $taken=false;
        if(function_exists('dns_get_record')){
            $r=@dns_get_record($domain,DNS_A+DNS_AAAA+DNS_CNAME+DNS_MX);
            if($r && count($r)>0) $taken=true;
        }
        if(!$taken && function_exists('checkdnsrr')){
            if(checkdnsrr($domain,"A")||checkdnsrr($domain,"MX")) $taken=true;
        }

        if($taken){
            $msg=str_replace('{domain}',$domain,$taken_msg);
        }else{
            $msg=str_replace(
                ['{domain}','{price}'],
                [$domain,$ext['price']],
                $avail_msg
            );
        }

        $results[]=[
            'domain'=>$domain,
            'status'=>$taken?'taken':'available',
            'message'=>$msg
        ];
    }

    // Save log
    global $wpdb;
    $wpdb->insert($wpdb->prefix.'bd_checker_logs',[
        'domain_name'=>$name,
        'ip_address'=>$_SERVER['REMOTE_ADDR']
    ]);

    wp_send_json_success(['results'=>$results]);
}
