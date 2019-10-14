<?php
#coding: utf-8
# +-------------------------------------------------------------------
# | 运行控制台
# +-------------------------------------------------------------------
# | Copyright (c) 2017-2019 Sower rights reserved.
# +-------------------------------------------------------------------
# +-------------------------------------------------------------------
declare (strict_types = 1);

namespace sower\console\command;

use sower\console\Command;
use sower\console\Input;
use sower\console\Output;

class ServiceDiscover extends Command
{
    public function configure()
    {
        $this->setName('service:discover')
            ->setDescription('Discover Services for Sower');
    }

    public function execute(Input $input, Output $output)
    {
        if (is_file($path = $this->app->getRootPath() . 'vendor/composer/installed.json')) {
            $packages = json_decode(@file_get_contents($path), true);

            $services = [];
            foreach ($packages as $package) {
                if (!empty($package['extra']['sower']['services'])) {
                    $services = array_merge($services, (array) $package['extra']['sower']['services']);
                }
            }

            $header = '// This cache file is automatically generated at:' . date('Y-m-d H:i:s') . PHP_EOL . 'declare (strict_types = 1);' . PHP_EOL;

            $content = '<?php ' . PHP_EOL . $header . "return " . var_export($services, true) . ';';

            if (!is_dir($runtimePath = $this->app->getRuntimePath())) {
                mkdir($runtimePath, 0755, true);
            }

            file_put_contents($runtimePath . 'services.php', $content);

            $output->writeln('<info>Succeed!</info>');
        }

    }
}
