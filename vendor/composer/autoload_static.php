<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit413797071ea3f46cf6d84809e60f3714
{
    public static $prefixLengthsPsr4 = array (
        's' => 
        array (
            'src\\' => 4,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'src\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'src\\timetable\\config\\config' => __DIR__ . '/../..' . '/src/timetable/config/config.php',
        'src\\timetable\\model\\datamanagementmodel' => __DIR__ . '/../..' . '/src/timetable/model/datamanagementmodel.php',
        'src\\timetable\\model\\settingsmodel' => __DIR__ . '/../..' . '/src/timetable/model/settingsmodel.php',
        'src\\timetable\\model\\timetablemodel' => __DIR__ . '/../..' . '/src/timetable/model/timetablemodel.php',
        'src\\timetable\\renderer\\course' => __DIR__ . '/../..' . '/src/timetable/renderer/course.php',
        'src\\timetable\\renderer\\day' => __DIR__ . '/../..' . '/src/timetable/renderer/day.php',
        'src\\timetable\\renderer\\department' => __DIR__ . '/../..' . '/src/timetable/renderer/department.php',
        'src\\timetable\\renderer\\faculty' => __DIR__ . '/../..' . '/src/timetable/renderer/faculty.php',
        'src\\timetable\\renderer\\home' => __DIR__ . '/../..' . '/src/timetable/renderer/home.php',
        'src\\timetable\\renderer\\login' => __DIR__ . '/../..' . '/src/timetable/renderer/login.php',
        'src\\timetable\\renderer\\logout' => __DIR__ . '/../..' . '/src/timetable/renderer/logout.php',
        'src\\timetable\\renderer\\notification' => __DIR__ . '/../..' . '/src/timetable/renderer/notification.php',
        'src\\timetable\\renderer\\settings' => __DIR__ . '/../..' . '/src/timetable/renderer/settings.php',
        'src\\timetable\\renderer\\timetable' => __DIR__ . '/../..' . '/src/timetable/renderer/timetable.php',
        'src\\timetable\\renderer\\venue' => __DIR__ . '/../..' . '/src/timetable/renderer/venue.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit413797071ea3f46cf6d84809e60f3714::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit413797071ea3f46cf6d84809e60f3714::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit413797071ea3f46cf6d84809e60f3714::$classMap;

        }, null, ClassLoader::class);
    }
}
