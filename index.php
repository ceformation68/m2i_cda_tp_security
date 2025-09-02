<?php
    // Charger la configuration
    //require_once('config/config.php');

    // Charger les variables d'environnement depuis le fichier .env
    //require_once('config/load_env.php');
    //loadEnv(__DIR__.'/.env');

    // Charger les variables d'environnement avec la classe EnvParser
    require_once('config/env_parser.php');
    EnvParser::load(__DIR__.'/.env');

	$strPage = $_GET['page']??'content';
	if (!file_exists("pages/".$strPage.".php")){
		header("Location:index.php");
	}
?>

<?php include ("_partial/head.php"); ?>

<main class="container">

	<?php include ("_partial/header.php"); ?>  
	<!-- Content -->
	<div class="row g-5 py-3">
	<?php 
		include ("_partial/col.php"); 
		include ("pages/".$strPage.".php");
	?> 
	</div>

</main>

<?php include ("_partial/footer.php"); ?>  