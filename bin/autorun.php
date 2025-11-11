<?php

use pms\extend\plugins\PluginInstallCommand;
use pms\extend\plugins\Setup;
use pms\hook\LifecycleHook;
use pms\hook\TerminalCommandHook;

if(class_exists('\pms\hook\LifecycleHook')){
    LifecycleHook::mount(LIFECYCLE_BOOT, Setup::class);
}

if(class_exists('\pms\hook\TerminalCommandHook')){
    TerminalCommandHook::mount(
        'plugin-install',
        PluginInstallCommand::class
    );
}