<html>
<head>
<script type="text/javascript" src="js/jquery-1.9.1.min.js"></script>
<script type="text/javascript" src="js/mailgration.js"></script>
<title>Assistente de Migra��o de E-mail</title>
</head>
<body>
<h1>Assistente de Migra��o de E-mail</h1>
<p>Lorem Ipsum Dolor Sit Amet</p>
<form action="archivesetup.php" method="POST" id="mailgration-form">
<p class="origem">Origem:
<br />
Endere�o do Servidor (sem Portas ou especifica��es de protocolos):
<input type="text" size="60" name="src_server" /><br />
Usu�rio de E-mail: <input type="text" size="60" name="src_username" /><br />
Senha: <input type="password" size="20" name="src_password" /><br />
</p>
<input type="button" id="load-inbox" name="load-inbox" value="Carregar Inboxes" />
<p class="select-inbox">
<select name="inboxes" id="inboxes-select" multiple="multiple"></select>
</p>
<p class="destino">
Destino:
<br />
Endere�o do Servidor (sem Portas ou especifica��es de protocolos):
<input type="text" size="60" name="dest_server" /><br />
Usu�rio de E-mail: <input type="text" size="60" name="dest_username" /><br />
Senha: <input type="password" size="20" name="dest_password" /><br />
</p> 

<input type="submit" value="Migrar" />
</form>



</body>

</html>