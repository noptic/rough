<?php
namespace daliaIT\rough;
use OutOfBoundsException;

class ListClassFileFinder extends ClassFileFinder{
    protected
    #:array
        $files = array();
        
    public function getClassFileName($class){
        if(! isset( $this->files[$class]) ){
            throw new OutOfBoundsException(
                "could not find file for class '$class'"
            );
        }
        return $this->files[$class];
    }
    
    #@access public public files array#
    
    #:array
    public function getFiles(){
        return $this->files;
    }
    
    #:this
    public function setFiles(array $value){
        $this->files = $value;
        return $this;
    }
    #@#
}