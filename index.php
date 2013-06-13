<?php
if(!function_exists('imap_open')) {
  exit('<h1>This Application CANNOT run because its missing MODULE php_imap.dll on Windows or php_imap.so on Linux</h1><br />Read more: <a href="http://php.net/manual/pt_BR/function.imap-open.php">http://php.net/manual/pt_BR/function.imap-open.php</a>');
}
?><html>
<head>
<script type="text/javascript" src="js/jquery-1.9.1.min.js"></script>
<script type="text/javascript" src="js/mailgration.js"></script>
<link rel="stylesheet" href="css/style.css" />
<title>Assistente de Migração de E-mail</title>
</head>
<body>
<h1>Assistente de Migração de E-mail</h1>
<p>Lorem Ipsum Dolor Sit Amet</p>
<form action="archivesetup.php" method="POST" id="mailgration-form">
<div class="origem">Origem:
<br />
Servidor de Origem:
<select id="src_server" name="src_server">
<option value="0">- Escolher -</option>
<?php
  include('mailref.php');
  foreach($mailRef as $key => $val) { ?>
    <option value="<?php echo $key; ?>"><?php echo $val['display_name']; ?></option>
    <?php
  }
?>
<option value="other">Outro</option>
</select>
<div id="src_server_info">
Endereço Imap: <input type="text" size="60" name="src_server_name" id="src_server_name" /><br />
Porta: <input type="text" size="60" name="src_server_port" id="src_server_port" /><br />
* Padrão: 143<br />
Protocolo de Segurança: 
<select id="src_server_security_protocol" name="src_server_security_protocol[]" multiple="true">
<option value="norsh">norsh</option>
<option value="ssl">ssl</option>
<option value="validate-cert">validate-cert</option>
<option value="novalidate-cert">novalidate-cert</option>
<option value="tls">tls</option>
<option value="notls">notls</option>
<option value="readonly">readonly</option>
</select> <br />
* Caso você não tenha certeza, selecione APENAS a opção 'novalidate-cert'
</div><br />
Usuário de E-mail: <input type="text" size="60" name="src_server_username" id="src_username" /><br />
Senha: <input type="password" size="20" name="src_server_password" id="src_password" /><br />
</div>
<input type="button" id="load-inbox" name="load-inbox" value="Carregar Inboxes" />
<p class="select-inbox">
<select name="inboxes[]" id="inboxes-select" multiple></select>
</p>
<p class="inbox-limit"></p>
<div class="destino">Destino:
<br />
Servidor de Destino:
<select id="dest_server" name="dest_server">
<option value="0">- Escolher -</option>
<?php
  include('mailref.php');
  foreach($mailRef as $key => $val) { ?>
    <option value="<?php echo $key; ?>"><?php echo $val['display_name']; ?></option>
    <?php
  }
?>
<option value="other">Outro</option>
</select>
<div id="dest_server_info">
Endereço Imap: <input type="text" size="60" name="dest_server_name" id="dest_server_name" /><br />
Porta: <input type="text" size="60" name="dest_server_port" id="dest_server_port" /><br />
* Padrão: 143<br />
Protocolo de Segurança: 
<select id="dest_server_security_protocol" name="dest_server_security_protocol[]" multiple="true">
<option value="norsh">norsh</option>
<option value="ssl">ssl</option>
<option value="validate-cert">validate-cert</option>
<option value="novalidate-cert">novalidate-cert</option>
<option value="tls">tls</option>
<option value="notls">notls</option>
<option value="readonly">readonly</option>
</select> <br />
* Caso você não tenha certeza, selecione APENAS a opção 'novalidate-cert'
</div><br />
Usuário de E-mail: <input type="text" size="60" name="dest_server_username" id="dest_username" /><br />
Senha: <input type="password" size="20" name="dest_server_password" id="dest_password" /><br />
</div>
<p>
<input type="checkbox" name="delete_src_msg" />Apagar as Mensagens do Servidor<br />
<input type="submit" value="Migrar" />
</p>
</form>



</body>

</html>
