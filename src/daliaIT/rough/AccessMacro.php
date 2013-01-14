<?php
namespace daliaIT\rough;
use Exception;
class AccessMacro{
    public function __invoke($args){
        if(count($args) < 3){
            throw new Exception(
                "Missing required macro argument." 
                ."The macro 'Access' requires at least 3 arguments:\n"
                ."<getAccess> <setAccess> <properties>"
            );
        }
        $getAccess  = $args[0];
        $setAccess  = $args[1];
        $hints      = (isset($args[2]))
            ? $args[2]
            : false;
        $properties = (array) $args[2];
        $getMacro   = new GetMacro();
        $setMacro   = new SetMacro();
        $result     = '';
        if($getAccess !== 'none'){
            $result .= $getMacro(array($getAccess,$properties));
        }
        if($setAccess !== 'none'){
            $result .= $setMacro(array($setAccess,$properties));
        }
        return $result;
    }
}