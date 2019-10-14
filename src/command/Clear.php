<?php
#coding: utf-8
# +-------------------------------------------------------------------
# | 运行控制台
# +-------------------------------------------------------------------
# | Copyright (c) 2017-2019 Sower rights reserved.
# +-------------------------------------------------------------------
# +-------------------------------------------------------------------
namespace sower\console\command;

use sower\console\Command;
use sower\console\Input;
use sower\console\input\Argument;
use sower\console\input\Option;
use sower\console\Output;

class Clear extends Command
{
    protected function configure()
    {
        // 指令配置
        $this
            ->setName('clear')
            ->addArgument('app', Argument::OPTIONAL, 'app name .')
            ->addOption('path', 'd', Option::VALUE_OPTIONAL, 'path to clear', null)
            ->addOption('cache', 'c', Option::VALUE_NONE, 'clear cache file')
            ->addOption('log', 'l', Option::VALUE_NONE, 'clear log file')
            ->addOption('dir', 'r', Option::VALUE_NONE, 'clear empty dir')
            ->addOption('routes', 'u', Option::VALUE_NONE, 'clear routes cache')
            ->setDescription('Clear runtime file');
    }

    protected function execute(Input $input, Output $output)
    {
        if ($input->getOption('routes')) {
            $this->app->cache->clear('route_cache');
        } else {
            $app         = $input->getArgument('app');
            $runtimePath = $this->app->getRootPath() . 'runtime' . DIRECTORY_SEPARATOR . ($app ? $app . DIRECTORY_SEPARATOR : '');

            if ($input->getOption('cache')) {
                $path = $runtimePath . 'cache';
            } elseif ($input->getOption('log')) {
                $path = $runtimePath . 'log';
            } else {
                $path = $input->getOption('path') ?: $runtimePath;
            }

            $rmdir = $input->getOption('dir') ? true : false;
            $this->clear(rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR, $rmdir);
        }

        $output->writeln("<info>Clear Successed</info>");
    }

    protected function clear(string $path, bool $rmdir): void
    {
        $files = is_dir($path) ? scandir($path) : [];

        foreach ($files as $file) {
            if ('.' != $file && '..' != $file && is_dir($path . $file)) {
                array_map('unlink', glob($path . $file . DIRECTORY_SEPARATOR . '*.*'));
                if ($rmdir) {
                    rmdir($path . $file);
                }
            } elseif ('.gitignore' != $file && is_file($path . $file)) {
                unlink($path . $file);
            }
        }
    }
}
