<?php
/*/
type:       class
author:     Oliver Anan <oliver@ananit.de>
tags:       [macro, access, get, set, getter, setter]

AccessMacro
================================================================================
Creates a getter and/or a setter for one or more properties.

This macro combines a GetMacro and a SetMacro,code generation and type hinting
are handled by GetMacro and SetMacro

Syntax
--------------------------------------------------------------------------------

    access <visibilty> <properties> [type=mixed]
    
visibilty
 - The visibility of the getter and setter method.
 - No method will be generated if the value eqals 'none'.
 - Single word or array. First element defines the visivilty of the getter
   second the visibilty of the setter
 - Required argument.
    
properties
 - The properties which the getters and setters point to.
 - Single word or array.
:- Required argument.
    
type
 - The type of the property.
 - Type hinting and validation are delegated to GetMacro and SetMacro.
 - Optional argument. Default is 'mixed'
     
Examples
--------------------------------------------------------------------------------
Allow public read acces to name but do not create a setter:

    class User
    {
        protected 
            $name;
            
        #@access [public none]  name#
        
        public function getName(){
            return $this->name;
        }
        #@#
    }
    
Create typesave setter:

    class User
    {
        protected
            $name;
            
        #@access public name string#
        
        #:string
        public function getName(){
            return $this->name;
        }
        
        #:this
        public function setName($value){
            if(! is_string($value)){
               throw new \InvalidArgumentException(
                 __METHOD__ .' expects a string but got a '.gettype($value)
               );
            }
            $this->name = $value;
            return $this;
        }
        #@# 
    }
    
create multiple setters and getters:

    class User
    {
        protected
            $givenName,
            $familyName;
            
        #@access public [givenName familyName] string#
        
        #:string
        public function getGivenName(){
            return $this->givenName;
        }
        
        #:string
        public function getFamilyName(){
            return $this->familyName;
        }
        
        #:this
        public function setGivenName($value){
            if(! is_string($value)){
               throw new \InvalidArgumentException(
                 __METHOD__ .' expects a string but got a '.gettype($value)
               );
            }
            $this->givenName = $value;
            return $this;
        }
        
        #:this
        public function setFamilyName($value){
            if(! is_string($value)){
               throw new \InvalidArgumentException(
                 __METHOD__ .' expects a string but got a '.gettype($value)
               );
            }
            $this->familyName = $value;
            return $this;
        }
        #@# 
    }
    
Source
--------------------------------------------------------------------------------
/*/
namespace daliaIT\rough\macro;
use Exception,
    InvalidArgumentException;
class AccessMacro{
    
    public function __invoke($args){
        if(count($args) < 2){
            throw new Exception(
                "Missing required macro argument." 
                ."The macro 'access' requires at least 2 arguments:\n"
                ."<getAccess> <setAccess> <properties>"
            );
        }
        $access = $args[0];
        if(is_array($access)){
            if(count($access) != 2){
                throw new InvalidArgumentException(
                    "Invalid parameter: parameter 0 must be a single argument "
                    ."or a array woth 2 arguments."    
                ); 
            }
            $getAccess  = $access[0];
            $setAccess  = $access[1];
        } else {
            $getAccess  = $access;
            $setAccess  = $access;
        }

        $hints      = (isset($args[2]))
            ? $args[2]
            : false;
        $properties = (array) $args[1];
        $getMacro   = new GetMacro();
        $setMacro   = new SetMacro();
        $result     = '';
        if($getAccess !== 'none'){
            $result .= $getMacro(array($getAccess,$properties,$hints));
        }
        if($setAccess !== 'none'){
            $result .= $setMacro(array($setAccess,$properties,$hints));
        }
        return $result;
    }
}