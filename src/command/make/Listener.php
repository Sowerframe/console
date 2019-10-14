<?php
#coding: utf-8
# +-------------------------------------------------------------------
# | 运行控制台
# +-------------------------------------------------------------------
# | Copyright (c) 2017-2019 Sower rights reserved.
# +-------------------------------------------------------------------
# +-------------------------------------------------------------------
namespace sower\console\command\make;

use sower\console\command\Make;

class Listener extends Make
{
    protected $type = "Listener";

    protected function configure()
    {
        parent::configure();
        $this->setName('make:listener')
            ->setDescription('Create a new listener class');
    }

    protected function getStub(): string
    {
        return __DIR__ . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR . 'listener.stub';
    }

    protected function getNamespace(string $app): string
    {
        return parent::getNamespace($app) . '\\listener';
    }
}
