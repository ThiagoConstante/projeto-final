<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitd346d7f978ba3e143c9456d22505f3a3
{
    public static $prefixLengthsPsr4 = array (
        'F' => 
        array (
            'Firebase\\JWT\\' => 13,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Firebase\\JWT\\' => 
        array (
            0 => __DIR__ . '/..' . '/firebase/php-jwt/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitd346d7f978ba3e143c9456d22505f3a3::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitd346d7f978ba3e143c9456d22505f3a3::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitd346d7f978ba3e143c9456d22505f3a3::$classMap;

        }, null, ClassLoader::class);
    }
}
