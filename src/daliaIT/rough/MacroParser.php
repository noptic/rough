<?php
namespace daliaIT\rough;
use Exception,
    RuntimeException;
class MacroParser
{
    protected
        $macroLib,
        $pattern =  '/^([ \t]*)#@(.*?)(?:@#|#(.*?)#@#)/sm',
        $options = array(
            'indentOutput'  => true,
            'stripMacros'   => false,
        );
    
    public function __construct(IMacroLib $lib){
        $this->macroLib = $lib;
    }
    
    public function replace($input,array $options=array()){
        $lib =$this->macroLib;
        $argParser = new MacroArgParser();
        $parser = $this;
        $options = array_replace($this->options,$options);
        
        $callback = function($matches) use ($lib,$argParser,$parser,$options){
            $indent = $matches[1];
            $macroString = $matches[2];
            $args = $argParser->parse($macroString);
            $name = array_shift($args);
            try{
                $output = $lib->runMacro($name,$args,$parser);
            } catch(Exception $e){
                throw new RuntimeException(
                    "Processing macro string '$macroString' failed with message:\n"
                    .$e->getMessage()
                );
            }
            if(!$options['stripMacros']){
                $output = "#@$macroString#\n$output#@#";    
            }
            if($options['indentOutput']){
                $buffer = array();
                foreach(explode("\n",$output) as $line){
                    $buffer[] = "$indent$line";
                }
                $output = implode("\n",$buffer);
                unset($buffer);
            }
            return $output; 
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