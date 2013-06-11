/**
 * Performs ajax interaction between the
 * User Interface and the mail Server
 */

$(document).ready(function(){
  //handle the click of the button
  $('#load-inbox').click(function() {
    
    if($('select#src_server').val=="other" && $('#src_server_name').val().length == 0) {
      alert('Digite o "Endereço do Servidor".');
      return false;
    }
    if($('#src_username').val().length == 0) {
      alert('Digite o "Usuário de E-mail".');
      return false;
    }
    if($('#src_password').val().length == 0) {
      alert('Digite a "Senha".');
      return false;
    }
    $.ajax(
      {
        url: 'getinbox.php',
        type: 'POST',
        data: $('#mailgration-form').serialize(),
        success: function(data) {
          if(data.length) {
            //clear results in form
            $('#inboxes-select option').remove();
            //add mail folders
            $('.inbox-limit').empty();
            $('.inbox-limit').append(data);
            
            // Activate triggers to limit number of messages and period
            $('.checkbox-limit').click(function(){
              if($(this).is(':checked')) {
                $(this).parent().find('.limit-box-num-wrapper,.limit-box-date-wrapper,.limit-box-multi-wrapper').show();
              }
              else {
                $(this).parent().find('.limit-box-num-wrapper input,.limit-box-date-wrapper input').prop('checked',false);
                $(this).parent().find('.limit-box input').val('');
                $(this).parent().find('.limit-box').hide();
              }
            });
            
            $('.checkbox-limit-sub').click(function(){
              if($(this).is(':checked')) {
                $(this).parent().find('.limit-box').show();
              }
              else {
                $(this).parent().find('.limit-box input').val('');
                $(this).parent().find('.limit-box').hide();
              }
            });
            
            //$('p.select-inbox').show();
            $('.inbox-limit').show();
            $('.destino').show();
          } 
        }        
      }       
    );
  });
  $('select#src_server').change(function() {
    if($(this).val() != 'other') {
      $('#src_server_info').hide();
      $('#src_server_info input').val('');
    }
    else {
      $('#src_server_info').show();
    }
  });
  $('select#dest_server').change(function() {
    if($(this).val() != 'other') {
      $('#dest_server_info').hide();
      $('#dest_server_info input').val('');
    }
    else {
      $('#dest_server_info').show();
    }
  });
});