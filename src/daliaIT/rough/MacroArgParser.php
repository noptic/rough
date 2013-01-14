<?php
/*/
Credits
--------------------------------------------------------------------------------
str_split_unicode written by qeremy [atta] gmail [dotta] com
published on

Source
--------------------------------------------------------------------------------
/*/

namespace daliaIT\rough;
use Exception;
class MacroArgParser
{
    protected
    #>string
       $startList       = '[',
       $startLiteral    = '"',
       $endList         = ']',
       $endLiteral      = '"',
       $escape          = '\\',
       $whiteSpace      = array(" ","\t");
       #<
       
    public function parse($argString){
        $args = array();
        $chars = $this->str_split_unicode($argString, 1);
        while($chars){
            $char = array_shift($chars);
            if(array_search($char, $this->whiteSpace) !== false){
                continue;
            } elseif ($char === $this->startLiteral){
                $args[] = $this->parseLiteral($chars);
            } elseif($char === $this->startList){
                $args[] = $this->parseList($chars);
            } else {
                array_unshift($chars, $char);
                $args[] = $this->parseText($chars);
            }
        }
        return $args;
    }
    
    protected function parseLiteral(array &$chars){
        $escaped    = false;
        $buffer     = '';
        
        while($chars){
            $char = array_shift($chars);
            if($escaped){
                $buffer .= ($char === $this->endLiteral)
                    ? $char
                    : eval("return \"\\$char\"");
                $escaped = false;
            } elseif ($char == $this->endLiteral){
                echo "END";
                return $buffer;
            } elseif ($char == '\\'){
                $escaped = true;
            } else {
                $buffer .= $char;
            }
        }
        throw new Exception(
            "Unterminated literal"
        );
    }
    
    public function parseText(array &$chars){
         $buffer     = '';
        while($chars){
            $char = array_shift($chars);
            if(array_search($char, $this->whiteSpace) !== false){
                break;
            } else {
                $buffer .= $char;
            }
        }
        return $buffer;
    }
    
    public function parseList(array &$chars){
        $buffer     = '';
        while($chars){
            $char = array_shift($chars);
            if($char === $this->endList){
                return $this->parse($buffer);
            } else {
                $buffer .= $char;
            }
        }
        throw new Exception(
            "Unterminated list"
        );
    }
    
    public function str_split_unicode($str, $l = 0) {
        if ($l > 0) {
            $ret = array();
            $len = mb_strlen($str, "UTF-8");
            for ($i = 0; $i < $len; $i += $l) {
                $ret[] = mb_substr($str, $i, $l, "UTF-8");
            }
            return $ret;
        }
        return preg_split("//u", $str, -1, PREG_SPLIT_NO_EMPTY);
    }
    
    #@access public public [startList startLiteral endList endLiteral escape whiteSpace] #
    
    public function getStartList(){
        return $this->startList;
    }
    
    public function getStartLiteral(){
        return $this->startLiteral;
    }
    
    public function getEndList(){
        return $this->endList;
    }
    
    public function getEndLiteral(){
        return $this->endLiteral;
    }
    
    public function getEscape(){
        return $this->escape;
    }
    
    public function getWhiteSpace(){
        return $this->whiteSpace;
    }
    
    public function setStartList($value){
        $this->startList = $value;
        return $this;
    }
    
    public function setStartLiteral($value){
        $this->startLiteral = $value;
        return $this;
    }
    
    public function setEndList($value){
        $this->endList = $value;
        return $this;
    }
    
    public function setEndLiteral($value){
        $this->endLiteral = $value;
        return $this;
    }
    
    public function setEscape($value){
        $this->escape = $value;
        return $this;
    }
    
    public function setWhiteSpace($value){
        $this->whiteSpace = $value;
        return $this;
    }
    #@#


    
}