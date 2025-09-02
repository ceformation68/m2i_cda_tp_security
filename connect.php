<?php
	//$db = new PDO('mysql:host=localhost;dbname=phpsec', 'root', '');
    $host       = $config['database']['host'];
    $dbname     = $config['database']['dbname'];
    $user       = $config['database']['user'];
    $password   = $config['database']['password'];

    try{
        $db = new PDO('mysql:host='.$host.';dbname='.$dbname, $user, $password);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $db->exec("SET CHARACTER SET utf8");
    } catch(PDOException $e) {
        echo "Échec : " . $e->getMessage();
    }
?>