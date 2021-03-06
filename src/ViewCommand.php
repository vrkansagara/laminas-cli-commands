<?php

namespace Divix\Laminas\Cli\Command;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Question\Question;

use Laminas\Code\Generator\ClassGenerator;
use Laminas\Code\Generator\DocBlockGenerator;
use Laminas\Code\Generator\PropertyGenerator;
use Laminas\Code\Generator\MethodGenerator;

/**
 * Usage:
 * 
 * "vendor/bin/laminas-cli.bat" mvc:view --module=<moduleName> <controllerName> <name>
 */
class ViewCommand extends AbstractCommand
{
    protected static $defaultName = 'mvc:view';

    protected function configure()
    {
        $this
            ->setDescription('Creates a new view.')
            ->setHelp('This command allows you to create a MVC view')
            ->addArgument('controller', InputArgument::REQUIRED, 'The name of the related controller.')
            ->addArgument('name', InputArgument::REQUIRED, 'The name of the view.');
        
        parent::configure();
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $section1 = $output->section();
        $section2 = $output->section();
        $section1->writeln('Start creating a view');
        
        $moduleName = $this->getModuleName($input, $output, 'view');
        
        $controllerName = $input->getArgument('controller');
        $name = $input->getArgument('name');
        $contents = '<p>'.$moduleName.' - '.$name.'</p>';
        
        $section2->writeln(PHP_EOL.$contents.PHP_EOL);
        
        if ($this->isJsonMode()) {
            $code = (json_encode([$name.'.phtml' => $contents]));
            $section2->writeln($code);
        } else {
            $this->storeViewContents($name.'.phtml', $moduleName, $controllerName, $contents);
        }
        $section1->writeln('Done creating new view.');
        
        parent::postExecute($input, $output, $section1, $section2);

        return 0;
    }
}