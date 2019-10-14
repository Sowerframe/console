<?php
#coding: utf-8
# +-------------------------------------------------------------------
# | 运行控制台
# +-------------------------------------------------------------------
# | Copyright (c) 2017-2019 Sower rights reserved.
# +-------------------------------------------------------------------
# +-------------------------------------------------------------------
namespace sower\console\output\driver;

use sower\console\Output;

class Buffer
{
    /**
     * @var string
     */
    private $buffer = '';

    public function __construct(Output $output)
    {
        // do nothing
    }

    public function fetch()
    {
        $content      = $this->buffer;
        $this->buffer = '';
        return $content;
    }

    public function write($messages, bool $newline = false, int $options = 0)
    {
        $messages = (array) $messages;

        foreach ($messages as $message) {
            $this->buffer .= $message;
        }
        if ($newline) {
            $this->buffer .= "\n";
        }
    }

    public function renderException(\Throwable $e)
    {
        // do nothing
    }

}
