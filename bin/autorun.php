<?php

\pms\hook\LifecycleHook::mount(\pms\extend\plugins\Setup::class);

if(class_exists('\pms\hook\TerminalCommandHook')){
    \pms\hook\TerminalCommandHook::mount(
        'plugin-install',
        \pms\extend\plugins\PluginInstallCommand::class
    );
}