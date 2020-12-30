<?php
/**
 * This file is used to load the Composer autoloader if required.
 */

use Amply\Email;

// Define path/existence of Composer autoloader
$composerAutoloadFile = __DIR__ . '/vendor/autoload.php';
$composerAutoloadFileExists = (is_file($composerAutoloadFile));

// Can't locate Amply\Mail class?
if (!class_exists(Email::class)) {
    // if the user is not using composer, register an autoload function to load based on relative directory structure
    if (!$composerAutoloadFileExists) {
        require_once( __DIR__ . '/lib/Amply.php');

        // autoload based on directory structure & without composer
        spl_autoload_register(function($class){
            if (substr($class, 0, 6) == 'Amply\\'){
                $path = str_replace('\\', '/', $class);

                $pieces = explode('/', $path);
                array_shift($pieces); // we don't use the first piece (Amply)
                $file = array_pop($pieces);

                $dirPath = __DIR__ . '/lib/' . strtolower(implode('/', $pieces));
                $filePath = $dirPath . '/' . $file . '.php';

                if (file_exists($filePath) ) {
                    require_once($filePath);
                }

                if (!class_exists($class)) {
                    error_log("Error finding Amply class [$class] (expected [$filePath]). Please review your autoloading configuration.");
                }
            }
        });
    } else {
        // Load Composer autoloader
        require_once $composerAutoloadFile;

        // If desired class still not existing
        if (!class_exists(Email::class)) {
            // Suggest to review the Composer autoloader settings
            error_log("Error finding Amply classes. Please review your autoloading configuration.");
        }
    }
}
