<?php

// autoload_real.php @generated by Composer

class ComposerAutoloaderInit5f3bde11859a527d2ce9e0e968ec3703
{
    private static $loader;

    public static function loadClassLoader($class)
    {
        if ('Composer\Autoload\ClassLoader' === $class) {
            require __DIR__ . '/ClassLoader.php';
        }
    }

    /**
     * @return \Composer\Autoload\ClassLoader
     */
    public static function getLoader()
    {
        if (null !== self::$loader) {
            return self::$loader;
        }

        spl_autoload_register(array('ComposerAutoloaderInit5f3bde11859a527d2ce9e0e968ec3703', 'loadClassLoader'), true, true);
        self::$loader = $loader = new \Composer\Autoload\ClassLoader(\dirname(__DIR__));
        spl_autoload_unregister(array('ComposerAutoloaderInit5f3bde11859a527d2ce9e0e968ec3703', 'loadClassLoader'));

        require __DIR__ . '/autoload_static.php';
        call_user_func(\Composer\Autoload\ComposerStaticInit5f3bde11859a527d2ce9e0e968ec3703::getInitializer($loader));

        $loader->register(true);

        return $loader;
    }
}
