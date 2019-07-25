<?php
namespace App\Command\Rabbit;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Style\SymfonyStyle;
use PhpAmqpLib\Connection\AMQPConnection;
use PhpAmqpLib\Message\AMQPMessage;

class PublishMessage extends Command
{

    protected function configure()
    {
        $this->setName('rabbit:message:publish')
             ->setDescription('Publish messages')
             ->setHelp('This command publish messages to a rabbitmq exchange');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $io = new SymfonyStyle($input, $output);

        $exchange = $io->ask(
            'Please enter the name of the exchange',
            'DefaultExchange');

        $queue = $io->ask(
            'Please enter the name of the queue',
            'DefaultQueue');

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

        // @todo: get rabbit infos  from conf
        $amqp = new AMQPConnection('rabbitmq-server', 5672, 'guest', 'guest');
        $rabbit = $amqp->channel();

        // @todo: Exchange check
        $rabbit->exchange_declare($exchange,'direct',false,false,false);

        // @todo: Queue Check
        $rabbit->queue_declare($queue, false, true, false, false);

        // @todo: Bind Check
        $rabbit->queue_bind($queue,$exchange);

        // @todo: Send Message
        $msg = new AMQPMessage(
            $message,
            array('delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT)
        );

        $rabbit->basic_publish($msg,$exchange);

        $rabbit->close();
        $amqp->close();

        $io->success([
            '',
            sprintf('The exchange is: %s', $exchange),
            sprintf('The queue is: %s', $queue),
            sprintf('The message is: %s', $message),
        ]);

    }
}