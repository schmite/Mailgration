<?php

/*
 * mailref.php
 *
 *  This file contains the settings of many webmail services to avoid the need of fetching such data by the user.
 *  It's required in index.php so the select options can be loaded from it and in getinbox.php and archivesetup.php,
 *  where its values are used to assemble the proper connection string. 
 *
 *  $mailRef array holds a series of arrays with the following attributes:
 *  - display_name: Text to be output in the select's option, human-readable and easily recognizable
 *  - address: The server IMAP address to import e-mails from
 *  - port: The port in which connection will be established with the server. Usually 143 for non-secure connections and
 *          993 for secure connections
 *  - flags: Array of flags to be passed to PHP's imap_open function. It's usually enough to provide 'ssl' for secure
 *           connections and 'novalidate-cert' for non-secure. More information and reference can be found on:
 *           http://php.net/manual/en/function.imap-open.php 
 */

$mailRef = array();

$mailRef['gmail'] = array(
  'display_name' => 'Gmail',
  'address' => 'imap.gmail.com',
  'port' => 993,
  'flags' => array('ssl')  
);

$mailRef['uol'] = array(
  'display_name' => 'UOL',
  'address' => 'imap.uol.com.br',
  'port' => 993,
  'flags' => array('ssl')
);

$mailRef['aol'] = array(
  'display_name' => 'AOL',
  'address' => 'imap.aol.com',
  'port' => 993,
  'flags' => array('ssl')
);

$mailRef['bol'] = array(
  'display_name' => 'BOL',
  'address' => 'imap.bol.com.br',
  'port' => 993,
  'flags' => array('ssl')
);

$mailRef['brturbo'] = array(
  'display_name' => 'BrTurbo',
  'imap' => 'imap.brturbo.com.br',
  'port' => 993,
  'flags' => array('ssl')
);

$mailRef['globo'] = array(
  'display_name' => 'Globo.com',
  'imap' => 'imap.globo.com',
  'port' => 993,
  'flags' => array('ssl')
);

$mailRef['ibest'] = array(
  'display_name' => 'iBest',
  'imap' => 'imap.ibest.com.br',
  'port' => 993,
  'flags' => array('ssl')  
);

$mailRef['ig'] = array(
  'display_name' => 'IG',
  'imap' => 'imap.ig.com.br',
  'port' => 993,
  'flags' => array('ssl')  
);

$mailRef['terra'] = array(
  'display_name' => 'Terra',
  'imap' => 'imap.ig.com.br',
  'port' => 993,
  'flags' => array('ssl')  
);

$mailRef['yahoo'] = array(
  'display_name' => 'Terra',
  'imap' => 'imap.mail.yahoo.com',
  'port' => 993,
  'flags' => array('ssl')
)

?>