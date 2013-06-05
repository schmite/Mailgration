$(document).ready(function(){
  $('#load-inbox').click(function() {
    $.ajax(
      {
        url: 'getinbox.php',
        type: 'POST',
        data: $('#mailgration-form').serialize(),
        success: function(data) {
          if(data.length) {
            $('#inboxes-select').append(data);
            $('p.select-inbox').show();
          } 
        }        
      }       
    );
    
  });
});