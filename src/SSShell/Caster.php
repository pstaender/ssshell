<?php
namespace PStaender\SSShell;

class Caster
{

    private static $include_relationships = true;
    private static $_nesting_level = 0;

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
