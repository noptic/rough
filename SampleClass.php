<?php
namespace daliaIT\rough;
class Sample
{
    protected
        $foo,
        $bar,
        $baz,
        $taz;
    
    #@get public foo,bar#
    public function getStuff(){}
    #@#
    
    #@set protected baz,taz array@#
}