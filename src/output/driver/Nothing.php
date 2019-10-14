<?php
#coding: utf-8
# +-------------------------------------------------------------------
# | SowerFrame
# +-------------------------------------------------------------------
# | Copyright (c) 2017-2019 Sower rights reserved.
# +-------------------------------------------------------------------
# +-------------------------------------------------------------------

namespace sower\console\output\driver;

use sower\console\Output;

class Nothing
{

    public function __construct(Output $output)
    {
        // do nothing
    }

    public function write($messages, bool $newline = false, int $options = 0)
    {
        // do nothing
    }

    public function renderException(\Throwable $e)
    {
        // do nothing
    }
}
