<?php

namespace LIG\Command;

use Monolog\Logger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ConsumeCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('consume')
            ->setDescription('Consume messages from RabbitMQ')
            ->addOption('queue_name', null, InputOption::VALUE_REQUIRED, 'Name of queue to consume', 'sample')
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $queue_name = $input->getOption('queue_name');

        // Connection
        $connection = new \AMQPConnection(array(
            'host' => getenv('RABBITMQ_PORT_5672_TCP_ADDR'),
            'port' => getenv('RABBITMQ_PORT_5672_TCP_PORT'),
            'login' => getenv('APP_BROKER_LOGIN'),
            'password' => getenv('APP_BROKER_PASSWORD'),
            'vhost' => getenv('APP_BROKER_VHOST')
        ));
        $connection->connect();
        $channel = new \AMQPChannel($connection);

        // MessageProvider
        $queue = new \AMQPQueue($channel);
        $queue->setName($queue_name);
        $messageProvider = new \Swarrot\Broker\MessageProvider\PeclPackageMessageProvider($queue);

        // MessagePublisher
        $exchange = new \AMQPExchange($channel);
        $exchange->setName($queue_name . '_retry');
        $messagePublisher = new \Swarrot\Broker\MessagePublisher\PeclPackageMessagePublisher($exchange);

        // Logger
        $logger = new Logger('main');
        $logger->pushHandler(new \Monolog\Handler\ErrorLogHandler());

        $sampleProcessor = new \LIG\Extensions\Swarrot\Processor\SampleProcessor();

        $stack = (new \Swarrot\Processor\Stack\Builder())
            ->push('Swarrot\Processor\Retry\RetryProcessor',
                $messagePublisher,
                $logger
            )// The goal is to re-published messages in broker when an error occurred.
            ->push('Swarrot\Processor\Ack\AckProcessor',
                $messageProvider, $logger) // The goal is to ack (or NACK) messages when needed.
        ;

        $processor = $stack->resolve($sampleProcessor);
        $consumer = new \Swarrot\Consumer($messageProvider, $processor);


        $consumer->consume(array(
            'retry_key_pattern' => $queue_name . '_retry_%attempt%',
            'retry_attempts' => 3
        ));

    }
}
