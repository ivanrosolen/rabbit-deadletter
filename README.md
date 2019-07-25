# RabbitMQ Deadletter Exchange implementation in PHP

## Setup

`docker-compose up -d`

`docker exec  -it rabbit-deadletter_php_1  bin/console`

[RabbitMQ Management](http://localhost:15672/)

## Messages Workflow

Send Messages

`docker exec  -it rabbit-deadletter_php_1  bin/console rabbit:message:publish`

Wait X seconds, this timeout value is defined here [rabbit.ini](conf/rabbit.ini)

Check Deadletter Exchange/Queue

`command`

Read Message

`command`
