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

class Setup extends Command
{

    protected $config;

    public function __construct(Array $config)
    {
        parent::__construct();

        $this->config = $config;
    }

    protected function configure()
    {
        $this->setName('rabbit:setup')
             ->setDescription('Setup RabbitMQ')
             ->setHelp('This command setup rabbitmq with exchange and queue for deadletter');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        if (!$this->config['rabbitmq']['host'] ||
            !$this->config['rabbitmq']['port'] ||
            !$this->config['rabbitmq']['user'] ||
            !$this->config['rabbitmq']['pwd'] ||
            !$this->config['rabbitmq']['vhost'] ||
            !$this->config['rabbitmq']['deadletter-exchange'] ||
            !$this->config['rabbitmq']['deadletter-queue'] ||
            !$this->config['rabbitmq']['deadletter-routing-key']
        ) {
            throw new \RuntimeException('Invalid RabbitMQ Config');
        }

        $amqp = new AMQPConnection(
            $this->config['rabbitmq']['host'],
            $this->config['rabbitmq']['port'],
            $this->config['rabbitmq']['user'],
            $this->config['rabbitmq']['pwd'],
            $this->config['rabbitmq']['vhost']
        );
        $rabbit = $amqp->channel();

        $io = new SymfonyStyle($input, $output);

        $rabbit->exchange_declare(
            $this->config['rabbitmq']['deadletter-exchange'],
            AMQPExchangeType::DIRECT,
            false,
            false,
            false
        );

        $rabbit->queue_declare(
            $this->config['rabbitmq']['deadletter-queue'],
            false,
            true,
            false,
            false
        );

        $rabbit->queue_bind(
            $this->config['rabbitmq']['deadletter-queue'],
            $this->config['rabbitmq']['deadletter-exchange'],
            $this->config['rabbitmq']['deadletter-routing-key']
        );


        $rabbit->close();
        $amqp->close();

        $io->success('All Done!');

    }
}