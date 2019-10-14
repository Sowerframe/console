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
use sower\console\input\Option;
use sower\console\Output;

class Schema extends Command
{
    protected function configure()
    {
        $this->setName('optimize:schema')
            ->addArgument('app', Argument::OPTIONAL, 'app name .')
            ->addOption('db', null, Option::VALUE_REQUIRED, 'db name .')
            ->addOption('table', null, Option::VALUE_REQUIRED, 'table name .')
            ->setDescription('Build database schema cache.');
    }

    protected function execute(Input $input, Output $output)
    {
        $app = $input->getArgument('app');

        if (empty($app) && !is_dir($this->app->getBasePath() . 'controller')) {
            $output->writeln('<error>Miss app name!</error>');
            return false;
        }

        if ($app) {
            $runtimePath = $this->app->getRootPath() . 'runtime' . DIRECTORY_SEPARATOR . $app . DIRECTORY_SEPARATOR;
            $appPath     = $this->app->getBasePath() . $app . DIRECTORY_SEPARATOR;
            $namespace   = 'app\\' . $app;
        } else {
            $runtimePath = $this->app->getRootPath() . 'runtime' . DIRECTORY_SEPARATOR;
            $appPath     = $this->app->getBasePath();
            $namespace   = 'app';
        }

        $schemaPath = $runtimePath . 'schema' . DIRECTORY_SEPARATOR;
        if (!is_dir($schemaPath)) {
            mkdir($schemaPath, 0755, true);
        }

        if ($input->hasOption('table')) {
            $table = $input->getOption('table');
            if (false === strpos($table, '.')) {
                $dbName = $this->app->db->getConfig('database');
            }

            $tables[] = $table;
        } elseif ($input->hasOption('db')) {
            $dbName = $input->getOption('db');
            $tables = $this->app->db->getConnection()->getTables($dbName);
        } else {

            $path = $appPath . 'model';
            $list = is_dir($path) ? scandir($path) : [];

            foreach ($list as $file) {
                if (0 === strpos($file, '.')) {
                    continue;
                }
                $class = '\\' . $namespace . '\\model\\' . pathinfo($file, PATHINFO_FILENAME);
                $this->buildModelSchema($schemaPath, $class);
            }

            $output->writeln('<info>Succeed!</info>');
            return;
        }

        $db = isset($dbName) ? $dbName . '.' : '';
        $this->buildDataBaseSchema($schemaPath, $tables, $db);

        $output->writeln('<info>Succeed!</info>');
    }

    protected function buildModelSchema(string $path, string $class): void
    {
        $reflect = new \ReflectionClass($class);
        if (!$reflect->isAbstract() && $reflect->isSubclassOf('\sower\Model')) {

            /** @var \sower\Model $model */
            $model = new $class;

            $table   = $model->getTable();
            $dbName  = $model->getConfig('database');
            $content = '<?php ' . PHP_EOL . 'return ';
            $info    = $model->db()->getConnection()->getFields($table);
            $content .= var_export($info, true) . ';';

            file_put_contents($path . $dbName . '.' . $table . '.php', $content);
        }
    }

    protected function buildDataBaseSchema(string $path, array $tables, string $db): void
    {
        if ('' == $db) {
            $dbName = $this->app->db->getConfig('database') . '.';
        } else {
            $dbName = $db;
        }

        foreach ($tables as $table) {
            $content = '<?php ' . PHP_EOL . 'return ';
            $info    = $this->app->db->getConnection()->getFields($db . $table);
            $content .= var_export($info, true) . ';';
            file_put_contents($path . $dbName . $table . '.php', $content);
        }
    }
}
