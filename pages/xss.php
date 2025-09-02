<?php
	$strPreco 	= "	<ul>
						<li>Nettoyage des données saisies</li>
						<li>Utilisation de requêtes préparées</li>
					</ul>";
	$strDesc	= "La faille XSS est une vulnérabilité de sécurité qui se produit lorsqu'un site web stocke de manière persistante des données d'utilisateur qui contiennent du code malveillant qui sera ensuite affiché sur les pages web pour les utilisateurs légitimes.";
	$strTip		= "Insérer du script dans le formulaire : <script>alert(\"coucou\");</script>";

	include("connect.php");

    // Nettoyage des données
    $strName    = filter_var($_POST['name'] ?? "", FILTER_SANITIZE_SPECIAL_CHARS);
    $strComment = filter_var($_POST['message'] ?? "", FILTER_SANITIZE_SPECIAL_CHARS);

    // Insertion du commentaire
	if ($strName != "" && $strComment != ""){
        // Utilisation de requêtes préparées pour éviter les injections SQL
        $stmt = $db->prepare("INSERT INTO comments (name, comment, publish) VALUES (:name, :comment, 0)");
        $stmt->bindParam(':name', $strName);
        $stmt->bindParam(':comment', $strComment);
        $stmt->execute();
        // Message pour l'utilisateur
        $strMessage = "Merci ".$strName." pour votre commentaire, il sera publié après validation par un administrateur.";
        //$db->exec("INSERT INTO comments (name, comment) VALUES ('".$_GET['name']."', '".$_GET['message']."')");
	}

	$arrComments = array();
	$arrComments = $db->query('SELECT * FROM comments WHERE publish = 1')->fetchAll();
?>
<div class="col-md-8">
	<h2>Cross-Script Scripting (XSS)</h2>
	<?php
		include("_partial/desc.php");
	?>
	<div class="py-4">
        <?php
            if (isset($strMessage)){
                echo '<div class="alert alert-success" role="alert">'.$strMessage.'</div>';
            }
        ?>
		<form name="guestform" method="post">
			<input type="hidden" name="page" value="xss">
			<p>
				<label for="name">Nom *</label>
				<input id="name" required class="form-control" name="name" type="text" size="30" maxlength="10" >
			</p>
			<p>
				<label for="comments">Commentaire *</label>
				<textarea id="comments" required class="form-control" name="message" cols="50" rows="3" maxlength="50" ></textarea>
			</p>
			<p>
				<input class="form-control btn btn-primary" name="btnSign" type="submit" value="Envoyer" >
			</p>
		</form>
	</div>
	
	<div id="comments">
		<?php 
			foreach ($arrComments as $arrDet){
		?>
		<div class="card mb-4">
            <div class="card-body">
                <p><?php echo $arrDet['comment']; ?></p>
				<div class="d-flex justify-content-between">
					<div class="d-flex flex-row align-items-center">
						<p class="small mb-0 ms-2"><?php echo $arrDet['name']; ?></p>
					</div>
				</div>
			</div>
		</div>
		<?php 
			}
		?>
	</div>
	<?php
		include ("_partial/soluce.php"); 
	?>
</div>