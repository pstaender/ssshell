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
            ->setDescription('Inspect static methods and attributes, e.g.: static props|methods SiteTree')
            ->addArgument('className', InputArgument::REQUIRED)
            ->addArgument('propertiesOrMethods', InputArgument::OPTIONAL)
            ->addArgument('nestingLevel', InputArgument::OPTIONAL);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dump = [];
        $className = $input->getArgument('className');
        $name = $input->getArgument('propertiesOrMethods') ?: '*';
        $nestingLevel = (int) $input->getArgument('nestingLevel');
        $depth = $nestingLevel == 0 ? 3 : $nestingLevel;

        $showProperties = preg_match('/^\$/i', $name);
        $showMethods = !$showProperties;

        if ($showProperties) {
            $showProperties = substr($name, 1);
        }

        if (!class_exists($className)) {
            $className = NamespacesCommand::resolve_class_name($className);
        }

        $reflection = new ReflectionClass($className);
        $reflectionMethod = ReflectionMethod::IS_STATIC;

        $props = $reflection->getProperties($reflectionMethod);
        $methods = $reflection->getMethods($reflectionMethod);

        if ($showProperties) {
            $props = array_map(function (\ReflectionProperty $reflectionProperty) {
                $visibility = null;
                if ($reflectionProperty->isPrivate()) {
                    $visibility = 'private';
                } elseif ($reflectionProperty->isProtected()) {
                    $visibility = 'protected';
                } elseif ($reflectionProperty->isFinal()) {
                    $visibility = 'final';
                } else {
                    $visibility = 'public';
                }

                $reflectionProperty->setAccessible(true);

                return [
                    'name' => $reflectionProperty->getName(),
                    'value' => $reflectionProperty->getValue(),
                    'access' => $visibility,
                ];
            }, $props);
            foreach ($props as $v) {
                if ($showProperties === '*' || $v['name'] === $showProperties) {
                    $dump[] = $v;
                }
            }
        }

        if ($showMethods) {
            $methods = array_map(function (\ReflectionMethod $reflection) {
                $visibility = null;
                if ($reflection->isPrivate()) {
                    $visibility = 'private';
                } elseif ($reflection->isProtected()) {
                    $visibility = 'protected';
                } elseif ($reflection->isFinal()) {
                    $visibility = 'final';
                } else {
                    $visibility = 'public';
                }

                return [
                    'name' => $reflection->getName(),
                    'access' => $visibility,
                    'parameters' => $reflection->getParameters(),
                ];
            }, $methods);

            $dump = $methods;

            if ($name !== '*') {
                $filteredMethods = [];
                foreach ($methods as $reflection) {
                    if ($reflection['name'] === $name) {
                        $filteredMethods[] = $reflection;
                    }
                }
                $dump = $filteredMethods;
            }
        }

        $output->page(
            $this->presenter->present($dump, $depth, Presenter::VERBOSE)
        );
    }
}
