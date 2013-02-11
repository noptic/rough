<?php
namespace daliaIT\rough\command;
use RuntimeException,
    daliaIT\clayball\Command,
    daliaIT\rough\FileSearcher,
    Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Output\OutputInterface,
    Symfony\Component\Console\Input\InputOption,
    Symfony\Component\Console\Input\InputArgument;

class CreateIndex extends Command
{
    protected
        #:OutputInterface
        $out;
        
    protected function configure()
    {
        $this
            ->addArgument(
                'source',
                InputArgument::REQUIRED,
                'The file containing the build settings'
            );
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->out = $output;
        $this->out->writeln("create index");
        $buildFilePath = getcwd().'/'.$input->getArgument('source');
        $files = $this->getTargetFiles(
            dirname($buildFilePath),
            $this->getBuildInfo($buildFilePath)
        );
        $indexFilePath = dirname($buildFilePath).'/index.json';
        file_put_contents($indexFilePath, str_replace(',',",\n",json_encode($files)) );
        return $this;    
    }
    
    protected function getBuildInfo($buildFilePath){
        if(! is_readable($buildFilePath) ){
            throw new RuntimeException(
                "Can not read build file '$buildFilePath'"    
            );
        } 
        $buildInfo = json_decode(file_get_contents($buildFilePath),true);
        foreach(array('extra','dalia-it','rough','build') as $index => $key){
            if(! isset($buildInfo[$key]) ){
                throw new RuntimeException(
                    "Invalid build file. Missing node #$index '$key'"
                );
            }
            $buildInfo = $buildInfo[$key];
        }
        if(! isset($buildInfo['source']) ){
            throw new RuntimeException(
                "Invalid build file. No source directory set"    
            );
        }
        if(! isset($buildInfo['output']) ){
            throw new RuntimeException(
                "Invalid build file. No output directory set"    
            );
        }
        return $buildInfo;
    }
    
    protected function getTargetFiles($base, $buildInfo){
        $files = array();
        $searcher = new FileSearcher();
        foreach( ((array) $buildInfo['source']) as $src){
            $src = trim($src,'/');
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
}