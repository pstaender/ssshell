<?php

namespace PStaender\SSShell;

use Psy\Configuration;
use Psy\Shell;
use Symfony\Component\Console\Input\ArgvInput;

class InteractiveShell
{

    private $shell = null;
    private $shellConfig = null;

    public function run()
    {
        return $this->shell()->run();
    }

    private function shell()
    {
        $this->shell = new Shell($this->shellConfig());
        $namespace = NamespacesCommand::get_namespace();
        if (!$this->shellConfig()->inputIsPiped() && $this->shellConfig()->getInputInteractive()) {
            /**
             * Only add namespace if we are not in piped input
             * Otherwise we will get (syntax) error because two inputs are
             * received at the same time
             */
            if ($namespace) {
                $this->shell->addInput('namespace '.NamespacesCommand::get_namespace());
            } else {
                foreach (NamespacesCommand::get_classes() as $className) {
                    if (class_exists($className)) {
                        $this->shell->addInput("use $className", $this->shellConfig()->getOutputVerbosity() <= 32);
                    }
                }
            }
        }
        

        $this->shell->addCommands($this->getCommands());

        return $this->shell;
    }

    private function getCommands()
    {
        return [
            new SakeCommand(),
            new StaticCommand(),
            new NamespacesCommand(),
        ];
    }

    private function shellConfig()
    {
        if ($this->shellConfig) {
            return $this->shellConfig;
        }

        // using same cli arguments as psysh
        $config = Configuration::fromInput(new ArgvInput());

        $environment = \SilverStripe\Control\Director::get_environment_type();
        $version = (new \SilverStripe\Core\Manifest\VersionProvider())->getVersion();

        $startupMessage = "Loading $environment environment (SilverStripe $version)";
        
        $config->setStartupMessage($startupMessage);
        $config->getPresenter()->addCasters(
            $this->getCasters()
        );

        return $this->shellConfig = $config;
    }

    private function getCasters()
    {
        return [
            'SilverStripe\ORM\DataObject' => Caster::class.'::castModel',
            'SilverStripe\ORM\DataList' => Caster::class.'::castList',
            'SilverStripe\ORM\ArrayList' => Caster::class.'::castList',
            'SilverStripe\ORM\Connect\Query' => Caster::class.'::castQuery',
        ];
    }
}
