jQuery(document).ready(function($){
  $('#bdc-submit').on('click', function(){
    let domainName = $('#bdc-input').val().trim();

    if(domainName===''){
      $('#bdc-result').html('❌ Please enter a domain name');
      return;
    }

    $('#bdc-result').html('⏳ Checking...');

    $.post(bdAjax.ajaxurl,{
      action:'bdc_check_domain',
      name:domainName,
      security:bdAjax.nonce
    },function(response){
      if(response.success){
        let html='';
        response.data.results.forEach(function(item){
          html+=`
            <div class="bdc-result-box ${item.available?'avail':'not-avail'}">
              <div class="bdc-left">
                <strong>${item.domain}</strong><br>
                ${item.status}
              </div>
              <div class="bdc-right">
                <span class="price">${item.price} BDT/yr</span>
                <a href="#" class="buy-btn">Buy Now</a>
              </div>
            </div>
          `;
        });
        $('#bdc-result').html(html);
      }else{
        $('#bdc-result').html('⚠️ Error!');
      }
    }).fail(function(){
      $('#bdc-result').html('⚠️ AJAX Failed');
    });
  });
});
