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
use sower\console\input\Argument as InputArgument;
use sower\console\input\Option as InputOption;
use sower\console\Output;

class Help extends Command
{

    private $command;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->ignoreValidationErrors();

        $this->setName('help')->setDefinition([
            new InputArgument('command_name', InputArgument::OPTIONAL, 'The command name', 'help'),
            new InputOption('raw', null, InputOption::VALUE_NONE, 'To output raw command help'),
        ])->setDescription('Displays help for a command')->setHelp(<<<EOF
The <info>%command.name%</info> command displays help for a given command:

  <info>php %command.full_name% list</info>

To display the list of available commands, please use the <info>list</info> command.
EOF
        );
    }

    /**
     * Sets the command.
     * @param Command $command The command to set
     */
    public function setCommand(Command $command): void
    {
        $this->command = $command;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(Input $input, Output $output)
    {
        if (null === $this->command) {
            $this->command = $this->getConsole()->find($input->getArgument('command_name'));
        }

        $output->describe($this->command, [
            'raw_text' => $input->getOption('raw'),
        ]);

        $this->command = null;
    }
}
