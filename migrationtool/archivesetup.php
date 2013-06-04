<?php

require("./archivesetup.conf");


// Getting Params from Form
$src_username = $_POST['src_username'];
$src_password = $_POST['src_password'];
$dest_username = $_POST['dest_username'];
$dest_password = $_POST['dest_password'];
$folder_date = $_POST['folder_date'];
$inbox_date = $_POST['folder_date'];
$delete_src_msg = $_POST['folder_date'];


if (empty($dest_username) || empty($dest_password) || empty($src_username) || empty($src_password) || empty($inbox_date) || empty($folder_date) || empty($delete_src_msg)) {
  print "src_username = $src_username<BR>\n";
  print "src_password = $src_password<BR>\n";
  print "src_server = $src_server<BR>\n";
  print "dest_username = $dest_username<BR>\n";
  print "dest_password = $dest_password<BR>\n";
  print "dest_server = $dest_server<BR>\n";
  print "folder_date = $folder_date<BR>\n";
  print "inbox_date = $inbox_date<BR>\n";
  print "delete_src_msg = $delete_src_msg<BR>\n";
  exit("<A HREF=\"javascript:history.back()\">Please fill out all information.</A>");
}

if ($inbox_date == "true" && empty($inbox_date_weeks)) {
  print "You must enter a number of weeks for moving messages in INBOX<BR>\n";
  exit("<A HREF=\"javascript:history.back()\">Please fill out all information.</A>");
}
if ($folder_date == "true" && empty($folder_date_weeks)) {
  print "You must enter a number of weeks for moving messages in IMAP folders<BR>\n";
  exit("<A HREF=\"javascript:history.back()\">Please fill out all information.</A>");
}

// Set up some vars
if (empty($time)) $time = "2am";

$script_header = "#!/bin/sh\n\n";

if ($inbox_date == "false") $inbox_date_weeks = -1;
if ($folder_date == "false") $folder_date_weeks = -1;

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
