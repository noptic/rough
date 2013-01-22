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

    access <getAcess> <setAccess> <properties> [type=mixed]
    
getAccess
:   The visibility of the getter method.
:    No getter method will be generated if the value eqals 'none'.
:    Single word.
:    Required argument.
    
setAccess
:   The visibility of the setter method.
    No setter method will be generated if the value eqals 'none'.
:   Single word.
:   Required argument.
    
properties
:   The properties which the getters and setters point to.
:   Single word or list.
:   Required argument.
    
type
:   The type of the property the getter points to.
:   Type hinting and validation are delegated to GetMacro and SetMacro.
:   Optional argument. Default is 'mixed'
     
Examples
--------------------------------------------------------------------------------
Allow public read acces to name but do not create a setter:

    class User
    {
        protected 
            $name;
            
        #@access public none  name#
        
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
            
        #@access public public name string#
        
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
            
        #@access public public [givenName familyName] string#
        
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
namespace daliaIT\rough;
use Exception;
class AccessMacro{
    public function __invoke($args){
        if(count($args) < 3){
            throw new Exception(
                "Missing required macro argument." 
                ."The macro 'access' requires at least 3 arguments:\n"
                ."<getAccess> <setAccess> <properties>"
            );
        }
        $getAccess  = $args[0];
        $setAccess  = $args[1];
        $hints      = (isset($args[3]))
            ? $args[3]
            : false;
        $properties = (array) $args[2];
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