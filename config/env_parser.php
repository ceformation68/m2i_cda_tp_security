<?php
class EnvParser {
    public static function load($filePath) {
        if (!file_exists($filePath)) {
            throw new Exception("Fichier .env introuvable : $filePath");
        }

        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $lineNumber => $line) {
            $line = trim($line);

            // Ignorer les lignes vides et commentaires
            if (empty($line) || $line[0] === '#') {
                continue;
            }

            // Vérifier le format clé=valeur
            if (strpos($line, '=') === false) {
                continue;
            }

            list($key, $value) = explode('=', $line, 2);

            $key = trim($key);
            $value = self::parseValue(trim($value));

            // Valider le nom de la clé
            if (!self::isValidKey($key)) {
                throw new Exception("Nom de clé invalide ligne " . ($lineNumber + 1) . ": $key");
            }

            // Ne pas écraser les variables déjà définies
            if (getenv($key) === false) {
                $_ENV[$key] = $value;
                putenv("$key=$value");
            }
        }
    }

    private static function parseValue($value) {
        // Supprimer les guillemets
        if ((substr($value, 0, 1) === '"' && substr($value, -1) === '"') ||
            (substr($value, 0, 1) === "'" && substr($value, -1) === "'")) {
            $value = substr($value, 1, -1);
        }

        // Gérer les échappements dans les guillemets doubles
        $value = str_replace(['\\n', '\\r', '\\t'], ["\n", "\r", "\t"], $value);

        // Substitution de variables ${VAR_NAME}
        $value = preg_replace_callback('/\$\{([A-Z_][A-Z0-9_]*)\}/', function($matches) {
            return getenv($matches[1]) ?: '';
        }, $value);

        return $value;
    }

    private static function isValidKey($key) {
        return preg_match('/^[A-Z_][A-Z0-9_]*$/', $key);
    }

    public static function get($key, $default = null) {
        $value = getenv($key);
        return $value !== false ? $value : $default;
    }
}
