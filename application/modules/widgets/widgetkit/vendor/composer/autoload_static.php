<?php

// autoload_static.php @generated by Composer

namespace YOOtheme\Autoload;

class ComposerStaticInit9d6570cfe0f7bd6e82e86f3d54822a4b
{
    public static $prefixLengthsPsr4 = array (
        'Y' => 
        array (
            'YOOtheme\\Widgetkit\\' => 19,
            'YOOtheme\\Framework\\' => 19,
        ),
        'C' => 
        array (
            'Composer\\Installers\\' => 20,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'YOOtheme\\Widgetkit\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
        'YOOtheme\\Framework\\' => 
        array (
            0 => __DIR__ . '/..' . '/yootheme/framework/src',
        ),
        'Composer\\Installers\\' => 
        array (
            0 => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers',
        ),
    );

    public static $prefixesPsr0 = array (
        'a' => 
        array (
            'abeautifulsite' => 
            array (
                0 => __DIR__ . '/..' . '/abeautifulsite/simpleimage/src',
            ),
        ),
    );

    public static $classMap = array (
        'Codebird\\Codebird' => __DIR__ . '/..' . '/jublonet/codebird-php/src/codebird.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit9d6570cfe0f7bd6e82e86f3d54822a4b::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit9d6570cfe0f7bd6e82e86f3d54822a4b::$prefixDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticInit9d6570cfe0f7bd6e82e86f3d54822a4b::$prefixesPsr0;
            $loader->classMap = ComposerStaticInit9d6570cfe0f7bd6e82e86f3d54822a4b::$classMap;

        }, null, ClassLoader::class);
    }
}
