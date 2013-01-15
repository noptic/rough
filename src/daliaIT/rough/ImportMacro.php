<?php
namespace daliaIT\rough;
use ReflectionClass;

class ImportMacro{
    protected
        $stripTokens = array(T_COMMENT,T_DOC_COMMENT);
        
    public function __invoke($args,$parser){     
        $code = $parser->replace( 
            file_get_contents( $this->getClassFileName($args[0]) ),
            array('stripMacros' => true)  
        );
        return $this->extractBody($code);
    }
    
    protected function getClassFileName($class){
        $reflect = new ReflectionClass($class);
        return $reflect->getFileName();
    }
    
    public function extractBody($sourcecode){
        $tokens = token_get_all($sourcecode);
        $code = '';
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
        return $code;
    }
}