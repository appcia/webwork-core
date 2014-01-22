<?

/**
 * Shared configuration for development environments
 */
return array(
    'app' => array(
        'php' => array(
            'error_reporting' => E_ALL,
            'display_errors' => true,
            'display_startup_errors' => true
        )
    ),

    'asset' => array(
        'manager' => array(
            'debug' => false // enable to test modified CSS/LESS, JS
        )
    ),
);