# RabbitMQ Deadletter Exchange implementation in PHP

## Setup

`docker-compose up -d`

(RabbitMQ Management)[http://localhost:15672/]

## Messages Workflow

Send Messages
`command`
(RabbitMQ Queue)[http://localhost:15672/]

Wait X seconds, this timeout value is defined here (rabbit.ini)[conf/rabbit.ini]

Check Deadletter Exchange/Queue
`command`
(RabbitMQ Deadletter)[http://localhost:15672/]

Read Message
`command`
