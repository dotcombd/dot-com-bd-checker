jQuery(document).ready(function($){

    // === ডোমেইন চেক বাটন ক্লিক ===
    $('#bd-domain-submit').on('click', function(){
        let domainName = $('#bd-domain-input').val().trim();
        let ext = $('#bd-domain-ext').val();
        let fullDomain = domainName + ext;

        if(domainName === ''){
            $('#bd-domain-result').html('❌ Please enter a domain name');
            return;
        }

        // লোডিং টেক্সট
        $('#bd-domain-result').html('⏳ Checking...');

        // AJAX কল
        $.post(bdAjax.ajaxurl, {
            action: 'bd_domain_checker',
            domain: fullDomain,
            security: bdAjax.nonce
        }, function(response){

            if(response.success){

                let html = '';

                // === সার্ভার থেকে আসা লিস্ট লুপ করা ===
                response.data.list.forEach(item => {

                    // স্ট্যাটাস টেক্সট
                    let statusText = item.available 
                        ? `<span class="status avail">✅ Available</span>`
                        : `<span class="status taken">❌ Taken</span>`;
                    
                    // Buy Now বাটন
                    let buyButton = item.available
                        ? `<a href="#" class="buy-btn">Buy Now</a>`
                        : `<a href="#" class="buy-btn disabled">Not Available</a>`;

                    // === ফাইনাল HTML টেমপ্লেট (2-লাইন কমপ্যাক্ট) ===
                    html += `
                        <div class="domain-card compact">
                            <!-- 1st Row: Domain + Status -->
                            <div class="domain-top">
                                <span class="domain-name">${item.domain}</span>
                                ${statusText}
                            </div>
                            <!-- 2nd Row: Price + Button -->
                            <div class="domain-bottom">
                                <span class="domain-price">${item.price} BDT/yr</span>
                                <div class="domain-action">
                                    ${buyButton}
                                </div>
                            </div>
                        </div>
                    `;
                });

                $('#bd-domain-result').html(html);

            } else {
                $('#bd-domain-result').html('⚠️ Server returned error');
            }

        }).fail(function(xhr, status, error){
            console.error("❌ AJAX Error:", status, error);
            $('#bd-domain-result').html('⚠️ AJAX Failed. See console.');
        });
    });

});
