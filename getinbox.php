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
          $_exploded = explode('}',$val);
          $val = $_exploded[1];
          $val = "<option value=\"$val\">$val</option>";
          $stringona .= $val;
          //print imap_utf7_decode($val) . "<br />\n";          
      }
  } else {
      echo "imap_list failed: " . imap_last_error() . "\n";
  }
  print_r($stringona);
  
  //return(json_encode($list));
  

?>