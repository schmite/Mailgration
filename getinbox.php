<?php

  foreach($_POST as $item => $val) {
      ${$item} = $val;
  }
  
  $stringona = '';
  
  $src_mbox = imap_open("{"."$src_server:143/novalidate-cert}","$src_username","$src_password") 
	 or die("can't connect: ".imap_last_error()."\n");
  
  
  $list = imap_list($src_mbox, "{"."$src_server:143/novalidate-cert}", "*");
  if (is_array($list)) {
      foreach ($list as &$val) {
          $_status = imap_status($src_mbox,$val,SA_MESSAGES);
          $_totalMessages = $_status->messages;
          $_exploded = explode('}',$val);
          $val = $_exploded[1];
          //$val = "<option value=\"$val\">$val</option>";
          $val = '<div class="limit-wrapper">
                    <input type="checkbox" class="checkbox-limit" name="'.$val.'" />'.$val.' ('.$_totalMessages.' mensagens nesta caixa)<br />
                    <div class="limit-box limit-box-num-wrapper">
                      <input type="checkbox" class="checkbox-limit-sub" name="'.$val.'-limit-num-check" />Limitar o número de mensagens a ser importados<br />
                      <div class="limit-box-num limit-box">
                        Limite :<input type="text" name="'.$val.'-limit-num" />
                      </div>
                    </div>
                    <div class="limit-box limit-box-date-wrapper">
                      <input type="checkbox" class="checkbox-limit-sub" name="'.$val.'-limit-date-check" />Limitar o período de mensagens a ser importados<br />
                      <div class="limit-box limit-box-date">
                        A partir de: <input type="text" name="'.$val.'-limit-date-init" /><br />
                        Até: <input type="text" name="'.$val.'-limit-date-end" /><br />                 
                      </div>
                    </div>
                  </div>
          ';
          $stringona .= $val;
          //print imap_utf7_decode($val) . "<br />\n";          
      }
  } else {
      echo "imap_list failed: " . imap_last_error() . "\n";
  }
  print_r($stringona);
  
  //return(json_encode($list));
  

?>