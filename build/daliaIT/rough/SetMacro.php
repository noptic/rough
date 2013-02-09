<?php
/*/
type:       class
author:     Oliver Anan <oliver@ananit.de>
tags:       [macro, set, setter, chain]

SetMacro
================================================================================
Creates a setter for one or more properties.

The generated object is chainable and always return the called object instance.

The setters can be made typestrict using type hinting or 'is_...' methods.
Types which use validation methods:
 
 - bool
 - float   
 - int
 - numeric
 - scalar
 - string 
 - object
 
    #@set public sampleProperty int#
    
    #:this
    public function setSampleProperty($value){
        if(! is_int($value)){
           throw new \InvalidArgumentException(
             __METHOD__ .' expects a int but got a '.gettype($value)
           );
        }
        $this->sampleProperty = $value;
        return $this;
    }
    #@#
    
Anything else will be used as literal typehint

    #@set public sampleProperty qwerty#
    
    #:this
    public function setSampleProperty(qwerty $value){
        $this->sampleProperty = $value;
        return $this;
    }
    #@#
    
Syntax
--------------------------------------------------------------------------------

    set <acess> <properties> [type=mixe]d
    
access
:   The visibility of the setter method.
:   Single word
:   Required argument.
    

properties
:   The properties which the setters point to.
:   Single word or list.
:   Required argument.
    
type
:   The type of the property the setter points to.
:   Optional argument. Default is 'mixed'.
:   ICreates a typestrict setter.
     
Examples
--------------------------------------------------------------------------------
Allow public write acces to name:

    class User
    {
        protected 
            $name;
            
        #@set public name#
        
        #:this
        public function setName($value){
            $this->name = $value;
            return $this;
        }
        #@#
    }
    
Create type strict setters

    class User
    {
        protected
            $name,
            $boss,
            $comments;
        #@set public name string#
        
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
        
        #@set public boss User#
        
        #:this
        public function setBoss(User $value){
            $this->boss = $value;
            return $this;
        }
        #@#
        #@set public comments array#
        
        #:this
        public function setComments(array $value){
            $this->comments = $value;
            return $this;
        }
        #@#
    }
    
create multiple setters:

    class User
    {
        protected
            $givenName,
            $familyName;
            
        #@set public [givenName familyName] string#
        
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
class SetMacro{
    protected
        $validationFunctions = array(
            'bool'      => 'is_bool',
            'float'     => 'is_float',
            'int'       => 'is_int',
            'numeric'   => 'is_numeric',
            'scalar'    => 'is_scalar',
            'string'    => 'is_string',
            'object'    => 'is_object'
        );
    
    public function __invoke($args){
        $access     = $args[0];
        $properties = (array) $args[1];
        $result     = "";
        $typeHints  = (isset($args[2]))
            ? $args[2]
            : '';
        if(! is_array($typeHints)){
            $typeHints = array_fill(0,count($properties),$typeHints);
        }
        foreach($properties as $index => $property){
            $functionName =  
                'set' . strtoupper($property{0}) . substr($property, 1);
            $typeHint = '';
            $validationCode = '';
            if($typeHints[$index]){
                if( isset($this->validationFunctions[$typeHints[$index]]) ){
                    $func = $this->validationFunctions[$typeHints[$index]];
                    $validationCode = 
                      "    if(! $func(\$value)){\n"
                     ."       throw new \InvalidArgumentException(\n"
                     ."         __METHOD__ .' expects a {$typeHints[$index]} but got a '.gettype(\$value)\n"
                     ."       );\n"
                     ."    }\n";
                     
                } else{
                    $typeHint = $typeHints[$index].' ';
                }
            }    
            $result .= implode("\n",array(
                '',
                '#:this',
                "$access function $functionName($typeHint\$value){",
                "$validationCode    \$this->$property = \$value;",
                "    return \$this;",
                "}",
                ''
            ));
        }
        return $result;
    }
    
    #@access public public validationFunctions array#
    
    #:array
    public function getValidationFunctions(){
        return $this->validationFunctions;
    }
    
    #:this
    public function setValidationFunctions(array $value){
        $this->validationFunctions = $value;
        return $this;
    }
    #@#
}