<?php

namespace PStaender\SSShell;

use Psy\Configuration;
use Psy\Shell;

class InteractiveShell
{
    private static $shell_config = [
        'usePcntl' => false,
        'startupMessage' => null,
    ];

    private $shell = null;

    public function run()
    {
        return $this->shell()->run();
    }

    private function shell()
    {
        $this->shell = new Shell($this->shellConfig());
        $namespace = NamespacesCommand::get_namespace();
        if ($namespace) {
            $this->shell->addInput('namespace '.NamespacesCommand::get_namespace());
        } else {
            foreach (NamespacesCommand::get_classes() as $className) {
                if (class_exists($className)) {
                    $this->shell->addInput("use $className", $silent = true);
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
        $shellConfig = self::$shell_config;
        if (!$shellConfig['startupMessage']) {
            $environment = \SilverStripe\Control\Director::get_environment_type();
            $version = (new \SilverStripe\Core\Manifest\VersionProvider())->getVersion();
            $shellConfig['startupMessage'] .= "Loading $environment environment (SilverStripe $version)";
        }
        $config = new Configuration($shellConfig);
        $config->getPresenter()->addCasters(
            $this->getCasters()
        );

        return $config;
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
