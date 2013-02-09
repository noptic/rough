<?php
namespace daliaIT\rough;
class ClassFileFinder{
    public function getClassFileName($class){
        $reflect = new ReflectionClass($class);
        return $reflect->getFileName();
    }
}