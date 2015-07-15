<?php

namespace LIG\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class PopulateCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('populate')
            ->setDescription('To populate RabbitMQ of messages.')
            ->addOption('exchange_name', null, InputOption::VALUE_REQUIRED, 'Name of exchange to publish', 'sample')
            ->addOption('number', null, InputOption::VALUE_REQUIRED, 'Number of messages', '1000')
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $exchange_name = $input->getOption('exchange_name');
        $number = $input->getOption('number');


        $connection = new \AMQPConnection(array(
            'host' => getenv('RABBITMQ_PORT_5672_TCP_ADDR'),
            'port' => getenv('RABBITMQ_PORT_5672_TCP_PORT'),
            'login' => getenv('APP_BROKER_LOGIN'),
            'password' => getenv('APP_BROKER_PASSWORD'),
            'vhost' => getenv('APP_BROKER_VHOST')
        ));
        $connection->connect();
        $channel = new \AMQPChannel($connection);
        $exchange = new \AMQPExchange($channel);
        $exchange->setName($exchange_name);

        for($i=1; $i <= $number; $i++)
        {
            $exchange->publish('Hello World !', $exchange_name);

            if($output->getVerbosity() > OutputInterface::VERBOSITY_NORMAL){
                $output->writeln('Message #'. $i . ' publish.');
            }
        }


    }
}
