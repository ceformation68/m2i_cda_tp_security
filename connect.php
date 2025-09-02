<?php
	//$db = new PDO('mysql:host=localhost;dbname=phpsec', 'root', '');
    /* Avec le fichier config.php
    $host       = $config['database']['host'];
    $dbname     = $config['database']['dbname'];
    $user       = $config['database']['user'];
    $password   = $config['database']['password'];
    */
    /* Avec le fichier .env
    $host       = $_ENV['DB_HOST'];
    $dbname     = $_ENV['DB_NAME'];
    $user       = $_ENV['DB_USER'];
    $password   = $_ENV['DB_PASSWORD'];
    */
    // Avec le fichier .env et la classe EnvParser
    $host       = getenv('DB_HOST');
    $dbname     = getenv('DB_NAME');
    $user       = getenv('DB_USER');
    $password   = getenv('DB_PASSWORD');

    try{
        $db = new PDO('mysql:host='.$host.';dbname='.$dbname, $user, $password);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $db->exec("SET CHARACTER SET utf8");
    } catch(PDOException $e) {
        echo "Échec : " . $e->getMessage();
    }