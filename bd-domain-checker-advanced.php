<?php
/**
 * Plugin Name: BD Domain Checker Pro
 * Description: Advanced BD (.bd & .বাংলা) Domain Availability Checker with Price + Buy Now button.
 * Version: 2.0
 * Author: DOT.COM.BD
 */

if (!defined('ABSPATH')) exit;

// ✅ ইনক্লুড Ajax Handler
require_once plugin_dir_path(__FILE__).'includes/ajax-handler.php';

/**
 * ✅ প্লাগিন অ্যাক্টিভ হলে ডিফল্ট প্রাইস সেট করা
 */
register_activation_hook(__FILE__, function(){
    if(!get_option('bdc_domain_price')){
        update_option('bdc_domain_price', '800'); // ডিফল্ট দাম
    }
});

/**
 * ✅ ড্যাশবোর্ড মেনু যোগ করা
 */
add_action('admin_menu', function(){
    add_options_page(
        'BD Domain Checker Settings',
        'BD Domain Checker',
        'manage_options',
        'bd-domain-checker',
        'bdc_settings_page'
    );
});

/**
 * ✅ সেটিংস পেজ
 */
function bdc_settings_page(){ 
    if(isset($_POST['bdc_price'])){
        update_option('bdc_domain_price', sanitize_text_field($_POST['bdc_price']));
        echo "<div class='updated'><p>✅ Price Updated!</p></div>";
    }
    $price = get_option('bdc_domain_price'); ?>
    
    <div class="wrap">
        <h2>BD Domain Checker Settings</h2>
        <form method="post">
            <label><strong>Domain Price (BDT/yr):</strong></label><br>
            <input type="number" name="bdc_price" value="<?php echo esc_attr($price); ?>" />
            <p><input type="submit" class="button-primary" value="Save Changes"></p>
        </form>
    </div>
<?php }

/**
 * ✅ শর্টকোড → ফর্ম + রেজাল্ট বক্স লোড করবে
 */
add_shortcode('bd_domain_checker', function(){
    ob_start();
    include plugin_dir_path(__FILE__).'templates/form-layout.php';
    return ob_get_clean();
});

/**
 * ✅ CSS + JS লোড করা
 */
add_action('wp_enqueue_scripts', function(){
    wp_enqueue_style('bdc-style', plugin_dir_url(__FILE__).'assets/css/style.css');
    wp_enqueue_script('bdc-js', plugin_dir_url(__FILE__).'assets/js/script.js',['jquery'],false,true);
    wp_localize_script('bdc-js','bdAjax',[
        'ajaxurl'=>admin_url('admin-ajax.php'),
        'nonce'=>wp_create_nonce('bdc_nonce')
    ]);
});
