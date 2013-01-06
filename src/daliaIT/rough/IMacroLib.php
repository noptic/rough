<?php
namespace daliaIT\rough;
interface IMacroLib{
    function runMacro($name,$args,$content,$macroString,$indent);
}