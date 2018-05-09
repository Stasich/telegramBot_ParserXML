<?php
/**
 * Created by PhpStorm.
 * User: stasich
 * Date: 07.05.18
 * Time: 1:31
 */

spl_autoload_register(function($className) {
    $className = str_replace('\\', '/', $className);
    require_once __DIR__ . "/src/$className" . '.php';
});
