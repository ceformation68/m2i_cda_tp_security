<?php 
	include("connect.php");

    // Recherche du nombre de connexions échouées en fonction de l'adresse ip
    $intFailedAttempts = $db->query("SELECT COUNT(*) AS fail_count FROM login_attempts WHERE attempt_ip = '".$_SERVER['REMOTE_ADDR']."' AND attempt_login_success = 0 AND attempt_login_time > (NOW() - INTERVAL 15 MINUTE);")->fetchColumn();
    // Si 5 tentatives ou plus, bloquer la connexion
    /*if ($intFailedAttempts >= 5) {
        echo '<div class="alert alert-danger" role="alert">Trop de tentatives de connexion échouées. Veuillez réessayer dans 15 minutes.</div>';
        return;
    }*/
    // Si 5 tentatives ou plus, ip en blacklist
    if ($intFailedAttempts >= 5) {
        $stmt = $db->prepare("INSERT INTO blacklist (blacklist_ip_address, blacklist_block_reason, blacklist_block_time) VALUES (:ip, 'Trop de tentatives de connexion échouées', NOW()) ON DUPLICATE KEY UPDATE blacklist_block_time = NOW();");
        $stmt->bindParam(':ip', $_SERVER['REMOTE_ADDR']);
        $stmt->execute();
        echo '<div class="alert alert-danger" role="alert">Trop de tentatives de connexion échouées. Votre adresse IP a été bloquée. Veuillez contacter un administrateur.</div>';
        return;
    }
    /** Ajouter à la base de données */
    /*  CREATE TABLE blacklist (
            blacklist_ip_address VARCHAR(50) NOT NULL,
            blacklist_block_reason VARCHAR(255) NOT NULL,
            blacklist_block_time DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (blacklist_ip_address)
        ) ENGINE=InnoDB;
    */

	$arrUser = false;
    // Vérification des identifiants
    $strUsername = trim($_POST['username'] ?? '');
    $strPassword = trim($_POST['password'] ?? '');

	if (isset($_POST['username'])){
        if ($strUsername === '' || $strPassword === '') {
            $strError = "Vous devez renseigner un nom d'utilisateur et un mot de passe.";
        } else {
            // Requête préparée pour éviter les injections SQL
            $stmt = $db->prepare("SELECT id, name, password FROM users WHERE login = :username");
            $stmt->bindParam(':username', $strUsername);
            $stmt->execute();
            $arrUser = $stmt->fetch();
            // Vérification du mot de passe haché
            if ($arrUser && password_verify($strPassword, $arrUser['password'])) {
                logLoginAttempt($db, $strUsername, true);
                $strMessage = "Bienvenue " . $arrUser['name'];
                unset($arrUser['password']); // Ne pas stocker le mot de passe en session
                $_SESSION['user'] = $arrUser;
            } else {
                logLoginAttempt($db, $strUsername, false);
                $arrUser = false; // Réinitialiser si échec
                $strError = "Nom d'utilisateur ou mot de passe incorrect.";
            }
        }
	}
?>
<div class="py-4">
        <?php
            if (isset($strError)){
                echo '<div class="alert alert-danger" role="alert">'.$strError.'</div>';
            }
            if (isset($strMessage)){
                echo '<div class="alert alert-success" role="alert">'.$strMessage.'</div>';
            }
        ?>
	<form action="#" method="post">
		<input type="hidden" name="page" value="<?php echo $strPage; ?>">
		<p>
			<label>Username:</label>
			<input class="form-control" type="text" name="username">
		</p>
		<p>
			<label>Password:</label>
			<input class="form-control" type="password" name="password" >
		</p>
		<p>
			<input class="form-control btn btn-primary" type="submit" value="Se connecter" name="login" >
		</p>
</div>

<?php
    // Stocker en BDD les tentatives de connexion
    function logLoginAttempt($db, $username, $success){
        $stmt = $db->prepare("INSERT INTO login_attempts (attempt_username, attempt_login_time, attempt_ip, attempt_login_success) 
                                VALUES (:username, NOW(), :ip, :success)");
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':success', $success, PDO::PARAM_BOOL);
        $stmt->bindParam(':ip', $_SERVER['REMOTE_ADDR']);
        $stmt->execute();
    }
        /** Ajouter à la base de données */
        /*
        CREATE TABLE login_attempts (
            attempt_id INT NOT NULL AUTO_INCREMENT,
            attempt_username VARCHAR(255) NOT NULL,
            attempt_login_time DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            attempt_ip VARCHAR(45) NOT NULL,
            attempt_login_success BOOLEAN NOT NULL,
            PRIMARY KEY (attempt_id)
        ) ENGINE=InnoDB;
        */

