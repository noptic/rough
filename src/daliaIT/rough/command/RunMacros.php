<?php
namespace daliaIT\rough\command;
use RuntimeException,
    daliaIT\clayball\Command,
    daliaIT\rough\MacroParser,
    daliaIT\rough\MacroLib,
    daliaIT\rough\FileSearcher,
    Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Input\ArrayInput,
    Symfony\Component\Console\Output\OutputInterface,
    Symfony\Component\Console\Input\InputOption,
    Symfony\Component\Console\Input\InputArgument;

class RunMacros extends Command
{
    protected
    #:Parser
        $parser,
    #:OutputInterface
        $out,
    #:InputInterface
        $in;
        
    protected function configure()
    {
        $this
            ->addArgument(
                'source',
                InputArgument::REQUIRED,
                'The file containing the build settings'
            );
        $macros = $this->createMacros($this->context['macros']);
        $lib = new MacroLib();
        $lib->setMacros($macros);
        $this->parser = new MacroParser($lib);
    }
    
    protected function createMacros(array $macroList){
        $result = array();
        foreach($macroList as $name => $class){
            $result[$name] = new $class($this);
        }
        return $result;
    }
    
    public function getContext(){
        return $this->context;
    }
    
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->out = $output;
        $this->in = $input;
        $buildFilePath = getcwd().'/'.$input->getArgument('source');
        $this->buildFiles(
            dirname($buildFilePath),
            $this->getBuildInfo($buildFilePath)
        );
        return $this;    
    }
    
    protected function updateIndex(){    
        $input = new ArrayInput(array(
            'command'   => 'index',
            'source'    => $this->in->getArgument('source')
        ));
        $this
            ->getApplication()
            ->find('index')
            ->run($input, $this->out);
    }
    
    protected function getBuildInfo($buildFilePath){
        $this->out->writeln("read build info from $buildFilePath");
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
    
    protected function buildFiles($base, $buildInfo){
        $this->updateIndex();
        $files = json_decode(file_get_contents("$base/index.json"));
        $this->out->writeln("run macros");
        foreach($files as $shortName => $file){
            if($this->in->getOption('verbose')){
                $this->out->writeln("  $shortName");
            }
            $dir = trim($buildInfo['output'],'/');
            $out = "$base/$dir/$shortName";
            $this->buildFile($file,  $out);
        }
        return $this;
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
}