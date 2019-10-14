<?php
#coding: utf-8
# +-------------------------------------------------------------------
# | 运行控制台
# +-------------------------------------------------------------------
# | Copyright (c) 2017-2019 Sower rights reserved.
# +-------------------------------------------------------------------
# +-------------------------------------------------------------------
namespace sower\console\command\optimize;

use sower\console\Command;
use sower\console\Input;
use sower\console\input\Argument;
use sower\console\Output;

class Route extends Command
{
    protected function configure()
    {
        $this->setName('optimize:routes')
            ->addArgument('app', Argument::OPTIONAL, 'app name.')
            ->setDescription('Build app routes cache.');
    }

    protected function execute(Input $input, Output $output)
    {
        $app = $input->getArgument('app');

        if (empty($app) && !is_dir($this->app->getBasePath() . 'controller')) {
            $output->writeln('<error>Miss app name!</error>');
            return false;
        }

        $path = $this->app->getRootPath() . 'runtime' . DIRECTORY_SEPARATOR . ($app ? $app . DIRECTORY_SEPARATOR : '');

        $filename = $path . 'routes.php';
        if (is_file($filename)) {
            unlink($filename);
        }

        file_put_contents($filename, $this->buildRouteCache($app));
        $output->writeln('<info>Succeed!</info>');
    }

    protected function buildRouteCache(string $app = null): string
    {
        $this->app->routes->clear();
        $this->app->routes->lazy(false);

        // 路由检测
        $path = $this->app->getRootPath() . 'routes' . DIRECTORY_SEPARATOR . ($app ? $app . DIRECTORY_SEPARATOR : '');

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

        $content = '<?php ' . PHP_EOL . 'return ';
        $content .= '\sower\App::unserialize(\'' . \sower\App::serialize($this->app->routes->getName()) . '\');';
        return $content;
    }

}
