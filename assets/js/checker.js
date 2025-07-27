jQuery(document).ready(function($){
    $('#bd-domain-submit').on('click', function(){
        let domainName = $('#bd-domain-input').val().trim();

        if(domainName === ''){
            $('#bd-domain-result').html('<div class="alert-msg">❌ Please enter a domain name</div>');
            return;
        }

        $('#bd-domain-result').html('<div class="loading-msg">⏳ Checking...</div>');

        $.post(bdcAjax.ajaxurl, {
            action: 'bdc_check_domain',
            name: domainName,
            security: bdcAjax.nonce
        }, function(response){
            if(response.success){
                let html = '';
                response.data.results.forEach(function(item){
                    let statusText = item.available ? 
                        `<span class="status avail">✅ Available</span>` : 
                        `<span class="status taken">❌ Registered</span>`;

                    let buyButton = item.available ? 
                        `<a href="#" class="buy-btn">Buy Now</a>` : 
                        `<a href="#" class="buy-btn disabled" disabled>Not Available</a>`;

                    html += `
                    <div class="domain-card">
                        <div class="domain-left">
                            <div class="domain-name">${item.domain}</div>
                            <div class="domain-meta">
                                <span class="domain-price">${item.price} BDT/yr</span>
                                ${statusText}
                            </div>
                        </div>
                        <div class="domain-right">
                            ${buyButton}
                        </div>
                    </div>
                    `;
                });
                $('#bd-domain-result').html(html);
            } else {
                $('#bd-domain-result').html('<div class="alert-msg">⚠️ Server returned error</div>');
            }
        }).fail(function(xhr, status, error){
            console.error("❌ AJAX Error:", status, error);
            $('#bd-domain-result').html('<div class="alert-msg">⚠️ AJAX Failed. See console.</div>');
        });
    });
});
