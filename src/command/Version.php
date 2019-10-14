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

class Version extends Command
{
    protected function configure()
    {
        // 指令配置
        $this->setName('version')
            ->setDescription('show Sower framework version');
    }

    protected function execute(Input $input, Output $output)
    {
        $output->writeln('v' . $this->app->version());
    }

}
