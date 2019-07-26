# RabbitMQ Deadletter Exchange implementation in PHP

## Setup

#### Rename [rabbitmq.ini.dist](config/rabbitmq.ini.dist) to rabbitmq.ini

`mv config/rabbitmq.ini.dist config/rabbitmq.ini`

#### Start docker containers in background

`docker-compose up -d`

#### Config deadletter exchange/queue/routing-key

`docker exec  -it rabbit-deadletter_php_1  bin/console rabbit:setup`

## Messages Workflow

#### Send Messages

`docker exec  -it rabbit-deadletter_php_1  bin/console rabbit:message:publish`

Wait X seconds, this timeout value is defined on the **ttl** atribute from [rabbitmq.ini](config/rabbitmq.ini)

#### Check Deadletter Queue

[RabbitMQ Deadletter Queue](http://localhost:15672/#/queues/%2F/deadletter-queue)

#### Read Messages

`docker exec  -it rabbit-deadletter_php_1  bin/worker`

## Final Thoughts

Fine for delay re-queue messages or _basic.nack_.

Not for a Unacked Message that sits there for a long time