<?php
    function loadEnv($filePath) {
        if (!file_exists($filePath)) {
            throw new Exception("Le fichier .env n'existe pas : $filePath");
        }

        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            // Ignorer les commentaires
            if (strpos(trim($line), '#') === 0) {
                continue;
            }

            // Séparer la clé et la valeur
            if (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);

                $key = trim($key);
                $value = trim($value);

                // Supprimer les guillemets si présents
                $value = trim($value, '"\'');

                // Définir la variable d'environnement
                $_ENV[$key] = $value;
                putenv("$key=$value");
            }
        }
    }