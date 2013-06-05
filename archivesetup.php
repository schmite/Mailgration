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
// "Work Horse" script needs to be transformed in a function to be called upon different inbox folders
// Inbox folders list might be called upon a 2-step form
print("The following folders will be imported: <br />");
foreach($inboxes as $inbox) {
  print $inbox . "<br />";
}

require('archive.php');
migrate_mail($src_server, $src_username, $src_password, $dest_server, $dest_username, $dest_password, $delete_src_msg,$inboxes[0]);

die();



$script_header = "#!/bin/sh\n\n";

// This sets up the call to the work horse script with all the required parameters
function getScriptCall($folder_name, $folder_date_weeks, $delete_src_msg) {
  global $src_server, $src_username, $src_password;
  global $dest_server, $dest_username, $dest_password;
  $script_call = "./archive.php $src_server $src_username \"$src_password\" $dest_server $dest_username \"$dest_password\" \"$folder_name\" $folder_date_weeks \"$delete_src_msg\"\n";
  return $script_call;
}

/*
    Script logic begins here
*/

// Prep the shell script
if (empty($archivesh)) $archivesh = "./scripts/archive.sh";
$there = file_exists($archivesh);
$empty = (filesize($archivesh) == 0) ? true : false;
$fp = fopen($archivesh, "a-");
if (!$there || $empty) {
  fwrite($fp, $script_header);
}

// Get list of mailboxes from src_server for $username
$src_mbox = imap_open("{"."$src_server:143"."/novalidate-cert}","$src_username","$src_password")
         or die("can't connect: ".imap_last_error());
// TODO - provide the user with a checkbox for only archiving subscribed folders
//         array imap_listsubscribed(int imap_stream, string ref, string pattern)
//                                       $src_mbox     \{$src_server}   *
//         (Maybe imap_lsub() or imap_getsubscribed())
$list = imap_listmailbox($src_mbox,"{"."$src_server:143"."/novalidate-cert}","*");
if (is_array($list)) {
  reset($list);
} else {
  print "imap_listmailbox failed: ".imap_last_error()."\n";
}
while (list($key, $val) = each($list)) {
  $mailbox = imap_utf7_decode($val);
  $fullmailbox = $mailbox;
  $mailbox = ereg_replace("\{", "", $mailbox);
  $mailbox = ereg_replace("\}", "", $mailbox);
  $mailbox = ereg_replace("^$src_server:143/novalidate-cert", "", $mailbox);
  
  // Skip UNIX hidden files
  if (ereg("^\.", $mailbox)) {
    continue;
  }
  
  // mailboxes to be skipped skipped here.
  foreach ($folders_skip as $skip) {
    if ($mailbox == $skip) $skipthis = true;
    if (ereg("^$skip", $mailbox)) $skipthis = true;
  }
  
  // Set up $script_call and script it to the archive.sh shell script
  if (!$skipthis) {
    $script_call = getScriptCall($mailbox, ($mailbox == "INBOX") ? $inbox_date_weeks : $folder_date_weeks, $delete_src_msg);
    fwrite($fp, $script_call);
  }
  $skipthis = false;
}

fwrite($fp, "\n");
fclose($fp);
if (!chmod($archivesh, 0755)) {
  print "\n\n<BR><BR>WARNING: $archivesh not chmoded!!<BR>";
  print "<A HREF=\"mailto:jsolis@globalworks.com\">Please notify Jason Solis</A><BR><BR>\n\n";
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
