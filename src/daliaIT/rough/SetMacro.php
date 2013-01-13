<?php
namespace daliaIT\rough;
class SetMacro{
    public function __invoke($args){
        $access     = $args[0];
        $properties = (array) $args[1];
        $result     = "";
        $hint       = (isset($args[2]))
            ? $args[2].' '
            : '';
        
        foreach($properties as $property){
            $functionName =  
                'set' . strtoupper($property{0}) . substr($property, 1);
                
            $result .= implode("\n",array(
                "$access function $functionName($hint\$value){",
                "    \$this->$property = \$value;",
                "    return \$this;",
                "}",
                ''
            ));
        }
        return $result;
    }
}