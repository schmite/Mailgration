<?php

require("./archivesetup.conf");


// Getting Params from Form
foreach($_POST as $item => $val) {
    ${$item} = $val;
}

print_r($_POST);


if (empty($dest_username) || empty($dest_password) || empty($src_username) || empty($src_password)) {
  print "src_username = $src_username<BR>\n";
  print "src_password = $src_password<BR>\n";
  print "src_server = $src_server<BR>\n";
  print "dest_username = $dest_username<BR>\n";
  print "dest_password = $dest_password<BR>\n";
  print "dest_server = $dest_server<BR>\n";
  exit("<A HREF=\"javascript:history.back()\">Please fill out all information.</A>");  
}


// There's no need of shell script
require('archive.php');

print("The following folders will be imported: <br />");
foreach($inboxes as $inbox) {
  print $inbox . "<br />";
}


foreach($inboxes as $inbox) {
  migrate_mail($src_server, $src_username, $src_password, $dest_server, $dest_username, $dest_password, $delete_src_msg,$inbox);
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
