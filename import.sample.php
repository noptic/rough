<?php
namespace daliaIT\rough;
require 'src/daliaIT/rough/IMacroLib.php';
require 'src/daliaIT/rough/MacroLib.php';
require 'src/daliaIT/rough/MacroParser.php';

$sample = file_get_contents("SampleClass.php");

//run macros
$lib    = new MacroLib();
$parser = new MacroParser($lib);
$code = '';

$lib
    ->setMacro('get',function($args,$content,$macroString,$indent){
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
    })
    ->setMacro('set',function($args,$content,$macroString,$indent){
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
    });
    
$sample = $parser->replace($sample);

$tokens = token_get_all($sample);
do{
    $token = array_shift($tokens);
}
while( $token[0] !== 353 && $tokens);

do{
    $token = array_shift($tokens);
    if( is_string($token) ){
        $token = array(null,$token);
    }
}
while( ($token[1] !== '{') && $tokens );

$nesting = 1;
while($tokens)
{
    $token = array_shift($tokens);
    if( is_string($token) ){
        $token = array(null,$token);
    }
    if($token[1] === '}' && --$nesting == 0){ 
        break;
    } elseif( $token[1] === '{' ){
        $nesting++;
    }
    $code .= $token[1];
}
#file_put_contents('tmp',$code);
#$code = php_strip_whitespace('tmp');
var_dump($code);