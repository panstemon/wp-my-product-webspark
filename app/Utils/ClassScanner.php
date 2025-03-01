<?php

namespace WPMyProductWebspark\Utils;

class ClassScanner {
    public static function scanAppNamespaceForSubclasses(string $namespace, string $parentClass): array {
        $classes = self::scanAppNamespaceForClasses($namespace);
        $subclasses = [];

        foreach ($classes as $class) {
            if (strpos($class, $namespace) === 0 && is_subclass_of($class, $parentClass)) {
                $subclasses[] = $class;
            }
        }

        return $subclasses;
    }

    public static function scanAppNamespaceForClasses(string $namespace): array {
        $path = str_replace('WPMyProductWebspark', WPMPW_PATH . '/app', $namespace);

        $phpFiles = glob($path . '/*.php');
        $classes = [];

        foreach($phpFiles as $file) {
            $name = basename($file, '.php');
            $className = $namespace . '\\' . $name;
            $classes[] = $className;
        }

        return $classes;
    }
}