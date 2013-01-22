<?php
/*/
type:       class
author:     Oliver Anan <oliver@ananit.de>
tags:       [macro, get, getter]

GetMacro
================================================================================
Creates a getter for one or more properties.


Syntax
--------------------------------------------------------------------------------

    get <acess> <properties> [type=mixe]d
    
access
:   The visibility of the getter method.
:   Single word
:   Required argument.
    

properties
:   The properties which the getters point to.
:   Single word or list.
:   Required argument.
    
type
:   The type of the property the getter points to.
:   Optional argument. Default is 'mixed'
     
Examples
--------------------------------------------------------------------------------
Allow public read acces to name:

    class User
    {
        protected 
            $name;
            
        #@get public name#
        
        #:mixed
        public function getName(){
            return $this->name;
        }
        #@#
    }
    
Create getter with a type tag:

    class User
    {
        protected
            $name;
            
        #@get public name string#
        
        #:string
        public function getName(){
            return $this->name;
        }
        #@# 
    }
    
create multiple getters:

    class User
    {
        protected
            $givenName,
            $familyName;
            
        #@get public [givenName familyName] string#
        
        #:string
        public function getGivenName(){
            return $this->givenName;
        }
        
        #:string
        public function getFamilyName(){
            return $this->familyName;
        }
        #@# 
    }
    
Source
--------------------------------------------------------------------------------
/*/
namespace daliaIT\rough;
use InvalidArgumentException;
class GetMacro{
    public function __invoke($args){
        $access     = $args[0];
        if(count($args)<2){
            throw new InvalidArgumentException(
                "missing arguments for get macro. Usage: 'get <access> <properties>'"
            );
        }
        $properties = (array) $args[1];
        $typeHints = (isset($args[2]))
            ? $args[2]
            : 'mixed';
        if(! is_array($typeHints)){
            $typeHints = array_fill(0,count($properties),$typeHints);
        }
        $result     = '';
        foreach($properties as $index => $property){
            $functionName =  
                'get' . strtoupper($property{0}) . substr($property, 1);
            if($typeHints[$index]){
                $result .= "\n#:{$typeHints[$index]}";
            }    
            $result .= 
                 "\n$access function $functionName(){"
                ."\n    return \$this->$property;"
                ."\n}\n";
        }
        return $result;
    }
}