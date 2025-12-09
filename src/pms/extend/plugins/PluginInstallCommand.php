<?php
namespace pms\extend\plugins;

use pms\annotate\Inject;
use pms\app\TerminalCommandApp;
use pms\facade\Path;
use pms\inject\TerminalInputInject;
use pms\inject\TerminalOutputInject;

class PluginInstallCommand extends TerminalCommandApp
{
    protected string $name = "plugin-install";
    protected string $description = "pms插件安装";
    protected array $validate = [
        'name' => [
            'type' => COMMAND_ARGUMENT_TYPE,
            'des' => '插件名称',
        ]
    ];

    #[Inject(TerminalInputInject::class)]
    protected TerminalInputInject $input;

    #[Inject(TerminalOutputInject::class)]
    protected TerminalOutputInject $output;

    public function entry(): void
	{
        $name = $this->input->getArgument('name');
        if (empty($name)) {
            $this->output->writeLn("请输入插件名称");
            $this->output->end();
        }
        $nameArr = explode('/', $name);
        if (count($nameArr) !== 2) {
            $this->output->writeLn("插件名称不正确");
            $this->output->end();
        }
        $path = Path::getPluginsRoot($name, "/plugin.json");
        if (!is_file($path)) {
            $this->output->writeLn("插件项目不支持");
            $this->output->end();
        }
        $info = file_get_contents($path);
        $info = json_decode($info, true);
        if ($info === null || $info === false) {
            $this->output->writeLn("插件信息不正确");
            $this->output->end();
        }
        if (!isset($info['name'])) {
            $this->output->writeLn("插件信息不正确");
            $this->output->end();
        }
        if ($name !== $info['name']) {
            $this->output->writeLn("插件名称与安装目录不相符");
            $this->output->end();
        }
        $autoloadFile = Path::getPluginsRoot($name ,"autoload.php");
        $autoloadPackFile = Path::getPluginsRoot('installed.php');
        if(is_file($autoloadFile)){
            $pluginsAutoloadPack = include $autoloadPackFile;
            $pluginsAutoloadPack = [
                ...$pluginsAutoloadPack,
                $name
            ];
            $pluginsAutoloadPack = array_unique($pluginsAutoloadPack);
            $epStr = "return [\r\n";
            foreach ($pluginsAutoloadPack as $k => $v) {
                if ($k !== 0) {
                    $epStr .= ",\r\n";
                }
                $epStr .= "    " . "'" . $v . "'";
            }
            $epStr .= "\r\n];";
            $packCode = "<?php\r\n // 需要自动加载 autoload.php 文件的插件名称集合 \r\n$epStr\r\n";
            file_create(Path::getPluginsRoot('/installed.php'), $packCode);
        }
        $this->output->writeArrayBlock([
            $this->output->setBoldStr($this->output->setColorStr(TERMINAL_COLOR_GREEN, "【插件安装成功】")),
            $this->output->setBoldStr("插件名称:") . $name,
            $this->output->setBoldStr("插件目录:") . Path::getPluginsRoot($name),
        ]);
        $this->output->end();

    }


}