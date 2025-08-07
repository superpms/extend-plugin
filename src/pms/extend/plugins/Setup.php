<?php

namespace pms\extend\plugins;

use pms\contract\LifecycleInterface;
use pms\facade\Path;

class Setup implements LifecycleInterface
{

    protected static string $rootPath;

    public static function start(string $rootPath, \pms\program\boot\Options $bootOptions)
    {

        static::$rootPath = $rootPath;

        /**
         * 加载插件autoload文件
         */
        static::initPluginAutoloadFile($bootOptions);
    }

    protected static function initPluginAutoloadFile(\pms\program\boot\Options $bootOptions): void{
        $pluginDir = path_join(static::$rootPath,$bootOptions->extend?->plugins ?? '/plugins');
        Path::mount('pluginsRoot',$pluginDir);
        $autoloadPackFile = Path::getPluginsRoot('autoload.php');
        if (is_file($autoloadPackFile)) {
            $pluginsConfig = include $autoloadPackFile;
            foreach ($pluginsConfig as $item) {
                $autoloadFile = Path::getPluginsRoot($item,"/autoload.php");
                if (is_file($autoloadFile)) {
                    include_once $autoloadFile;
                }
            }
        } else {
            file_create($autoloadPackFile, "<?php\r\n // 需要加载 autoload.php 文件的插件名称集合 \r\n return [];");
        }
    }

}