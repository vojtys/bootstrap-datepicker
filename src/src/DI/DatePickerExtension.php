<?php

namespace Vojtys\Forms\DatePicker;

use Nette;
use Nette\DI\CompilerExtension;
use Nette\Forms\Container;

/**
 * Class DatePickerExtension
 * @package Vojtys\Forms\DatePicker
 */
class DatePickerExtension extends CompilerExtension
{
    /**
     * @var array
     */
    protected $defaults = [];

    /**
     * Adjusts DI container compiled to PHP class. Intended to be overridden by descendant.
     * @return void
     */
    public function afterCompile(Nette\PhpGenerator\ClassType $class)
    {
        $config = $this->getConfig($this->defaults);
        $initialize = $class->getMethod('initialize');
        $initialize->addBody('Vojtys\Forms\DatePicker\DatePickerExtension::bind(?);', [$config]);
    }

    public static function bind($config)
    {
        // Bind to form container to cache for DatePickerInput
        Container::extensionMethod('addDatePickerInput', function ($container, $name, $label = NULL) use ($config) {
            return $container[$name] = new DatePickerInput($label, $config);
        });

        // Bind to form container to cache form DatePickerRange
        Container::extensionMethod('addDatePickerRange', function ($container, $name, $label = NULL) use ($config) {
            return $container[$name] = new DatePickerRange($label, $config);
        });
    }
}