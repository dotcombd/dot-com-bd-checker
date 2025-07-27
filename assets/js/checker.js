jQuery(document).ready(function($){
    $('#bd-domain-submit').on('click', function(){
        let domainName = $('#bd-domain-input').val().trim();
        let ext = $('#bd-domain-ext').val();
        let fullDomain = domainName + ext;

        if(domainName === ''){
            $('#bd-domain-result').html('❌ Please enter a domain name');
            return;
        }

        $('#bd-domain-result').html('⏳ Checking...');

        $.post(bdAjax.ajaxurl, {
            action: 'bd_domain_checker',
            domain: fullDomain,
            security: bdAjax.nonce
        }, function(response){
            if(response.success){
                $('#bd-domain-result').html(response.data.message);
            } else {
                $('#bd-domain-result').html('⚠️ Server returned error');
            }
        }).fail(function(xhr, status, error){
            console.error("❌ AJAX Error:", status, error);
            $('#bd-domain-result').html('⚠️ AJAX Failed. See console.');
        });
    });
});
