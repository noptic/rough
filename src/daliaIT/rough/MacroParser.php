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
    
    public function replace($input){
        $lib =$this->macroLib;
        $argParser = new MacroArgParser();
        $callback = function($matches) use ($lib,$argParser){
            $indent = $matches[1];
            $macroString = $matches[2];
            $args = $argParser->parse($macroString);
            $name = array_shift($args);
            $output = $lib->runMacro($name,$args,$content,$macroString,$indent);
            $output = "$indent#@$macroString#\n$output#@#";
            return str_replace("\n","\n$indent", $output)."\n"; 
        };
        return preg_replace_callback($this->pattern, $callback, $input);
    }
    
    #@access public public [macroLib pattern] #
    
    public function getMacroLib(){
        return $this->macroLib;
    }
    
    public function getPattern(){
        return $this->pattern;
    }
    
    public function setMacroLib($value){
        $this->macroLib = $value;
        return $this;
    }
    
    public function setPattern($value){
        $this->pattern = $value;
        return $this;
    }
    #@#


}