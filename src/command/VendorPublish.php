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
use sower\console\input\Option;

class VendorPublish extends Command
{
    public function configure()
    {
        $this->setName('vendor:publish')
            ->addOption('force', 'f', Option::VALUE_NONE, 'Overwrite any existing files')
            ->setDescription('Publish any publishable assets from vendor packages');
    }

    public function handle()
    {

        $force = $this->input->getOption('force');

        if (is_file($path = $this->app->getRootPath() . 'vendor/composer/installed.json')) {
            $packages = json_decode(@file_get_contents($path), true);

            foreach ($packages as $package) {
                //配置
                $configDir = $this->app->getConfigPath();

                if (!empty($package['extra']['sower']['config'])) {

                    $installPath = $this->app->getRootPath() . 'vendor/' . $package['name'] . DIRECTORY_SEPARATOR;

                    foreach ((array) $package['extra']['sower']['config'] as $name => $file) {

                        $target = $configDir . $name . '.php';
                        $source = $installPath . $file;

                        if (is_file($target) && !$force) {
                            $this->output->info("File {$target} exist!");
                            continue;
                        }

                        if (!is_file($source)) {
                            $this->output->info("File {$source} not exist!");
                            continue;
                        }

                        copy($source, $target);
                    }
                }
            }

            $this->output->writeln('<info>Succeed!</info>');
        }
    }
}
