<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitd90b572808bdd826142080a9a9dc6f78
{
    public static $prefixLengthsPsr4 = array (
        'z' => 
        array (
            'zil\\' => 4,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'zil\\' => 
        array (
            0 => __DIR__ . '/../..' . '/zil',
        ),
    );

    public static $classMap = array (
        'zil\\App' => __DIR__ . '/../..' . '/zil/main.php',
        'zil\\config\\Config' => __DIR__ . '/../..' . '/zil/config/config.php',
        'zil\\factory\\Authentication' => __DIR__ . '/../..' . '/zil/factory/authentication.php',
        'zil\\factory\\BuildQuery' => __DIR__ . '/../..' . '/zil/factory/buildQuery.php',
        'zil\\factory\\Database' => __DIR__ . '/../..' . '/zil/factory/database.php',
        'zil\\factory\\Filehandler' => __DIR__ . '/../..' . '/zil/factory/filehandler.php',
        'zil\\factory\\Fileuploader' => __DIR__ . '/../..' . '/zil/factory/fileuploader.php',
        'zil\\factory\\Logger' => __DIR__ . '/../..' . '/zil/factory/logger.php',
        'zil\\factory\\Redirect' => __DIR__ . '/../..' . '/zil/factory/redirect.php',
        'zil\\factory\\Sanitize' => __DIR__ . '/../..' . '/zil/factory/sanitize.php',
        'zil\\factory\\Security' => __DIR__ . '/../..' . '/zil/factory/security.php',
        'zil\\factory\\Session' => __DIR__ . '/../..' . '/zil/factory/session.php',
        'zil\\factory\\View' => __DIR__ . '/../..' . '/zil/factory/view.php',
        'zil\\server\\Request' => __DIR__ . '/../..' . '/zil/server/request.php',
        'zil\\server\\Response' => __DIR__ . '/../..' . '/zil/server/response.php',
        'zil\\server\\Router' => __DIR__ . '/../..' . '/zil/server/router.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitd90b572808bdd826142080a9a9dc6f78::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitd90b572808bdd826142080a9a9dc6f78::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitd90b572808bdd826142080a9a9dc6f78::$classMap;

        }, null, ClassLoader::class);
    }
}
