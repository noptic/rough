<?php
namespace daliaIT\rough;
use Composer\Autoload\ClassLoader;
class ComposerClassFileFinder extends ClassFileFinder
{
    protected
        $loader;
        
    public function __construct(ClassLoader $loader){
        $this->loader = $loader;
    }
    
    public function getClassFileName($class){
        return $this->loader->findFile($class);
    }
}