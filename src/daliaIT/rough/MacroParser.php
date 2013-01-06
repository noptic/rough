<?php
namespace daliaIT\rough;
class MacroParser
{
    protected
        $macroLib,
        $pattern =  '/^([ \t]*)#@(.*?)(?:@#|#(.*?)#@#)/sm';
    
    public function __construct(IMacroLib $lib){
        $this->macroLib = $lib;
    }
    
    #@get public macroLib,pattern #
    public function getMacroLib(){
        return $this->macroLib;
    }
    
    public function getPattern(){
        return $this->pattern;
    }
    #@#
    
    #@set public macroLib,pattern#
    public function setMacroLib($value){
        $this->macroLib = $value;
        return $this;
    }
    
    public function setPattern($value){
        $this->pattern = $value;
        return $this;
    }
    #@#
    
    public function replace($input){
        $lib =$this->macroLib;
        $callback = function($matches) use ($lib){
            $indent = $matches[1];
            $macroString = $matches[2];
            $content = (isset($matches[3]))
                ? $matches[3]
                : '';
            $args = explode(" ",$macroString);
            $name = array_shift($args);
            return  $lib->runMacro($name,$args,$content,$macroString,$indent);
        };
        return preg_replace_callback($this->pattern, $callback, $input);
    }
}