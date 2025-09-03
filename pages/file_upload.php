<?php
	$strPreco 	= "<ul>
						<li>Autoriser uniquement les types de fichier nécessaires</li>
						<li>Vérifier les informations du fichier</li>
						<li>Renommer le fichier</li>
					</ul>";
	$strDesc	= "Les attaquants peuvent utiliser des fichiers malveillants qui ont une extension différente de celle autorisée pour contourner les contrôles de sécurité. Si la taille du fichier est trop grande, cela peut entraîner une surcharge du serveur et un déni de service (DoS). Les attaquants peuvent également télécharger des fichiers volumineux pour occuper l'espace de stockage du serveur et empêcher le téléchargement de fichiers légitimes. Les fichiers téléchargés peuvent contenir des virus ou des logiciels malveillants qui peuvent infecter le serveur et compromettre la sécurité de l'ensemble du système.";
	$strTip		= "Ajouter un fichier malveillant...";
?>

<div class="col-md-8 position-relative">
	<h2>Traitement des fichiers</h2>
	<?php
		include("_partial/desc.php");
		if (count($_FILES) >0){
            // Vérification de l'image
            $arrMyfile = $_FILES['myFile'];

            if($arrMyfile['error'] > 0){
                $strError = "Erreur lors du transfert";
            }else{
                // Vérifier la taille du fichier (max 500Ko)
                if ($arrMyfile['size'] > 500000) {
                    $strError = "Le fichier est trop gros (max 500Ko)";
                }else{
                    $allowedTypes   = ['image/jpeg', 'image/png'];
                    $fileType       = $arrMyfile['type'];
                    // Vérifier le type MIME
                    if (!in_array($fileType, $allowedTypes)) {
                        $strError = "Type de fichier non autorisé. Seuls les fichiers JPEG, PNG sont autorisés.";
                    }else{
                        // Renommer le fichier avec la date et un id unique
                        $objDate = new DateTime();
                        $newFileName = $objDate->format('Ymd_His_') . uniqid() . '.' . pathinfo($arrMyfile['name'], PATHINFO_EXTENSION);
                        $uploaddir = './uploads/';
                        $uploadfile = $uploaddir . $newFileName;
                        // Déplacer le fichier dans le dossier uploads - sans redimensionnement
                        /*if (move_uploaded_file($arrMyfile['tmp_name'], $uploadfile)) {
                            $strMessage = "Le fichier a bien été uploadé";
                        } else {
                            $strError = "Erreur d'upload du fichier";
                        }*/
                        // Redimensionner l'image en gardant le ratio + enregistrement dans le dossier uploads
                        $imgSrc = imagecreatefromjpeg($arrMyfile['tmp_name']);
                        $imgSrc = imagescale($imgSrc, 100, -1); // Largeur max 100px
                        imagejpeg($imgSrc, $uploadfile);
                    }
                }
            }
		}
	?>
    <?php
        if (isset($strError)){
            echo '<div class="alert alert-danger" role="alert">'.$strError.'</div>';
        }
        if (isset($strMessage)){
            echo '<div class="alert alert-success" role="alert">'.$strMessage.'</div>';
        }
    ?>
	<form enctype="multipart/form-data" action="index.php?page=file_upload" method="POST">
		<p>
			<input class="form-control" type="file" name="myFile">
		</p>
		<p>
			<input class="form-control btn btn-primary" type="submit" name="upload" value="Envoyer">
		</p>
	</form>
	<?php
		$scandir = scandir("./uploads");
		echo "<ul>";
		foreach($scandir as $fichier){
			if ($fichier != "." && $fichier != ".."){
	?>
			<li><a href="uploads/<?php echo $fichier; ?>" ><?php echo $fichier; ?></a></li>
	<?php
			}
		}
		echo "</ul>";
	
		include ("_partial/soluce.php"); 
	?>
</div>