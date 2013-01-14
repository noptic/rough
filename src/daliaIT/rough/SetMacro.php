<?php
namespace daliaIT\rough;
class SetMacro{
    protected
        $validationFunctions = array(
            'bool'      => 'is_bool',
            'float'     => 'is_float',
            'int'       => 'is_int',
            'numeric'   => 'is_numeric',
            'scalar'    => 'is_scalar',
            'string'    => 'is_string',
            'object'    => 'is_object'
        );
    
    public function __invoke($args){
        $access     = $args[0];
        $properties = (array) $args[1];
        $result     = "";
        $typeHints  = (isset($args[2]))
            ? $args[2]
            : '';
        if(! is_array($typeHints)){
            $typeHints = array_fill(0,count($properties),$typeHints);
        }
        foreach($properties as $index => $property){
            $functionName =  
                'set' . strtoupper($property{0}) . substr($property, 1);
            $typeHint = '';
            $validationCode = '';
            if($typeHints[$index]){
                if( isset($this->validationFunctions[$typeHints[$index]]) ){
                    $func = $this->validationFunctions[$typeHints[$index]];
                    $validationCode = 
                      "    if(! $func(\$value)){\n"
                     ."       throw new \InvalidArgumentException(\n"
                     ."         __METHOD__ .' expects a {$typeHints[$index]} but got a '.gettype(\$value)\n"
                     ."       );\n"
                     ."    }\n";
                     
                } else{
                    $typeHint = $typeHints[$index].' ';
                }
            }    
            $result .= implode("\n",array(
                '',
                '#:this',
                "$access function $functionName($typeHint\$value){",
                "$validationCode    \$this->$property = \$value;",
                "    return \$this;",
                "}",
                ''
            ));
        }
        return $result;
    }
    
    #@access public public validationFunctions array@#
}