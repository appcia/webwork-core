<?

use Appcia\Webwork\Resource;

/**
 * Shared configuration for all environments (defaults)
 */
return array(
    'log' => array(
        'app' => 'module/core/log/app.log',
        'error' => 'module/core/log/error.log',
        'access' => 'module/core/log/access.log'
    ),

    'tracker' => array(
        'stepCount' => 30,
    ),

    'cache' => array(
        'proxyDir' => 'module/core/cache/proxy',
        'accelerator' => array(
            'type' => 'array'
        ),
        'data' => array(
            'type' => 'array'
        )
    ),

    'context' => array(
        'timezone' => 'Europe/Warsaw',
        'locale' => array(
            'active' => 'pl_PL',
            'list' => array('en_US', 'pl_PL')
        ),
        'translator' => array(
            'class' => 'gettext',
            'domain' => array(
                'name' => 'messages',
                'path' => 'module/core/cache/locale'
            ),
        ),
    ),

    'resource' => array(
        'manager' => array(
            'config' => array(
                Resource\Manager::UPLOAD => array(
                    'path' => 'module/core/web/upload/{token}/{key}/{filename}.{extension}',
                ),
            )
        )
    ),

    'asset' => array(
        'filter' => array(
            'namespace' => array(
                'Core\Asset\Filter'
            )
        )
    ),

    'view' => array(
        'helper' => array(
            'namespace' => array(
                'Core\View\Helper'
            )
        )
    ),
);
