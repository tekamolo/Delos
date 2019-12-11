<?php

spl_autoload_register(function($class){
    $subStringClass = strstr($class, '\\');
    if($subStringClass){
        $file = str_replace("\\","/",$subStringClass);
        $file = PROJECT_ROOT_PATH."/framework".$file.".php";
        if(file_exists($file)){
            require_once $file;
        }
    }
});