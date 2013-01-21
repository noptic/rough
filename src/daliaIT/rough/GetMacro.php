<?php
namespace daliaIT\rough;
use InvalidArgumentException;
class GetMacro{
    public function __invoke($args){
        $access     = $args[0];
        if(count($args)<2){
            throw new InvalidArgumentException(
                "missing arguments for get macro. Usage: 'get <access> <properties>'"
            );
        }
        $properties = (array) $args[1];
        $typeHints = (isset($args[2]))
            ? $args[2]
            : 'mixed';
        if(! is_array($typeHints)){
            $typeHints = array_fill(0,count($properties),$typeHints);
        }
        $result     = '';
        foreach($properties as $index => $property){
            $functionName =  
                'get' . strtoupper($property{0}) . substr($property, 1);
            if($typeHints[$index]){
                $result .= "\n#:{$typeHints[$index]}";
            }    
            $result .= 
                 "\n$access function $functionName(){"
                ."\n    return \$this->$property;"
                ."\n}\n";
        }
        return $result;
    }
}