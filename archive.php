<?php
include('constants.php');
function migrate_mail($src_server, $src_username, $src_password, $dest_server, $dest_username, $dest_password, $delete_src_msg, $inboxArray) {
  $debug = true;
  
  
  // Set up vars from the args for this script
  $src_password = "$src_password";
  $dest_password = "$dest_password";
  $folder_name = "$folder_name";
  $delete_src_msg = "$delete_src_msg";
  
  $mailbox = $inboxArray['inboxName'];
  $maxNumMsg = $inboxArray['inboxLimitNum'];
  $direction = $inboxArray['inboxLimitNumDir'];
  // Set up the time var
  $now = time();
  /*if ($folder_date_weeks == -1) {
    $archive_date = -1;
  }
  else {
    $archive_date = $now - (60 * 60 * 24 * 7 * $folder_date_weeks);
  }*/
  
  // Used for archive inbox (it gets a special name)
  $archive_date_string = date("dMY", $now);
  
  // Make sure we have all the required parameters
  if (empty($dest_username) || empty($dest_password) || empty($src_username) || empty($src_password)) {
    print "src_server = $src_server\n";
    print "src_username = $src_username\n";
    print "src_password = $src_password\n";
    print "dest_server = $dest_server\n";
    print "dest_username = $dest_username\n";
    print "dest_password = $dest_password\n";
    exit("Error: Missing arguments!!\n\n");
  }
  
  
  
  
  /*
  	Script begins here
  */
  
  // Open up the source and destination server
  //  Open up source server TO THE FOLDER we are archiving.  THIS IS VERY IMPORTANT!
  if ($debug) print "Opening src_server: $src_server...\n";
  $src_mbox = imap_open("{"."$src_server:143/novalidate-cert}" . $mailbox,"$src_username","$src_password") 
  	 or die("can't connect: ".imap_last_error()."\n");
  //  Open up dest server to default folder ($dest_mailbox/$mailbox may not exists yet)
  if ($debug) print "Opening dest_server: $dest_server...\n";
  $dest_mbox = imap_open("{"."$dest_server:143/novalidate-cert}","$dest_username","$dest_password", OP_HALFOPEN)
  	 or die("can't connect: ".imap_last_error()."\n");
  
  if ($debug) {
    print "\$src_mbox = $src_mbox\n";
    print "\$dest_mbox = $dest_mbox\n";
  }
  
  // If INBOX, don't copy to INBOX on dest_server,
  //  instead copy to a folder named after DATE of archiving.
  // TODO - Folders with subfolders should have their mail put in a special subfolder
  //         named after the Top Folder.  Look to imap_getmailboxes() and RFC 2060
  /*if ($mailbox == "INBOX") {
    $inbox = true;
    cleanBackupInboxFolder($dest_mbox, $dest_server, $backup_inbox_folder);
    $dest_mailbox = getArchiveDateName($now);
    //if ($debug) print "\n INBOX renamed to $dest_mailbox \n";
  }*/
  //else {
    $dest_mailbox = $mailbox;
    echo '<br />'.$mailbox.'<br />';
  //}
  
  // Create $dest_mailbox on $dest_server IF it doesn't exist
  if (!folderExists($dest_mbox, $dest_server, $dest_mailbox)) {
    if ($debug) print "creating $dest_mailbox on $dest_server $dest_mbox\n";
    createmailbox($dest_mbox, $dest_server, $dest_mailbox);
  }
  /*
  // Create TEMP folder to back up INBOX messages from src_server not being 'archived'
  if ($inbox && !folderExists($dest_mbox, $dest_server, $backup_inbox_folder)) {
    if ($debug) print "creating $backup_inbox_folder on $dest_server $dest_mbox\n";
    createmailbox($dest_mbox, $dest_server, $backup_inbox_folder);
  }
  */
  // And then reopen dest_server to $dest_mailbox so we can count before and afters
  // NOTE - the use of foo is a cludge to fix the imap_reopen bug!!
  $foo = $dest_mbox;
  $dest_mbox = imap_reopen($dest_mbox, "{"."$dest_server:143}" . $dest_mailbox);
  $dest_mbox = $foo;
  if ($debug) print "\$dest_mbox = $dest_mbox\n";
  
  
  // Print out number of messages from source server
  $da_no_msgs = imap_num_msg($src_mbox);
  print "Msg Count on $src_server - $mailbox: " . $da_no_msgs . "\n";
  
  // Print out number of message from dest server before running append()s
  $no_msgs = imap_num_msg($dest_mbox);
  print "Msg Count on $dest_server - $dest_mailbox before migration: ". $no_msgs."\n";
  
  // Get each message from src_mbox (connected to the correct mailbox) 
  //  and append it to {$dest_server}$mailbox IF it's older 
  //  than $archive_date, which is set based on $folder_date_weeks
  // Then mark message deleted on src_server IF $delete_src_msg is true
  
  //Performance
  imap_headers($src_mbox);
  
  $startPoint = mailgrationStartPoint($da_no_msgs,$direction,$maxNumMsg);
  for ($i = $startPoint; $i <= $startPoint+$maxNumMsg-1; $i++) {
    $obj = imap_header($src_mbox, $i);
    $msg_date = $obj->udate;
    $msg_date = getSentDate($src_mbox, $i);
    if (false) {
      print "msg_date = $msg_date\n";
      $con = date("D, d M Y H:i:s", $msg_date);
      print "convert date back = $con\n";
      exit();
    }
    //if (($archive_date == -1) || ($msg_date < $archive_date)) {
      $header = imap_fetchheader($src_mbox,$i);
      $contents = $header . "\r\n" . imap_body($src_mbox, $i, FT_PEEK);
      if ($debug) print "\nappending msg $i: $dest_server $dest_mbox : $msg_date < $archive_date\n";      
      if (imap_append($dest_mbox, "{"."$dest_server}".$dest_mailbox, $contents,getFlagsFromMsg($src_mbox,$i))) {
        if ($delete_src_msg == "true") {
          if ($debug) print "delete_src_msg = $delete_src_msg - Deleting source message\n";
          if (!deletemsg($src_mbox, $i)) {
            print "  WARNING message $i for $src_username on $src_server not deleted!!\n";
          }
        }
        else {
          if ($debug) print "delete_src_msg = $delete_src_msg - NOT deleting source message\n";
        }
      }
      else {
        print "  WARNING message $i for $src_username on $src_server not appended to $dest_server\n";
      }
    //}
    /*elseif ($inbox) {
      $contents = imap_fetchheader($src_mbox, $i) . "\r\n" . imap_body($src_mbox, $i);
      if ($debug) print "copying msg $i to $backup_inbox_folder: $msg_date > $archive_date\n";
      if (imap_append($dest_mbox, "\{$dest_server}".$backup_inbox_folder, $contents)) {
      }
      else {
        print "  WARNING message $i for $src_username not copied to $backup_inbox_folder\n";
      }
    }*/
    /*else {
      if ($debug) print "skipping msg $i: $msg_date > $archive_date\n";
    }*/
  }
  // Now expunge the mailbox
  if ($delete_src_msg == "true") {
    expunge($src_mbox);
  }
  
  // Print out number of messages from source server after deletions
  $no_msgs = imap_num_msg($src_mbox);
  print "Msg Count on $src_server - $mailbox after migration: " . $no_msgs . "\n";
  
  // Print out number of message from dest server after appends
  $no_msgs = imap_num_msg($dest_mbox);
  print "Msg Count on $dest_server - $dest_mailbox after migration : ". $no_msgs."\n\n";
  
  
  @imap_close($src_mbox);
  @imap_close($dest_mbox);

}

// $mbox - connection to mail server
// $server - name of mail server you are creating a mailbox on
// $mailbox - the name of the mailbox you want to create
function createmailbox($mbox, $server, $mailbox) {
  global $src_username;
  if (@imap_createmailbox($mbox,imap_utf7_encode("{" . $server . "}$mailbox"))) {
    $status = @imap_status($mbox,"{" . $server . "}" . $mailbox,SA_ALL);
    if($status) {
      print("$src_username - $mailbox:\n");
      print("UIDvalidity:". $status->uidvalidity)."\n\n";
    } else {
      print  "imap_status on new mailbox - $mailbox failed: ".imap_last_error()."\n";
    }
  } else {
    print  "could not create new mailbox - $mailbox: ".implode("\n",imap_errors())."\n";
  }
}

// $mbox - connection to the mail server
// $mailbox - the name of the folder you are checking if exists
function folderExists($mbox, $server, $mailbox) {
  $folders = imap_listmailbox($mbox, "{" . $server . "}", $mailbox);
  if (empty($folders))
    return false;
  else 
    return true;
}

// $mbox - connection to mail server
// $msg_no - the message number to be deleted
// NOTE - make sure that you are connected to the correct mailbox!!
function deletemsg($mbox, $msg_no) {
  if (imap_delete($mbox, $msg_no)) {
    return true;
  }
  else {
    print imap_last_error() . "\n";
    return false;
  }
}

// In PHP4 msg_number can be string with messages numbers 
// (like a '1,2,6') and/or range of messages (like a '1:*')
// NOTE - make sure that you are connected to the correct mailbox!!
function deletemsgs($mbox, $first_msg_no, $last_msg_no) {
  if (imap_delete($mbox, "$first_msg_no:$last_msg_no")) {
    return true;
  }
  else {
    print imap_last_error() . "\n";
    return false;
  }
}

/*
// mbox - the connection to the server
// server - the server we are cleaning the BackupInboxFolder on
// backup_inbox_folder - the name of the BackupInboxFolder
function cleanBackupInboxFolder($mbox, $server, $backup_inbox_folder) {
  imap_reopen($mbox, "\{$server:143}" . $backup_inbox_folder);
  deletemsgs($mbox, '1', '*');
}
*/

// mbox - the connection to the server
// NOTE - since imap_expunge is defined as Returns TRUE, i'm not going to bother.
function expunge($mbox) {
  imap_expunge($mbox);
}

// $mbox - connection to mail server
// $server - name of mail server you are checking status of
// $mailbox - the name of the mailbox you want to check the status of
function getMailBoxStatus($mbox, $server, $mailbox) {
  global $src_username;
  $status = @imap_status($mbox,"{" . $server . "}" . $mailbox,SA_ALL);
  if($status && $GLOBALS['debug']) {
    print("\n$src_username - $mailbox status:\n");
    print("Messages:    ". $status->messages   )."\n";
    print("Recent:      ". $status->recent     )."\n";
    print("Unseen:      ". $status->unseen     )."\n";
    print("UIDnext:     ". $status->uidnext    )."\n";
    print("UIDvalidity: ". $status->uidvalidity)."\n\n";
  } 
  return $status;
}

//   0    1    2  3   4      5       6
// Date: Tue, 22 Feb 2000 11:05:55 -0500

// 0  1  2 
// 11:05:55

// int mktime (int hour, int minute, int second, int month, int day, int year [, int is_dst])
function getSentDate($mbox, $i) {
  $header = imap_fetchheader($mbox, $i);
  $lines = split("\n", $header);
  foreach ($lines as $line) {
    if (ereg("^Date:", $line)) {
      $dateline = $line;
    }
  }
  $dateparts = split(" ", $dateline);
  $timeline = $dateparts[5];
  $timeparts = split(":", $timeline);
  switch ($dateparts[3]) {
    case "Jan":
      $month = 1;
      break;
    case "Feb":
      $month = 2;
      break;
    case "Mar":
      $month = 3;
      break;
    case "Apr":
      $month = 4;
      break;
    case "May":
      $month = 5;
      break;
    case "Jun":
      $month = 6;
      break;
    case "Jul":
      $month = 7;
      break;
    case "Aug":
      $month = 8;
      break;
    case "Sep":
      $month = 9;
      break;
    case "Oct":
      $month = 10;
      break;
    case "Nov":
      $month = 11;
      break;
    case "Dec":
      $month = 12;
      break;
  }
  $date = mktime($timeparts[0], $timeparts[1], $timeparts[2], $month, $dateparts[2], $dateparts[4]);
  return $date;
}

// Archived24Jan2001
function getArchiveDateName($now) {
 return "Archived" . date("dMY"); 
}

// $mbox - connection to mail server
// $msg_no - the message no you are reading the flag of
// $flag - the flag you want to get
function isFlagSet($mbox, $msg_no, $flag) {
  $headerinfo = imap_headerinfo($mbox, $msg_no);
  switch ($flag) {
    case "Seen":
      $result = ($headerinfo->Unseen == 'U' || $headerinfo->Recent == 'N') ? false : true;
      break;
    case "Answered":
      $result = ($headerinfo->Answered == 'A') ? true : false; 
      break;
    case "Flagged":
      $result = ($headerinfo->Flagged == 'F') ? true : false;
      break;
    case "Deleted":
      $result = ($headerinfo->Deleted == 'D') ? true : false;
      break;
    case "Draft";
      $result = ($headerinfo->Draft == 'X') ? true : false;
      break;
    default:
      if ($debug) print "ERROR - Flag specified no defined in isFlagSet() function!";
      $result = false;
  }
  return $result;
}

// $mbox - connection to mail server
// $msg_no - the message no you are setting a flag for
// $flag - the flag you want to set
function setFlag($mbox, $msg_no, $flag) {
  //  The flags which you can set are 
  //   "\\Seen", "\\Answered", "\\Flagged", "\\Deleted", and "\\Draft" (as defined by RFC2060).
  global $debug;
  if (imap_setflag_full($mbox, $msg_no, $flag)) {
    if ($debug) print "$flag set on $mbox supposidly\n";
    return true;
  }
  else {
    return false;
  }
}

function mailgrationStartPoint($numMsg,$dir,$limit) {  
  return ($dir == OLD ? 1 : $numMsg - $limit + 1);
}

function getFlagsFromMsg($mbox,$msg_no) {
  $toReturn = '';
  if (isFlagSet($mbox, $msg_no, "Seen")) {
    $toReturn .= '\\Seen';  
  }
  if (isFlagSet($src_mbox, $msg_no, "Answered")) {
    $toReturn .= ' \\Answered';
  }
  if (isFlagSet($src_mbox, $msg_no, "Flagged")) {
    $toReturn .= ' \\Flagged';
  }
  if (isFlagSet($src_mbox, $msg_no, "Deleted")) {
    $toReturn .= ' \\Deleted';
  }
  if (isFlagSet($src_mbox, $msg_no, "Draft")) {
    $toReturn .= '\\Draft';
  }
  return $toReturn;  
}

?>
