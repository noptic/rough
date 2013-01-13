<?php
namespace daliaIT\rough;
class GetMacro{
    public function __invoke($args){
        $access     = $args[0];
        $properties = (array) $args[1];
        $result     = '';
        foreach($properties as $property){
            $functionName =  
                'get' . strtoupper($property{0}) . substr($property, 1);
                
            $result .= 
                 "$access function $functionName(){"
                ."\n    return \$this->$property;"
                ."\n}\n";
        }
        return $result;
    }
}