<?php
namespace App\Command\Rabbit;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Style\SymfonyStyle;
use PhpAmqpLib\Connection\AMQPConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Exchange\AMQPExchangeType;

class PublishMessage extends Command
{

    protected $config;

    public function __construct(Array $config)
    {
        parent::__construct();

        $this->config = $config;
    }

    protected function configure()
    {
        $this->setName('rabbit:message:publish')
             ->setDescription('Publish messages')
             ->setHelp('This command publish messages to a rabbitmq exchange');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        if (!$this->config['rabbitmq']['host'] ||
            !$this->config['rabbitmq']['port'] ||
            !$this->config['rabbitmq']['user'] ||
            !$this->config['rabbitmq']['pwd'] ||
            !$this->config['rabbitmq']['vhost'] ||
            !$this->config['rabbitmq']['ttl']
        ) {
            throw new \RuntimeException('Invalid RabbitMQ Config');
        }

        // @todo: check if setup is ok

        $amqp = new AMQPConnection(
            $this->config['rabbitmq']['host'],
            $this->config['rabbitmq']['port'],
            $this->config['rabbitmq']['user'],
            $this->config['rabbitmq']['pwd'],
            $this->config['rabbitmq']['vhost']
        );
        $rabbit = $amqp->channel();

        $io = new SymfonyStyle($input, $output);

        $exchange = $io->ask(
            'Please enter the name of the Exchange',
            'DefaultExchange');

        $queue = $io->ask(
            'Please enter the name of the Queue',
            'DefaultQueue');

        $routingKey = $io->ask(
            'Please enter the Routing Key',
            'DefaultRoutingKey');

        $end = false;
        while ($end === false) {

            $message = $io->ask(
                'Please enter the message',
                'Default Message 123',
                function ($answer) {
                    if (strlen($answer) < 3 ) {
                        throw new \RuntimeException('You must type a message with more than 3 chars');
                    }
                    return $answer;
            });

            $io->newLine();

            $rabbit->exchange_declare(
                $exchange,
                AMQPExchangeType::DIRECT,
                false,
                false,
                false
            );

            $rabbit->queue_declare(
                $queue,
                false,
                true,
                false,
                false,
                false,
                [
                    'x-message-ttl' => ['I',$this->config['rabbitmq']['ttl']],
                    'x-dead-letter-exchange' => ['S','deadletter-exchange'],
                    'x-dead-letter-routing-key' => ['S','deadletter-routing-key']
                 ]
            );

            $rabbit->queue_bind(
                $queue,
                $exchange,
                $routingKey
            );

            $msg = new AMQPMessage(
                $message,
                [
                    'content_type' => 'text/plain',
                    'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT
                ]
            );

            $rabbit->basic_publish($msg,$exchange,$routingKey);

            $end = $io->confirm('Publish another message?', true) ? false : true;
        }

        $rabbit->close();
        $amqp->close();

        $io->success('All Done!');

    }
}