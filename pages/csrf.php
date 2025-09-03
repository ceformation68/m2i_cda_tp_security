<?php
	$strPreco 	= "	<ul>
						<li>Mise en place d'un jeton</li>
						<li>Demander le mot de passe actuel pour vérification</li>
					</ul>";
	$strDesc	= "La faille CSRF est une attaque dans laquelle un attaquant exploite la confiance entre un utilisateur et un site web. L'attaque consiste à envoyer une requête HTTP depuis le navigateur de la victime vers un site web tiers, sans que la victime en soit consciente. Cette requête est souvent une action malveillante, telle que supprimer des données, effectuer un achat ou changer un mot de passe.";
	$strTip		= "Changer les données dans l'url ou utiliser Burp Suite";
?>


<div class="col-md-8">
	<h2>CSRF</h2>
	<?php
		include("_partial/desc.php");
		include("connect.php");
        // Token CSRF en session
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        // Changement de mot de passe
		if (isset($_POST['password_new'])){
			if ($_POST['password_new'] == ''){
                $strError = "Vous devez renseigner un mot de passe";
            }else if (!preg_match("/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{16,}$/", $_POST['password_new'])){
                $strError = "Le mot de passe doit contenir :
                <ul>
                    <li>Au moins 16 caractères</li>
                    <li>Au moins une lettre majuscule</li>
                    <li>Au moins une lettre minuscule</li>
                    <li>Au moins un chiffre</li>
                    <li>Au moins un caractère spécial</li>
                </ul>";
            }else if ($_POST['password_old'] == ''){
                $strError = "Vous devez renseigner votre mot de passe actuel";
			}else if ($_POST['password_new'] != $_POST['password_conf']){
                $strError = "Le mot de passe et sa confirmation ne correspondent pas";
			}else{
                $boolOk = true;
                // Vérification du token CSRF
                if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                    $boolOk = false;
                }else{
                    // Vérification du mot de passe actuel
                    $stmt = $db->prepare("SELECT password FROM users WHERE id = 1");
                    $stmt->execute();
                    $row = $stmt->fetch();
                    if (!password_verify($_POST['password_old'], $row['password'])) {
                        $boolOk = false;
                    }else {
                        // Changement du mot de passe
                        //$db->exec("UPDATE users SET password = '".$_GET['password_new']."' WHERE id = 1");
                        // Changement du mot de passe avec requête préparée + hashage
                        $stmt = $db->prepare("UPDATE users SET password = :password WHERE id = 1");
                        $hashedPassword = password_hash($_POST['password_new'], PASSWORD_DEFAULT);
                        $stmt->bindParam(':password', $hashedPassword);
                        $stmt->execute();
                        // Message pour l'utilisateur
                        $strMessage = "Mot de passe changé avec succès !";
                        unset($_SESSION['csrf_token']); // Invalider le token après utilisation
                    }
                }
                if (!$boolOk){
                    $strError = "Erreur veuillez réessayer.";
                }
			}
		}
        if (isset($strError)){
            echo '<div class="alert alert-danger" role="alert">'.$strError.'</div>';
        }
        if (isset($strMessage)){
            echo '<div class="alert alert-success" role="alert">'.$strMessage.'</div>';
        }
    ?>
	<form action="#" method="post" >
		<input type="hidden" name="page" value="csrf">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <p>
            <label for="oldpass">Mot de passe actuel:</label>
            <input id="oldpass" class="form-control" type="password" name="password_old">
        </p>
        <p>
            <label for="pass">Nouveau mot de passe :</label>
            <input id="pass" class="form-control" type="password" name="password_new">
            <span class="small">Au moins : 16 caractères, une lettre majuscule, une lettre minuscule, un chiffre, un caractère spécial</span>
        </p>
		<p>
			<label for="passconf">Confirmer le mot de passe :</label>
			<input id="passconf" class="form-control" type="password" name="password_conf" >
		</p>
		<p>
			<input class="form-control btn btn-primary" type="submit" value="changer">
		</p>
	</form>
	
	<?php
		include ("_partial/soluce.php"); 
	?>	
</div>