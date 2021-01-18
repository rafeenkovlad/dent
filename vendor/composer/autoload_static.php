<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit0fc72c8767240f560261be6ff92fb5b7
{
    public static $prefixLengthsPsr4 = array (
        'T' => 
        array (
            'Test\\' => 5,
        ),
        'O' => 
        array (
            'Outpost\\' => 8,
        ),
        'M' => 
        array (
            'Mihanentalpo\\FastFuzzySearch\\' => 29,
        ),
        'L' => 
        array (
            'ListCSV\\' => 8,
        ),
        'F' => 
        array (
            'FunctionCommand\\' => 16,
        ),
        'D' => 
        array (
            'Dbdental\\worker\\' => 16,
            'Dbdental\\userinfo\\' => 18,
            'Dbdental\\reg\\' => 13,
            'Dbdental\\like\\' => 14,
            'Dbdental\\img\\' => 13,
            'Dbdental\\db\\' => 12,
            'Dbdental\\company\\' => 17,
            'Dbdental\\chat\\' => 14,
        ),
        'A' => 
        array (
            'Author\\rait\\' => 12,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Test\\' => 
        array (
            0 => __DIR__ . '/../..' . '/',
        ),
        'Outpost\\' => 
        array (
            0 => __DIR__ . '/../..' . '/',
        ),
        'Mihanentalpo\\FastFuzzySearch\\' => 
        array (
            0 => __DIR__ . '/..' . '/mihanentalpo/fast-fuzzy-search/src',
            1 => __DIR__ . '/..' . '/mihanentalpo/fast-fuzzy-search/src',
        ),
        'ListCSV\\' => 
        array (
            0 => __DIR__ . '/../..' . '/csv',
        ),
        'FunctionCommand\\' => 
        array (
            0 => __DIR__ . '/../..' . '/',
        ),
        'Dbdental\\worker\\' => 
        array (
            0 => __DIR__ . '/../..' . '/dbdental',
        ),
        'Dbdental\\userinfo\\' => 
        array (
            0 => __DIR__ . '/../..' . '/dbdental',
        ),
        'Dbdental\\reg\\' => 
        array (
            0 => __DIR__ . '/../..' . '/dbdental',
        ),
        'Dbdental\\like\\' => 
        array (
            0 => __DIR__ . '/../..' . '/dbdental',
        ),
        'Dbdental\\img\\' => 
        array (
            0 => __DIR__ . '/../..' . '/',
        ),
        'Dbdental\\db\\' => 
        array (
            0 => __DIR__ . '/../..' . '/dbdental',
        ),
        'Dbdental\\company\\' => 
        array (
            0 => __DIR__ . '/../..' . '/dbdental',
        ),
        'Dbdental\\chat\\' => 
        array (
            0 => __DIR__ . '/../..' . '/dbdental',
        ),
        'Author\\rait\\' => 
        array (
            0 => __DIR__ . '/../..' . '/',
        ),
    );

    public static $classMap = array (
        'Author\\rait\\Authrait' => __DIR__ . '/../..' . '/Authrait.php',
        'Dbdental\\chat\\Chat' => __DIR__ . '/../..' . '/dbdental/Chat.php',
        'Dbdental\\company\\CompanyInfo' => __DIR__ . '/../..' . '/dbdental/CompanyInfo.php',
        'Dbdental\\db\\Connect' => __DIR__ . '/../..' . '/dbdental/Connect.php',
        'Dbdental\\img\\Imgupload' => __DIR__ . '/../..' . '/Imgupload.php',
        'Dbdental\\like\\Rait' => __DIR__ . '/../..' . '/dbdental/Rait.php',
        'Dbdental\\reg\\Reg' => __DIR__ . '/../..' . '/dbdental/Reg.php',
        'Dbdental\\userinfo\\Userinfo' => __DIR__ . '/../..' . '/dbdental/Userinfo.php',
        'Dbdental\\worker\\WorkerInfo' => __DIR__ . '/../..' . '/dbdental/WorkerInfo.php',
        'FunctionCommand\\Functions' => __DIR__ . '/../..' . '/Functions.php',
        'ListCSV\\GodsList' => __DIR__ . '/../..' . '/csv/GodsList.php',
        'Mihanentalpo\\FastFuzzySearch\\FastFuzzySearch' => __DIR__ . '/..' . '/mihanentalpo/fast-fuzzy-search/src/FastFuzzySearch.php',
        'Outpost\\HelpPost' => __DIR__ . '/../..' . '/HelpPost.php',
        'Test\\Testt' => __DIR__ . '/../..' . '/Testt.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit0fc72c8767240f560261be6ff92fb5b7::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit0fc72c8767240f560261be6ff92fb5b7::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit0fc72c8767240f560261be6ff92fb5b7::$classMap;

        }, null, ClassLoader::class);
    }
}
