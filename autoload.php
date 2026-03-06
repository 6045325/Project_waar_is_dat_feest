<?php

spl_autoload_register(function ($class) {

    $directories = [
        __DIR__ . "/Core/",
        __DIR__ . "/Characters/",
        __DIR__ . "/UI/"
    ];

    foreach ($directories as $dir) {
        $file = $dir . $class . ".php";
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});