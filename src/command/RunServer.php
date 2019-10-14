<?php declare (strict_types = 1);
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
use sower\console\input\Option;
use sower\console\Output;
class RunServer extends Command
{
    public function configure()
    {
        $this->setName('run')
            ->addOption('host', 'H', Option::VALUE_OPTIONAL,
                'The host to server the application on', '0.0.0.0')
            ->addOption('port', 'p', Option::VALUE_OPTIONAL,
                'The port to server the application on', 8000)
            ->addOption('root', 'r', Option::VALUE_OPTIONAL,
                'The document root of the application', '')
            ->setDescription('PHP Built-in Server for Sower');
    }

    public function execute(Input $input, Output $output)
    {
        $host = $input->getOption('host');
        $port = $input->getOption('port');
        $root = $input->getOption('root');
        if (empty($root)) {
            $root = $this->app->getRootPath() . 'public';
        }

        $command = sprintf(
            'php -S %s:%d -t %s %s',
            $host,
            $port,
            escapeshellarg($root),
            escapeshellarg($root . DIRECTORY_SEPARATOR . 'index.php')
        );
        $output->writeln(sprintf('
   _____                             _______					 
  / ____|                            | |  \ \
 | (___     ___   __      __   ___   | |  | |
  \___ \   / _ \  \ \ /\ / /  / _ \  | |__/ /
  ____) | | (_) |  \ V  V /  |  __/  | |  \ \ 
 |_____/   \___/    \_/\_/    \___|  | |   \ \

 ')); 
        $output->writeln(sprintf('Sower Start Successfully On <http://%s:%s/>', '0.0.0.0' == $host ? '127.0.0.1' : $host, $port));
		$output->writeln(sprintf('Quick exit <info>`ctrl-c`</info>'));
        $output->writeln(sprintf('Document root is: %s', $root));
        passthru($command);
    }

}
