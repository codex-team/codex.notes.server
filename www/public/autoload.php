<?php

/**
 * Case insensitive modules autoloader.
 * Load classes (ex: App\Components\Index\Index) from related files (ex: components/index/Index.php)
 * Remove namespace prefix (App) and lowercase all path parts except the last one (Index).
 */
spl_autoload_register(function ($classname) {
    $pathParts = explode('\\', $classname);
    $pathToFile = PROJECTROOT;

    $namespace = array_shift($pathParts);

    // if import is a file without namespace – load it directly
    if (!$pathParts) {
        $pathToFile = PROJECTROOT . $namespace;
    }

    for ($i = 0; $i < count($pathParts); $i++) {
        $pathToFile .= ($i + 1 == count($pathParts)) ? $pathParts[$i] . '.php' : strtolower($pathParts[$i]) . '/';
    }

    if (file_exists($pathToFile)) {
        require_once $pathToFile;
    }
});