<?php
namespace daliaIT\rough;
use OutOfRangeException;
class MacroLib implements IMacroLib{
    protected
        $macros = array();
        
    function runMacro($name,$args,$parser){
        $macro = $this->getMacro($name);
        return $macro($args,$parser);
    }
    
    public function getMacro($name){
        if(! isset($this->macros[$name]) ){
            throw new OutOfRangeException("Unknown macro: '$name'");
        } 
        return $this->macros[$name];
    }
    
    public function setMacro($name, $macro){
        $this->macros[$name] = $macro;
        return $this;
    }
}