<?php
require_once('inc.connect.php');
?>

<DOCTYPE html>
<html>
	<head>
		<title>Consultas Médicas</title>
		<meta charset = 'utf-8'>
		<link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
		<link rel="stylesheet" type="text/css" href="css/bootstrap-theme.min.css">
		<link rel="stylesheet" href="css/style.css">
	   	<link rel="shortcut icon" href="./img/fav.png" sizes="32x32">
		<meta name="viewport" content="width=device-width, initial-scale=1">
	</head>
	<body>
	<header>
	    <nav class="navbar navbar-inverse">
	        <div class="container">
	            <div class="navbar-header">
					<a href="index.php?pg=home" alt="Consultas Médicas - Home" title="Home"><img id="logo" src="img/fav.png" width=10%></a>
				<button type="button" style="margin-top:25px" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#menu_lista" aria-expanded="false">
				<span style="background-color:#4CDBC4" class="icon-bar"></span>
				<span style="background-color:#4CDBC4" class="icon-bar"></span>
				<span style="background-color:#4CDBC4" class="icon-bar"></span>
				</button>
				</div> 
	           <div class="collapse navbar-collapse" id="menu_lista">
				<ul class="nav navbar-nav navbar-right">
					<li class="link_menu"><a href="index.php?pg=home"> Home </a></li>
					<li class="link_menu"><a href="index.php?pg=consulta"> Cadastrar Consulta</a></li>
					<li class="link_menu"><a href="index.php?pg=paciente"> Cadastrar Paciente</a></li>
					<li class="link_menu"><a href="index.php?pg=especialidade"> Cadastrar Especialidade</a></li>
					<li class="link_menu"><a href="index.php?pg=medico"> Cadastrar Médico</a></li>
				</ul>
			</div>	
		</div>
	    </nav>
	</header>

	<div style="min-height:70%; ">
	    <div class="container-fluid" margin="100px" >
	        <div class="row" style="background-color:#fff">

	        <?php
			if( isset($_GET['pg']) and !empty($_GET['pg'])){
				$pag = $_GET['pg'];
			}else{ 
				$pag = 'home';
			}

			if( isset($_GET['msg']) and !empty($_GET['msg'])){
				$msg = $_GET['msg'];
			}else{
				$msg = 'home';
			}

			if($msg=='cadastrado'){
				echo '<h2 class="bg-success">Cadastrado com sucesso!</h2>';
			}elseif($msg=='deletado'){
				echo '<h2 class="bg-success">Deletado com sucesso!</h2>';
			}elseif($msg=='alterado'){
				echo '<h2 class="bg-success">Alterado com sucesso!</h2>';
			}elseif ($msg=='erro') {
				echo '<h2 class="bg-danger">Erro ao executar comando :(</h2>';
			}
			
			include_once('inc.'.$pag.'.php');
			?>
	       </div>
	    </div>
	</div>	

	<footer>
	    <div class="container">
		<div class="row">
			<div class="col-md-2"><a href="#"><img id="logo" src="img/fav.png" width="20%"></a></div>
			<div class="col-md-2 lista_footer"><a href="index.php?pg=home"> Home </a></div>
			<div class="col-md-2 lista_footer"><a href="index.php?pg=consulta"> Cadastrar Consulta</a></div>
			<div class="col-md-2 lista_footer"><a href="index.php?pg=paciente"> Cadastrar Paciente</a></div>
			<div class="col-md-2 lista_footer"><a href="index.php?pg=especialidade"> Cadastrar Especialidade</a></div>
			<div class="col-md-2 lista_footer"><a href="index.php?pg=medico"> Cadastrar Médico</a></div>
		</div>

		<div class="row">
			<div class="col-md-12">
				<p>© 2023 Banco de dados II</p>
			</div>
		</div>
	</div>
		<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
		<script src="js/bootstrap.min.js" type="text/javascript"></script>
	</footer>
	</body>
</html>