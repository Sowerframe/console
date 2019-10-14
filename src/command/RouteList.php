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
use sower\console\Table;

class RouteList extends Command
{
    protected $sortBy = [
        'rule'   => 0,
        'routes'  => 1,
        'method' => 2,
        'name'   => 3,
        'domain' => 4,
    ];

    protected function configure()
    {
        $this->setName('routes:list')
            ->addArgument('app', Argument::OPTIONAL, 'app name .')
            ->addArgument('style', Argument::OPTIONAL, "the style of the table.", 'default')
            ->addOption('sort', 's', Option::VALUE_OPTIONAL, 'order by rule name.', 0)
            ->addOption('more', 'm', Option::VALUE_NONE, 'show routes options.')
            ->setDescription('show routes list.');
    }

    protected function execute(Input $input, Output $output)
    {
        $app = $input->getArgument('app');

        if (empty($app) && !is_dir($this->app->getBasePath() . 'controller')) {
            $output->writeln('<error>Miss app name!</error>');
            return false;
        }

        if ($app) {
            $filename = $this->app->getRootPath() . 'runtime' . DIRECTORY_SEPARATOR . $app . DIRECTORY_SEPARATOR . 'route_list_' . $app . '.php';
        } else {
            $filename = $this->app->getRootPath() . 'runtime' . DIRECTORY_SEPARATOR . 'route_list.php';
        }

        if (is_file($filename)) {
            unlink($filename);
        } elseif (!is_dir(dirname($filename))) {
            mkdir(dirname($filename), 0755);
        }

        $content = $this->getRouteList($app);
        file_put_contents($filename, 'Route List' . PHP_EOL . $content);
    }

    protected function getRouteList(string $app = null): string
    {
        $this->app->routes->setTestMode(true);
        $this->app->routes->clear();

        if ($app) {
            $path = $this->app->getRootPath() . 'routes' . DIRECTORY_SEPARATOR . $app . DIRECTORY_SEPARATOR;
        } else {
            $path = $this->app->getRootPath() . 'routes' . DIRECTORY_SEPARATOR;
        }

        $files = is_dir($path) ? scandir($path) : [];

        foreach ($files as $file) {
            if (strpos($file, '.php')) {
                include $path . $file;
            }
        }

        if ($this->app->config->get('routes.route_annotation')) {
            $this->app->console->call('routes:build', [$app ?: '']);
            $filename = $this->app->getRootPath() . 'runtime' . DIRECTORY_SEPARATOR . ($app ? $app . DIRECTORY_SEPARATOR : '') . 'build_route.php';

            if (is_file($filename)) {
                include $filename;
            }
        }

        $table = new Table();

        if ($this->input->hasOption('more')) {
            $header = ['Rule', 'Route', 'Method', 'Name', 'Domain', 'Option', 'Pattern'];
        } else {
            $header = ['Rule', 'Route', 'Method', 'Name'];
        }

        $table->setHeader($header);

        $routeList = $this->app->routes->getRuleList();
        $rows      = [];

        foreach ($routeList as $item) {
            $item['routes'] = $item['routes'] instanceof \Closure ? '<Closure>' : $item['routes'];

            if ($this->input->hasOption('more')) {
                $item = [$item['rule'], $item['routes'], $item['method'], $item['name'], $item['domain'], json_encode($item['option']), json_encode($item['pattern'])];
            } else {
                $item = [$item['rule'], $item['routes'], $item['method'], $item['name']];
            }

            $rows[] = $item;
        }

        if ($this->input->getOption('sort')) {
            $sort = strtolower($this->input->getOption('sort'));

            if (isset($this->sortBy[$sort])) {
                $sort = $this->sortBy[$sort];
            }

            uasort($rows, function ($a, $b) use ($sort) {
                $itemA = $a[$sort] ?? null;
                $itemB = $b[$sort] ?? null;

                return strcasecmp($itemA, $itemB);
            });
        }

        $table->setRows($rows);

        if ($this->input->getArgument('style')) {
            $style = $this->input->getArgument('style');
            $table->setStyle($style);
        }

        return $this->table($table);
    }

}
