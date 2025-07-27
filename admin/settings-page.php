<?php
if (!defined('ABSPATH')) exit;

// ✅ আগের সেভ করা সেটিংস লোড করবো
$extensions   = get_option('bdc_adv_extensions', []);
$welcome_text = get_option('bdc_adv_welcome', 'Welcome to BD Domain Checker!');
$msg_available = get_option('bdc_adv_available_msg', '✅ {domain} is available for {price} BDT/year');
$msg_taken     = get_option('bdc_adv_taken_msg', '❌ {domain} is already registered');

// ✅ ফর্ম সাবমিট হলে সেভ করবো
if (isset($_POST['bdc_save_settings']) && check_admin_referer('bdc_adv_save','bdc_adv_nonce')) {

    // ✅ Extension list আপডেট
    $updated_ext = [];
    if(!empty($_POST['ext_name'])){
        foreach($_POST['ext_name'] as $idx=>$ext_name){
            $updated_ext[] = [
                'ext' => sanitize_text_field($ext_name),
                'price' => intval($_POST['ext_price'][$idx]),
                'enabled' => isset($_POST['ext_enabled'][$idx]) ? true : false
            ];
        }
    }
    update_option('bdc_adv_extensions', $updated_ext);

    // ✅ অন্যান্য সেটিংস সেভ
    update_option('bdc_adv_welcome', sanitize_text_field($_POST['welcome_text']));
    update_option('bdc_adv_available_msg', sanitize_text_field($_POST['msg_available']));
    update_option('bdc_adv_taken_msg', sanitize_text_field($_POST['msg_taken']));

    echo '<div class="updated"><p>✅ Settings Saved!</p></div>';
}
?>

<div class="wrap">
    <h1>BD Domain Checker – Settings</h1>

    <form method="POST">
        <?php wp_nonce_field('bdc_adv_save','bdc_adv_nonce'); ?>

        <h2>🔧 Domain Extensions</h2>
        <table class="widefat">
            <thead>
                <tr>
                    <th>Enable?</th>
                    <th>Extension</th>
                    <th>Price (BDT/year)</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="bdc-ext-list">
                <?php if(!empty($extensions)): ?>
                    <?php foreach($extensions as $idx=>$ext): ?>
                        <tr>
                            <td><input type="checkbox" name="ext_enabled[<?php echo $idx; ?>]" <?php checked($ext['enabled'], true); ?>></td>
                            <td><input type="text" name="ext_name[<?php echo $idx; ?>]" value="<?php echo esc_attr($ext['ext']); ?>"></td>
                            <td><input type="number" name="ext_price[<?php echo $idx; ?>]" value="<?php echo esc_attr($ext['price']); ?>"></td>
                            <td><button type="button" class="button bdc-remove-row">❌</button></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
        <p>
            <button type="button" class="button button-secondary" id="bdc-add-ext">➕ Add Extension</button>
        </p>

        <hr>

        <h2>🎨 Frontend Texts</h2>
        <table class="form-table">
            <tr>
                <th>Welcome Text</th>
                <td><input type="text" name="welcome_text" value="<?php echo esc_attr($welcome_text); ?>" class="regular-text"></td>
            </tr>
            <tr>
                <th>Available Message</th>
                <td><input type="text" name="msg_available" value="<?php echo esc_attr($msg_available); ?>" class="regular-text">
                    <p class="description">Use {domain} &amp; {price} placeholders</p>
                </td>
            </tr>
            <tr>
                <th>Taken Message</th>
                <td><input type="text" name="msg_taken" value="<?php echo esc_attr($msg_taken); ?>" class="regular-text">
                    <p class="description">Use {domain} placeholder</p>
                </td>
            </tr>
        </table>

        <p><input type="submit" name="bdc_save_settings" class="button button-primary" value="💾 Save Settings"></p>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function(){
    let addBtn = document.getElementById('bdc-add-ext');
    let extList = document.getElementById('bdc-ext-list');

    addBtn.addEventListener('click', function(){
        let idx = extList.querySelectorAll('tr').length;
        let row = document.createElement('tr');
        row.innerHTML = `
            <td><input type="checkbox" name="ext_enabled[${idx}]" checked></td>
            <td><input type="text" name="ext_name[${idx}]" value=""></td>
            <td><input type="number" name="ext_price[${idx}]" value="0"></td>
            <td><button type="button" class="button bdc-remove-row">❌</button></td>
        `;
        extList.appendChild(row);
    });

    document.addEventListener('click', function(e){
        if(e.target.classList.contains('bdc-remove-row')){
            e.target.closest('tr').remove();
        }
    });
});
</script>
