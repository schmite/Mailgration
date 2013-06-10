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

if (empty($dest_username) || empty($dest_password) || empty($src_username) || empty($src_password)) {
  print "src_username = $src_username<BR>\n";
  print "src_password = $src_password<BR>\n";
  print "src_server = $src_server<BR>\n";
  print "dest_username = $dest_username<BR>\n";
  print "dest_password = $dest_password<BR>\n";
  print "dest_server = $dest_server<BR>\n";
  //exit("<A HREF=\"javascript:history.back()\">Please fill out all information.</A>");  
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
  
  // Forking part comes here
  foreach($inbox['inboxMultiProcessIntervals'] as $_interval) {
    $pid = pcntl_fork();
     if($pid!=0) {
      // Parent Process
      // TODO Wait until every child process have terminated to terminate
      // TODO Upon receiving a signal, replicate it to every child process
     }
     else {
          echo "Process $i will migrate from Message ".$_interval['begin']." to ".$_interval['end']."<br />\n";
         //migrate_mail($src_server, $src_username, $src_password, $dest_server, $dest_username, $dest_password, $delete_src_msg,$inbox,$_interval);
       } 
     
  }
  
  //migrate_mail($src_server, $src_username, $src_password, $dest_server, $dest_username, $dest_password, $delete_src_msg,$inbox);
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
