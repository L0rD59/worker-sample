<?php
namespace LIG\Extensions\Console;

use LIG\Command\ConfigureCommand;
use LIG\Command\ConsumeCommand;
use LIG\Command\PopulateCommand;
use Symfony\Component\Console\Application as BaseApplication;

class Application extends BaseApplication
{
    public function __construct()
    {
        parent::__construct('Worker Sample', '1.0');
    }

    public function getDefaultCommands()
    {
        $defaultsCommands = parent::getDefaultCommands();

        $defaultsCommands[] = new ConfigureCommand();
        $defaultsCommands[] = new ConsumeCommand();
        $defaultsCommands[] = new PopulateCommand();

        return $defaultsCommands;
    }
}