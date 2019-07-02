<?php
namespace PStaender\SSShell;

use Psy\Configuration;
use Psy\Shell;

class SilverStripeShell
{

    const VERSION = '0.0.1';

    private static $using_namespaces = [
        'SilverStripe\i18n\i18n',
        'SilverStripe\SiteConfig\SiteConfig',
        'SilverStripe\Security\Security',
        'SilverStripe\Security\Member',
        'SilverStripe\Security\Group',
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
        'SilverStripe\Config\Config',
        'SilverStripe\CampaignAdmin\CampaignAdmin',
        'SilverStripe\CMS\CMS',
        'SilverStripe\ORM\DataObject',
        'SilverStripe\CMS\Model\SiteTree',
    ];

    private static $shell_config = [
        'usePcntl' => false,
        'startupMessage' => null,
    ];

    private static $include_relationships = true;

    private static $_nesting_level = 0;

    private $shell = null;

    public function run()
    {
        return $this->shell()->run();
    }

    private function shell()
    {
        $this->shell = new Shell($this->shellConfig());
        foreach (self::$using_namespaces as $className) {
            if (class_exists($className)) {
                $this->shell->addInput("use $className");
            }
        }
        $this->shell->addCommands($this->getCommands());
        return $this->shell;
    }

    private function getCommands()
    {
        return [
            new SilverStripeSakeCommand(),
        ];
    }

    private function shellConfig()
    {
        $shellConfig = self::$shell_config;
        if (!$shellConfig['startupMessage']) {
            $shellConfig['startupMessage'] = "Loading " . \SilverStripe\Control\Director::get_environment_type() . " environment (SilverStripe Framework ^ v" . \SilverStripe\Dev\Deprecation::dump_settings()['version'] . ")";
        }
        $config = new Configuration($shellConfig);
        $config->getPresenter()->addCasters(
            $this->getCasters()
        );
        return $config;
    }

    // TODO: move to separate cast class

    private function getCasters()
    {
        return [
            'SilverStripe\ORM\DataObject' => self::class . '::castModel',
            'SilverStripe\ORM\DataList' => self::class . '::castList',
            'SilverStripe\ORM\ArrayList' => self::class . '::castList',
            'SilverStripe\ORM\Connect\Query' => self::class . '::castQuery',
        ];
    }

    public static function castModel($model)
    {
        $hasOne = [];
        $hasMany = [];
        $manyMany = [];

        if (self::$include_relationships) {
            foreach ($model->hasOne() as $name => $modelName) {
                if ($model->ID === 0) {
                    continue;
                }
                $hasOne[$name] = $model->$name();
            }
            foreach ($many = $model->hasMany() as $relationship => $class) {
                $hasMany[$relationship] = [];
                if ($relations = $model->$relationship()) {
                    $_nesting_level++;
                    if ($_nesting_level > 1) {
                        $_nesting_level--;
                        continue;
                    }
                    $hasMany[$relationship] = self::castList($relations);
                    $_nesting_level--;
                }
            }
            foreach ($many = $model->manyMany() as $relationship => $class) {
                $manyMany[$relationship] = [];
                if ($relations = $model->$relationship()) {
                    $_nesting_level++;
                    if ($_nesting_level > 1) {
                        $_nesting_level--;
                        continue;
                    }
                    $manyMany[$relationship] = self::castList($relations);
                    $_nesting_level--;
                }
            }
        }
        $attributes = array_merge(
            $model->toMap(), $hasOne, $hasMany, $manyMany
        );
        return $attributes;
    }

    public static function castList($model)
    {
        return $model->toNestedArray();
    }
    public static function castQuery($model)
    {
        return ['value' => $model->value(), 'map' => $model->map()];
    }
}
