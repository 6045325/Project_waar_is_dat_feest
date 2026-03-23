<?php

spl_autoload_register(function ($class) {

    $directories = [
        __DIR__ . "/classes/",
        __DIR__ . "/api/",
    ];

    foreach ($directories as $dir) {
        $file = $dir . $class . ".php";
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});
