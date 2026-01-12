<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Routing extends BaseConfig
{
    /**
     * Default namespace
     */
    public $defaultNamespace = 'App\Controllers';

    /**
     * Default controller
     */
    public $defaultController = 'Home';

    /**
     * Default method
     */
    public $defaultMethod = 'index';

    /**
     * Translate URI dashes
     */
    public $translateURIDashes = false;

    /**
     * 404 override
     */
    public $override404 = '';


    /**
     * Enable Auto Routing (Legacy)
     */
    public $autoRoute = true;

    /**
     * Route priority (REQUIRED CI 4.4+)
     */
    public $prioritize = false;

    /**
     * Route files
     * INI YANG ERROR TADI PAGI 🔥
     */
    public $routeFiles = [
        APPPATH . 'Config/Routes.php',
    ];

    /**
     * Multiple HTTP verbs
     */
    public $methods = [];

    /**
     * Placeholder types
     */
    public $placeholderTypes = [
        'any'      => '.*',
        'segment'  => '[^/]+',
        'num'      => '[0-9]+',
        'alpha'    => '[a-zA-Z]+',
        'alphanum' => '[a-zA-Z0-9]+',
        'hash'     => '[^/]+',
    ];

    /**
     * Route filters
     */
    public $filters = [];
}
