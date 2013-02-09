<?php
foreach( array('/../../autoload.php','/vendor/autoload.php') as $autoloader){
    if( file_exists(__DIR__.$autoloader) ){ 
        return require __DIR__.$autoloader; 
    }
}