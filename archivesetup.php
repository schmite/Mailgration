<?php

include('constants.php');
require("./archivesetup.conf");


// Getting Params from Form
foreach($_POST as $item => $val) {
    ${$item} = $val;
}
echo '<pre>';
print_r($_POST);
echo '</pre>';

/* Required data:
   $src_server
   $src_username
   $src_password
   $src_server_security_protocol
   
   $dest_server
   
*/ 

if (empty($dest_username) || empty($dest_password) || empty($src_username) || empty($src_password)) {
  print "src_username = $src_username<BR>\n";
  print "src_password = $src_password<BR>\n";
  print "src_server = $src_server<BR>\n";
  print "dest_username = $dest_username<BR>\n";
  print "dest_password = $dest_password<BR>\n";
  print "dest_server = $dest_server<BR>\n";
  //exit("<A HREF=\"javascript:history.back()\">Please fill out all information.</A>");  
}

//die();

// Setting up the server connection strings
  // Source  
  if($src_server == 'other') {  
    $strFlags = '';                                                     
    foreach($src_server_security_protocol as $flag) {                   
      $strFlags .= '/'.$flag;                                           
    }                                                                   
    $strSrcConnection = '{'.$src_server_name.":$src_server_port$strFlags}";
  }
  else {
    require('./mailref.php');
    if(!key_exists($src_server,$mailRef)) {
      die("Unknown server, probably data was inserted in a bad way");
    }
    
    $strFlags = '';                                                     
    foreach($mailRef[$src_server]['flags'] as $flag) {                   
      $strFlags .= '/'.$flag;                                           
    }    
    $strSrcConnection = '{'.$mailRef[$src_server]['address'].':'.$mailRef[$src_server]['port'].$strFlags.'}';        
  }
  // Destiny
  if($dest_server == 'other') {  
    $strFlags = '';                                                     
    foreach($dest_server_security_protocol as $flag) {                   
      $strFlags .= '/'.$flag;                                           
    }                                                                   
    $strDestConnection = '{'.$dest_server_name.":$dest_server_port$strFlags}";
  }
  else {
    require('./mailref.php');
    if(!key_exists($dest_server,$mailRef)) {
      die("Unknown server, probably data was inserted in a bad way");
    }
    
    $strFlags = '';                                                     
    foreach($mailRef[$dest_server]['flags'] as $flag) {                   
      $strFlags .= '/'.$flag;                                           
    }    
    $strDestConnection = '{'.$mailRef[$dest_server]['address'].':'.$mailRef[$dest_server]['port'].$strFlags.'}';        
  }


// Modifies inbox info to include limits set by user
foreach($inboxes as &$inbox) {
  print $inbox . "<br />";
  $inbox = array('inboxName' => $inbox);
  
  
  $_strInboxFiltered = str_replace(array(' ','.'),'_',$inbox['inboxName']);
  $_strCheckboxLimitNum = $_strInboxFiltered. '-limit-num';
  $_strCheckboxLimitDate = $_strInboxFiltered. '-limit-date';
  $_strCheckboxLimitMulti = $_strInboxFiltered. '-limit-multi';
  
  
  if(isset(${$_strCheckboxLimitNum . '-check'})) {
    // The user has set a limit to the number of messages to be imported    
    $inbox['inboxLimitNum'] = min(${$_strCheckboxLimitNum},${$_strInboxFiltered . '-num-msgs'});
    $inbox['inboxLimitNumDir'] = ${$_strCheckboxLimitNum . '-dir'};
  }
  else {
    $inbox['inboxLimitNumDir'] = OLD;
    $inbox['inboxLimitNum'] = ${$_strInboxFiltered . '-num-msgs'};
  }
  if(isset(${$_strCheckboxLimitDate. '-check'})) {
    // The user has set a limit to the period of time messages
    $inbox['inboxLimitDateBegin'] = ${$_strCheckboxLimitDate . '-init'} ? strtotime(${$_strCheckboxLimitDate . '-init'}) : 0;
    $inbox['inboxLimitDateEnd'] = ${$_strCheckboxLimitDate . '-end'} ? strtotime(${$_strCheckboxLimitDate . '-end'}) : time();
  }
  else {
    $inbox['inboxLimitDateBegin'] = 0;
    $inbox['inboxLimitDateEnd'] = time();
  }
  if(isset(${$_strCheckboxLimitMulti. '-check'})) {
    // The user has chosen multiple processes
    $inbox['inboxNumProcess'] = isset(${$_strCheckboxLimitMulti . '-num'}) ? ${$_strCheckboxLimitMulti . '-num'} : 1;
    $inbox['inboxMultiProcessIntervals'] = array();
    echo ${$_strCheckboxLimitMulti . '-num'}.'<br />';
    $_delta = ceil($inbox['inboxLimitNum']/$inbox['inboxNumProcess']);
    echo 'Delta: '.$_delta.'<br />';
    $_startPoint = $inbox['inboxLimitNumDir'] == OLD ? 0 : ${$_strInboxFiltered . '-num-msgs'} - $inbox['inboxLimitNum'] + 1;
    
    for($j = 0; $j < $inbox['inboxNumProcess']; $j++) {
      $_initPoint = $_startPoint + $j*$_delta + 1;
      $_endPoint = min($_startPoint + ($j+1)*$_delta,${$_strInboxFiltered . '-num-msgs'}); 
      array_push($inbox['inboxMultiProcessIntervals'],array('begin' => $_initPoint, 'end' => $_endPoint));
    }  
  }  
}

echo '<pre>';
foreach($inboxes as $inbox) {
  
  print_r($inbox);
  echo '<br />';
  
}
echo '</pre>';

// There's no need of shell script
require('archive.php');

foreach($inboxes as $inbox) {
  
  migrate_mail($strSrcConnection, $src_server_username, $src_server_password, $strDestConnection, $dest_server_username, $dest_server_password, $delete_src_msg,$inbox);

}




?>
<HTML>
<HEAD>
  <TITLE>IMAP Migration Tool</TITLE>
</HEAD>
<BODY BGCOLOR="#bbccdd">
<BR><BR>
<?php /* NOTE - $submit_message can be configured in archivesetup.conf */ ?>
<CENTER><?= $submit_message; ?>.</CENTER>
</BODY> 
</HTML>
