<?php
if (!defined('ABSPATH')) exit;

// ✅ Add menu in dashboard
add_action('admin_menu', function(){
    add_menu_page(
        'BD Domain Checker',
        'BD Domain Checker',
        'manage_options',
        'bd-domain-checker-pro',
        'bdc_settings_page',
        'dashicons-admin-tools',
        80
    );
});

function bdc_settings_page(){
    $tlds = get_option('bdc_tlds',[]);
    $buyText = get_option('bdc_buy_text','Buy Now');
    $buyURL = get_option('bdc_buy_url','#');

    // ✅ Save on submit
    if(isset($_POST['bdc_save_settings'])){
        $new_tlds = [];
        foreach($_POST['tld'] as $ext=>$data){
            $new_tlds[$ext] = [
                'enabled' => isset($data['enabled']) ? 1:0,
                'price'   => intval($data['price'])
            ];
        }
        update_option('bdc_tlds',$new_tlds);
        update_option('bdc_buy_text', sanitize_text_field($_POST['buy_text']));
        update_option('bdc_buy_url', esc_url_raw($_POST['buy_url']));
        echo '<div class="updated"><p>✅ Settings Saved!</p></div>';
        $tlds = $new_tlds;
        $buyText = $_POST['buy_text'];
        $buyURL = $_POST['buy_url'];
    }
    ?>
    <div class="wrap">
        <h1>BD Domain Checker Settings</h1>
        <form method="post">
            <table class="widefat">
                <thead><tr>
                    <th>TLD</th><th>Enable</th><th>Price (BDT/yr)</th>
                </tr></thead>
                <tbody>
                <?php foreach($tlds as $ext=>$data): ?>
                <tr>
                    <td><strong><?php echo $ext; ?></strong></td>
                    <td><input type="checkbox" name="tld[<?php echo $ext;?>][enabled]" <?php checked($data['enabled'],1);?>></td>
                    <td><input type="number" name="tld[<?php echo $ext;?>][price]" value="<?php echo esc_attr($data['price']);?>"></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <h3>Buy Now Button Settings</h3>
            <label>Button Text:</label>
            <input type="text" name="buy_text" value="<?php echo esc_attr($buyText);?>"><br><br>
            <label>Buy URL:</label>
            <input type="text" name="buy_url" value="<?php echo esc_attr($buyURL);?>" size="50"><br><br>
            <input type="submit" name="bdc_save_settings" class="button-primary" value="Save Settings">
        </form>
    </div>
    <?php
}
