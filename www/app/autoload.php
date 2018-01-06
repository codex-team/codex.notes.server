<?php

spl_autoload_register(function ($classname) {
    $pathParts = explode('\\', $classname);
    $pathToFile = PROJECTROOT . '/';
    for ($i = 1; $i < count($pathParts); $i++) {
        $pathToFile .= ($i + 1 == count($pathParts)) ? $pathParts[$i] . '.php' : strtolower($pathParts[$i]) . '/';
    }
    if (count($pathParts) > 1 && file_exists($pathToFile)) {
        require_once $pathToFile;
    }
});