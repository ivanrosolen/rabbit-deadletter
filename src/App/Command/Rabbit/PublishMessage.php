<?php
namespace App\Command\Rabbit;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Style\SymfonyStyle;

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

        $io->success([
            '',
            sprintf('The exchange is: %s', $exchange),
            sprintf('The message is: %s', $message),
        ]);

    }
}