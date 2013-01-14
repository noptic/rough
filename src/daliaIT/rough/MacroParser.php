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
            $output = $lib->runMacro($name,$args);
            $output = "$indent#@$macroString#\n$output#@#";
            return str_replace("\n","\n$indent", $output)."\n"; 
        };
        return preg_replace_callback($this->pattern, $callback, $input);
    }
    
    #@access public public macroLib IMacroLib#
    
    #:IMacroLib
    public function getMacroLib(){
        return $this->macroLib;
    }
    
    #:this
    public function setMacroLib(IMacroLib $value){
        $this->macroLib = $value;
        return $this;
    }
    #@#

    #@access public public pattern string#
    
    #:string
    public function getPattern(){
        return $this->pattern;
    }
    
    #:this
    public function setPattern($value){
        if(! is_string($value)){
           throw new \InvalidArgumentException(
             __METHOD__ .' expects a string but got a '.gettype($value)
           );
        }
        $this->pattern = $value;
        return $this;
    }
    #@#

}