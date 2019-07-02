<?php
namespace PStaender\SSShell;

use Psy\Command\ReflectingCommand;
use Psy\VarDumper\Presenter;
use Psy\VarDumper\PresenterAware;
use ReflectionClass;
use ReflectionMethod;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StaticCommand extends ReflectingCommand implements PresenterAware
{

    private $presenter;

    protected static $defaultName = 'static';

    public function setPresenter(Presenter $presenter)
    {
        $this->presenter = $presenter;
    }

    protected function configure()
    {
        $this
            ->setDescription('Inspect static methods and attributes, e.g. static (props|methods) DataObject')
            ->addArgument('methodsOrProperties', InputArgument::REQUIRED)
            ->addArgument('className', InputArgument::OPTIONAL);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($input->getArgument('className')) {
            $className = $input->getArgument('className');
            $methodsOrProperties = $input->getArgument('methodsOrProperties');
        } else {
            $className = $input->getArgument('methodsOrProperties');
            $methodsOrProperties = null;
        }

        $reflection = new ReflectionClass($className);
        $reflectionMethod = ReflectionMethod::IS_STATIC;

        $depth = 2;

        if (!$methodsOrProperties) {
            $depth++;
            $dump = [
                'properties' => $reflection->getProperties($reflectionMethod),
                'methods' => $reflection->getMethods($reflectionMethod),
            ];
        } else if (preg_match('#prop#i', $methodsOrProperties)) {
            $dump = $reflection->getProperties($reflectionMethod);
        } else {
            $dump = $reflection->getMethods($reflectionMethod);
        }

        $output->page(
            $this->presenter->present($dump, $depth, Presenter::VERBOSE)
        );
    }

}
