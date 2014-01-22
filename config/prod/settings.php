<?

/**
 * Shared configuration for production environments
 */
return array(
    'app' => array(
        'php' => array(
            'error_reporting' =>  E_ALL & ~E_DEPRECATED & ~E_STRICT,
            'display_errors' => false,
            'display_startup_errors' => false
        )
    ),

    'dispatcher' => array(
        'exceptionOnError' => false
    ),

    'view' => array(
        'renderer' => array(
            'class' => 'php',
            'sanitization' => true
        )
    ),

    'auth' => array(
        'expirationTime' => 3 * 60 * 60
    )
);