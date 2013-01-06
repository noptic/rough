<?php
namespace daliaIT\rough;
class SetMacro{
    public function __invoke($args,$content,$macroString,$indent){
        $access     = $args[0];
        $properties = explode(',',$args[1]);
        $result     = "$indent#@$macroString#";
        $hint       = (isset($args[2]))
            ? $args[2].' '
            : '';
        
        foreach($properties as $property){
            $functionName =  
                'set' . strtoupper($property{0}) . substr($property, 1);
                
            $result .= 
                 "\n$access function $functionName($hint\$value){"
                ."\n    \$this->$property = \$value;"
                ."\n    return \$this;"
                ."\n}\n";
        }
        
        $result .= "#@#";
        $result = str_replace("\n","\n$indent",$result);
        return $result;
    }
}