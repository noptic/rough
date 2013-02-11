<?php
/*/
type:       class
author:     Oliver Anan <oliver@ananit.de>
tags:       [macro, get, getter]

GetMacro
================================================================================
Creates a real trait or a pseudo trait depending on the current PHP version.
A pseudo trait is a final class used to emulate trait like behaviour in PHP
versions before php 5.4. 

Syntax 
--------------------------------------------------------------------------------

    trait <name>
    
name
:   The name of the trait
    
Source
--------------------------------------------------------------------------------
/*/
namespace daliaIT\rough\macro;
class TraitMacro{
    public function __invoke($args){
        $name     = $args[0];
        if(PHP_VERSION_ID < 50400){
            return "final class $name\n";    
        } else {
            return "trait $name\n";
        }
        
    }
}