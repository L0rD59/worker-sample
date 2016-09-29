#### Worker Sample

This is a sample of worker using [Swarrot](https://github.com/swarrot/swarrot "Swarrot") and consuming messages from [RabbitMQ](https://rabbitmq.com "RabbitMQ").

This sample provide this followed RabbitMQ workflow : 

One **eXchange** "sample" *type* "direct" biding to a **Queue** "sample" with *routing_key* "sample". 
It's this eXchange that we will use for publish message.

Another **eXchange** "sample_retry" *type* "direct" biding to three **Queues** : 

1. "sample_retry_1" with *x-message-ttl* to 30000 and *x-dead-letter* to "sample" biding with *routing_key* "sample_retry_1". 

2. "sample_retry_2" with *x-message-ttl* to 6000000 and *x-dead-letter* to "sample" biding with *routing_key* "sample_retry_2". 

3. "sample_retry_3" biding with *routing_key* "sample_retry_3".

Messages will be consumed from "sample" queue. During consume process if an exception is threw Swarrot will republish the message in "sample_retry" eXchange with **routing_key** "sample_retry_1". 
This message will wait 30 seconds in "sample_retry_1" queue before being republished in **x-dead-letter** "sample" by RabbitMQ to be consumed again by the worker. 
This will be repeated a second time with 5 minutes waiting before being republished in main queue.
The third time an exception is threw, the message will be stay in the "sample_retry_3" queue to be processed manually.

Note : The Swarrot SampleProcessor provided is configure to throw an exception every 3 messages consume so with enough messages, you will find one in "sample_retry_3".

### Requirements

This sample require [Docker](https://docker.com "Docker") and [Docker-Compose](https://docs.docker.com/compose/install/ "Docker-Compose").

### Installation

Clone this repository 

Up environment `docker-compose up -d`

Install composer dependencies `docker-compose run --rm composer install`

### Usage

Run `docker-compose run --rm php app/console configure` to configure RabbitMQ Exchanges and Queues

Run `docker-compose run --rm php app/console populate` to send 1000 messages in RabbitMQ

Run `docker-compose run --rm php app/console consume` to consume messages from RabbitMQ

Run `docker-compose port rabbitmq 15672` to see the address of rabbitMQ management.
