<?php
namespace daliaIT\rough;
foreach( array(
    'src/daliaIT/rough/IMacroLib.php',
    'src/daliaIT/rough/MacroLib.php',
    'src/daliaIT/rough/MacroParser.php',
    'src/daliaIT/rough/SetMacro.php',
    'src/daliaIT/rough/GetMacro.php'
)as $script){ require $script; }


$sample = file_get_contents("SampleClass.php");
$lib    = new MacroLib();
$parser = new MacroParser($lib);
$lib
    ->setMacro('get',new GetMacro())
    ->setMacro('set',new SetMacro());
    
var_dump( $parser->replace($sample) );
    

