<?php
declare (strict_types = 1);

namespace catcher\command\Tools;

use catcher\facade\Http;
use catcher\Tree;
use catcher\Utils;
use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;
use think\facade\Db;

class ExportDataCommand extends Command
{
    protected $table;

    protected function configure()
    {
        // 指令配置
        $this->setName('export')
            ->addArgument('table', Argument::REQUIRED, 'export tables')
            ->addOption('pid', '-p', Option::VALUE_REQUIRED, 'parent level name')
            ->setDescription('Just for catchAdmin export data');
    }

    protected function execute(Input $input, Output $output)
    {
        $table = Utils::tablePrefix() . $input->getArgument('table');

        $parent = $input->getOption('pid');

        $data = Db::name($table)->where('deleted_at', 0)->select()->toArray();

        if ($parent) {
            $data = Tree::done($data, 0, $parent);
        }

        file_put_contents(root_path() . DIRECTORY_SEPARATOR . $table . '.php', "<?php\r\n return " . var_export($data, true) . ';');

        $output->info('succeed!');
    }
}

