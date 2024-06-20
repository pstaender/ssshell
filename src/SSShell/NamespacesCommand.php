<?php

namespace PStaender\SSShell;

use Psy\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Psy\VarDumper\PresenterAware;
use Psy\VarDumper\Presenter;

class MappedClassNotFoundException extends \Exception
{
}

class NamespacesCommand extends Command implements PresenterAware
{
    private static $classes = [
        'SilverStripe\i18n\i18n',
        'SilverStripe\SiteConfig\SiteConfig',
        'SilverStripe\Security\Member',
        'SilverStripe\Security\Group',
        'SilverStripe\Security\Permission',
        'SilverStripe\Reports\Reports',
        'SilverStripe\ORM\DB',
        'SilverStripe\Control\Director',
        'SilverStripe\Core\Environment',
        'SilverStripe\Assets\File',
        'SilverStripe\Assets\Image',
        'SilverStripe\Assets\Folder',
        'SilverStripe\Assets\Filesystem',
        'SilverStripe\ErrorPage\ErrorPage',
        'SilverStripe\Dev\Debug',
        'SilverStripe\Core\Config\Config',
        'SilverStripe\CampaignAdmin\CampaignAdmin',
        'SilverStripe\CMS\CMS',
        'SilverStripe\ORM\DataObject',
        'SilverStripe\CMS\Model\SiteTree',
        'SilverStripe\Control\Email\Email',
    ];

    private static $namespace = null;

    private $presenter;

    protected static $defaultName = 'namespaces';

    public function setPresenter(Presenter $presenter)
    {
        $this->presenter = $presenter;
    }

    protected function configure()
    {
        $this
            ->setDescription('Shows all preloaded namespaces');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        return $output->page(
            $this->presenter->present(
                self::class_mapping(), 1, Presenter::VERBOSE
            )
        );
    }

    public static function set_namespace(string $namespace)
    {
        self::$namespace = $namespace;
    }

    public static function get_namespace()
    {
        return self::$namespace;
    }

    public static function set_classes(array $classes)
    {
        self::$classes = $classes;
    }

    public static function get_classes()
    {
        // resolve
        $allClasses = get_declared_classes();
        $classes = [];
        foreach (self::$classes as $c) {
            if (preg_match('/\*$/', $c)) {
                $c = substr($c, 0, -1);
                foreach ($allClasses as $class) {
                    if (strpos($class, $c) === 0) {
                        $classes[] = $class;
                    }
                }
            } else {
                $classes[] = $c;
            }
        }

        return $classes;
    }

    public static function class_mapping()
    {
        $namespaces = [];
        foreach (self::get_classes() as $namespace) {
            $parts = explode('\\', $namespace);
            $className = $parts[sizeof($parts) - 1];
            if (isset($namespaces[$className])) {
                echo "WARNING: $className more than once aliased… will be overwritten by $namespace";
            }
            $namespaces[$className] = $namespace;
        }
        // alternative: natcasesort
        ksort($namespaces);

        return $namespaces;
    }

    public static function resolve_class_name($className)
    {
        if (!isset(self::class_mapping()[$className])) {
            throw new MappedClassNotFoundException("$className is not included in the class mapping.");
        }

        return self::class_mapping()[$className];
    }

    public function getName(): ?string
    {
        return self::$defaultName ?? 'namespaces';
    }
}
