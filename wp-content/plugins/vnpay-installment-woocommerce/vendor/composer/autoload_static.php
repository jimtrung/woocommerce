<?php


namespace Composer\Autoload;

class ComposerAutoloaderInit8f72f3bb7c6ee32c2a5028b2e2d64888
{
    public static $prefixLengthsPsr4 = array (
        'W' => 
        array (
            'vnpayInst\\Traits\\' => 17,
            'vnpayInst\\Shortcodes\\' => 21,
            'vnpayInst\\InstGateway\\' => 19,
            'vnpayInst\\' => 10,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'vnpayInst\\Traits\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src/traits',
        ),
        'vnpayInst\\Shortcodes\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src/shortcodes',
        ),
        'vnpayInst\\InstGateway\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src/InstGateway',
        ),
        'vnpayInst\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'vnpayInst\\Facades\\FacadeResponse' => __DIR__ . '/../..' . '/src/FacadeResponse.php',
        'vnpayInst\\InstGateway\\vnpayInstGateway' => __DIR__ . '/../..' . '/src/InstGateway/international/vnpayInstGateway.php',
        'vnpayInst\\InstGateway\\vnpayInternationalResponse' => __DIR__ . '/../..' . '/src/InstGateway/international/vnpayInternationalResponse.php',
        'vnpayInst\\Page' => __DIR__ . '/../..' . '/src/Page.php',
        'vnpayInst\\Responses\\vnpayResponse' => __DIR__ . '/../..' . '/src/vnpayResponse.php',
        'vnpayInst\\Shortcodes\\Thankyou' => __DIR__ . '/../..' . '/src/shortcodes/Thankyou.php',
        'vnpayInst\\Traits\\Pages' => __DIR__ . '/../..' . '/src/traits/Pages.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerAutoloaderInit8f72f3bb7c6ee32c2a5028b2e2d64888::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerAutoloaderInit8f72f3bb7c6ee32c2a5028b2e2d64888::$prefixDirsPsr4;
            $loader->classMap = ComposerAutoloaderInit8f72f3bb7c6ee32c2a5028b2e2d64888::$classMap;

        }, null, ClassLoader::class);
    }
}
