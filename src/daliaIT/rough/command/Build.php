<?php
namespace daliaIT\rough\command;
use Exception,
    InvalidArgumentException,
    RuntimeException,
    daliaIT\rough\FileSearcher,
    daliaIT\rough\GetMacro,
    daliaIT\rough\SetMacro,
    daliaIT\rough\AccessMacro,
    daliaIT\rough\ImportMacro,
    daliaIT\rough\MacroLib,
    daliaIT\rough\MacroParser,
    daliaIT\rough\ListClassFileFinder;
    
class Build
{
    protected
    #:MacroParser
        $parser;
        
    public function __construct(){
        $lib    = new MacroLib();
        $lib->setMacro('get',new GetMacro())
            ->setMacro('set',new SetMacro())
            ->setMacro('access',new AccessMacro())
            ->setMacro('import',new ImportMacro());
        $this->parser = new MacroParser($lib);
    }
    
    public function run(array $args){
        if(! isset($args[0]) ){
            throw new InvalidArgumentException(
                'Missing argument. No build file set.'    
            );
        }
        $buildFilePath = getcwd().'/'.$args[0];
        if(! is_readable($buildFilePath) ){
            throw new RuntimeException(
                "Can not read build file '$buildFilePath'"    
            );
        } 
        $buildInfo = spyc_load_file($buildFilePath);
        if(! isset($buildInfo['source']) ){
            throw new RuntimeException(
                "Invalid build file. No source directory set"    
            );
        }
        $this->buildFiles(
            dirname($buildFilePath),
            $buildInfo
        );
        return $this;
    }
    
    protected function buildFiles($base, $buildInfo){
        $files      = $this->getTargetFiles($base, $buildInfo);
        $classList = $this->createClassLIst($files);
        $this->updateMacros($files, $classList);
        foreach($files as $shortName => $file){
            $out = "$base/{$buildInfo['target']}/$shortName";
            $this->buildFile($file,  $out);
        }
        return $this;
    }
    
    protected function updateMacros($fileList,$classList){
        $finder = new ListClassFileFinder;
        $finder->setFiles($classList);
        $this->parser->getMacroLib()
            ->getMacro('import')->setClassFileFinder($finder);
        return $this;
    }
    protected function createClassList(array $files){
        $classList = array();
        foreach($files as $shortName => $file){
            $class = trim(str_replace('/','\\',$shortName),"\\");
            $class = substr($class, "0", strpos($class,'.') );
            $classList[$class] = $file;
        }
        return $classList;
    }
    
    protected function buildFile($in, $out){
        $contents   = file_get_contents($in);
        try{
            $contents   = $this->processContents($contents);
        }  catch(Exception $e){
            throw new RuntimeException(
                "Processing file'$in' failed.\n"
                .$e->getMessage()
            );
        }
        $fileDir    = dirname($out);
        if(! file_exists($fileDir)){
            mkdir($fileDir, 0777, true);
        }
        file_put_contents($out, $contents);
        return $this;
    }
    
    protected function processContents($contents){
        return $this->parser->replace($contents);
    }
    
    protected function getTargetFiles($base, $buildInfo){
        $files = array();
        $searcher = new FileSearcher();
        foreach( ((array) $buildInfo['source']) as $src){
            $path       = "$base/$src";
            $pathLength = strlen($path);
            $newFiles = $searcher->searchRecursive(
                '*.php',
                $path
            );
            foreach($newFiles as $newFile){
                $files[ substr($newFile, $pathLength) ] = $newFile;
            }
        }
        return $files;
    }
    
    #@access public public MacroParser#
    
    public function getMacroParser(){
        return $this->MacroParser;
    }
    
    #:this
    public function setMacroParser($value){
        $this->MacroParser = $value;
        return $this;
    }
    #@#
}