<?php
/*/
type:       class
author:     Oliver Anan <oliver@ananit.de>
tags:       [macro, import]

ImportMacro
================================================================================
Imports the body of a class.

Syntax
--------------------------------------------------------------------------------

    import <target>
    
target
:   The visibility of the getter method.
:    Single word, or list.
:    Required argument.
    

     
Examples
--------------------------------------------------------------------------------
    
Source
--------------------------------------------------------------------------------
/*/
namespace daliaIT\rough;
use ReflectionClass;

class ImportMacro{
    protected
        $stripTokens = array(T_COMMENT,T_DOC_COMMENT),
        $classFileFinder;
        
    public function __construct(ClassFileFinder $finder=null){
        if(! $finder){
            $finder = new ClassFileFinder();
        }
        $this->classFileFinder = $finder;
    }
    
    public function __invoke($args,$parser){     
        $code = $parser->replace( 
            file_get_contents( $this->classFileFinder->getClassFileName($args[0]) ),
            array('stripMacros' => true)  
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
    
    #@access public public classFileFinder ClassFileFinder#
    
    #:ClassFileFinder
    public function getClassFileFinder(){
        return $this->classFileFinder;
    }
    
    #:this
    public function setClassFileFinder(ClassFileFinder $value){
        $this->classFileFinder = $value;
        return $this;
    }
    #@#
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