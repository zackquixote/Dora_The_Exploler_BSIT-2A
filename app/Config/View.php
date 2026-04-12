<?php

namespace Config;

use CodeIgniter\Config\View as BaseView;
use CodeIgniter\View\ViewDecoratorInterface;

class View extends BaseView
{
    /**
     * When false, the view method will clear the data between each
     * call. This keeps your data safe, ensuring no data is leaked
     * between two views in the same controller
     */
    public $saveData = true;

    /**
     * Parser Filters map a filter name with any PHP callable.
     * When using the Parser, it will find variables that end with
     * the filter name and apply the filter to the value.
     *
     * Example:
     *   { name|capitalize }
     */
    public $filters = [];

    /**
     * Parser Plugins provide the ability to extend the parser
     * and add your own custom tags or filters.
     */
    public $plugins = [];

    /**
     * Built-in View Decorators
     *
     * This is an array of class names that will be called
     * after a view file is loaded. The classes will be called
     * in the order they are listed, and will be passed the
     * current $view object as the first parameter.
     */
    public array $decorators = [];

    /**
     * Built-in View Decorators that are triggered
     * immediately after the controller method is called.
     */
    public array $controllerDecorators = [];
}