/**
 * Performs ajax interaction between the
 * User Interface and the mail Server
 */

$(document).ready(function(){
  //handle the click of the button
  $('#load-inbox').click(function() {
    if($('#src_server').val().length == 0) {
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
            $('#inboxes-select').append(data);
            //show next steps
            $('p.select-inbox').show();
          } 
        }        
      }       
    );
  });
});