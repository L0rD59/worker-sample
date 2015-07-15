<?php

namespace LIG\Extensions\Swarrot\Processor;

use Swarrot\Broker\Message;
use Swarrot\Processor\ProcessorInterface;

class SampleProcessor implements  ProcessorInterface
{
    public function process(Message $message, array $options)
    {
        echo sprintf("Consume message #%d\n", $message->getId());
        if($message->getId() % 3 == 0){
            throw new \Exception('fucking exception of message #'.$message->getId());
        }
//        echo sprintf("Consume message #%d\n", $message->getId());
        echo sprintf("body : %s\n", $message->getBody());
        echo "\n";
        echo "\n";
    }
}