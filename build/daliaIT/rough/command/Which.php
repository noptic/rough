<?php
namespace daliaIT\rough\command;
use daliaIT\clayball\Command,
    Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Output\OutputInterface,
    Symfony\Component\Console\Input\InputOption,
    Symfony\Component\Console\Input\InputArgument;

class Which extends Command
{
   protected function execute(InputInterface $input, OutputInterface $output)
    {
        $class = $input->getArgument('class');
        var_dump($this->context['raw']['loader']->findFile($class));   
    }
    
    protected function configure()
    {
        $this
            ->addArgument(
                'class',
                InputArgument::REQUIRED,
                'The class to search.'
            );
    }
}