<?php
/*/
type:       class
subtype:    macro
author:     Oliver Anan <oliver@ananit.de>
tags:       [macro, import]

ImportMacro
================================================================================
Imports the body of a class.

Syntax
--------------------------------------------------------------------------------

    import <target>
    
target
:   The class to import.
:    Single word
:    Required argument.
    

     
Examples
--------------------------------------------------------------------------------
    
Source
--------------------------------------------------------------------------------
/*/
namespace daliaIT\rough\macro;
use ReflectionClass,
    daliaIT\rough\ClassFileFinder;

class ImportMacro{
    protected static
        $classFileFinder;
        
    protected
        $stripTokens = array(T_COMMENT,T_DOC_COMMENT);
    
    public function __invoke($args,$parser){     
        $code = $parser->replace( 
            file_get_contents( 
                static::getClassFileFinder()->getClassFileName($args[0])
            )
        );
        return $this->extractBody($code);
    }
        
    public function extractBody($sourcecode){
        $tokens = token_get_all($sourcecode);
        $code = '';
        do{
            $token = array_shift($tokens);
        }
        while( $token[0] !== 353 && $tokens);
        do{
            $token = array_shift($tokens);
            if( is_string($token) ){
                $token = array(null,$token);
            }
        }
        while( ($token[1] !== '{') && $tokens );      
        $nesting = 1;
        while($tokens)
        {
            $token = array_shift($tokens);
            if( is_string($token) ){
                $token = array(null,$token);
            }
            if($token[1] === '}' && --$nesting == 0){ 
                break;
            } elseif( $token[1] === '{' ){
                $nesting++;
            }
            $code .= $token[1];
        }
        return $code;
    }
    
   
    
    #:ClassFileFinder
    public static function getClassFileFinder(){
        return static::$classFileFinder;
    }
    
    #:void
    public static function setClassFileFinder(ClassFileFinder $value){
        static::$classFileFinder = $value;
    }
    
    #@access public public stripTokens array#
    
    #:array
    public function getStripTokens(){
        return $this->stripTokens;
    }
    
    #:this
    public function setStripTokens(array $value){
        $this->stripTokens = $value;
        return $this;
    }
    #@#
}