jQuery(document).ready(function($){
  $('#bdc-domain-submit').on('click',function(){
    let name=$('#bdc-domain-input').val().trim();
    if(name===''){ $('#bdc-domain-result').html('❌ Please enter domain name'); return; }
    $('#bdc-domain-result').html('⏳ Checking...');

    $.post(bdcAjax.ajaxurl,{
      action:'bdc_adv_check',
      security:bdcAjax.nonce,
      name:name
    },function(res){
      if(res.success){
        let html='<ul class="bdc-result-list">';
        res.data.results.forEach(r=>{
          let cls=r.status==='available'?'avail':'taken';
          html+=`<li class="${cls}">${r.message}</li>`;
        });
        html+='</ul>';
        $('#bdc-domain-result').html(html);
      }else{
        $('#bdc-domain-result').html('⚠️ Server Error');
      }
    }).fail(()=>$('#bdc-domain-result').html('❌ AJAX Failed'));
  });
});
