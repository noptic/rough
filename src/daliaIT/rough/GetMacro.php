<?php
namespace daliaIT\rough;
class GetMacro{
    public function __invoke($args,$content,$macroString,$indent){
        $access     = $args[0];
        $properties = explode(',',$args[1]);
        $result = "$indent#@$macroString#";
        foreach($properties as $property){
            $functionName =  
                'get' . strtoupper($property{0}) . substr($property, 1);
                
            $result .= 
                 "\n$access function $functionName(){"
                ."\n    return \$this->$property;"
                ."\n}\n";
        }
        $result .= "#@#";
        $result = str_replace("\n","\n$indent",$result);
        return $result;
    }
}