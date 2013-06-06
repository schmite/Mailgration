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
<p class="origem">Origem:
<br />
Endereço do Servidor (sem Portas ou especificações de protocolos):
<input type="text" size="60" name="src_server" id="src_server" /><br />
Usuário de E-mail: <input type="text" size="60" name="src_username" id="src_username" /><br />
Senha: <input type="password" size="20" name="src_password" id="src_password" /><br />
</p>
<input type="button" id="load-inbox" name="load-inbox" value="Carregar Inboxes" />
<p class="select-inbox">
<select name="inboxes" id="inboxes-select" multiple="multiple"></select>
</p>
<p class="destino">
Destino:
<br />
Endereço do Servidor (sem Portas ou especificações de protocolos):
<input type="text" size="60" name="dest_server" /><br />
Usuário de E-mail: <input type="text" size="60" name="dest_username" /><br />
Senha: <input type="password" size="20" name="dest_password" /><br />
<br /><input type="submit" value="Migrar" />
</p>
</form>



</body>

</html>