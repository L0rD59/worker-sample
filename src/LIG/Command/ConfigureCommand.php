<?php

namespace LIG\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ConfigureCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('configure')
            ->setDescription('To configure RabbitMQ workflow (exchanges and queues).')
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
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
        $exchange->setType(AMQP_EX_TYPE_DIRECT);
        $exchange->setFlags(AMQP_DURABLE);
        $exchange->setName('sample');
        $exchange->declareExchange();

        $exchangeRetry = new \AMQPExchange($channel);
        $exchangeRetry->setType(AMQP_EX_TYPE_DIRECT);
        $exchangeRetry->setFlags(AMQP_DURABLE);
        $exchangeRetry->setName('sample_retry');
        $exchangeRetry->declareExchange();


        $queue = new \AMQPQueue($channel);
        $queue->setName('sample');
        $queue->setFlags(AMQP_DURABLE);
        $queue->declareQueue();
        $queue->bind('sample', 'sample');

        $queueRetry1 = new \AMQPQueue($channel);
        $queueRetry1->setName('sample_retry_1');
        $queueRetry1->setFlags(AMQP_DURABLE);
        $queueRetry1->setArguments(array(
            'x-message-ttl' => 30000,
            'x-dead-letter-exchange' => 'sample_retry',
            'x-dead-letter-routing-key' => 'sample_retry'
        ));
        $queueRetry1->declareQueue();
        $queueRetry1->bind('sample_retry', 'sample_retry_1');

        $queueRetry2 = new \AMQPQueue($channel);
        $queueRetry2->setName('sample_retry_2');
        $queueRetry2->setFlags(AMQP_DURABLE);
        $queueRetry2->setArguments(array(
            'x-message-ttl' => 600000,
            'x-dead-letter-exchange' => 'sample_retry',
            'x-dead-letter-routing-key' => 'sample_retry'
        ));
        $queueRetry2->declareQueue();
        $queueRetry2->bind('sample_retry', 'sample_retry_2');

        $queueRetry2 = new \AMQPQueue($channel);
        $queueRetry2->setName('sample_retry_3');
        $queueRetry2->setFlags(AMQP_DURABLE);
        $queueRetry2->declareQueue();
        $queueRetry2->bind('sample_retry', 'sample_retry_3');

    }
}
