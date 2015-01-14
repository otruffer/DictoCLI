<?php
namespace Dicto\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class DeleteSuiteCommand extends DictoCommand {

    protected function configure()
    {
        parent::configure();
        $this->setName('deleteSuites')
            ->setDescription('Deletes a suite identified by the parameter --suiteName.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);
        $output->writeln($this->dicto->deleteSuite($this->suiteName));
    }
}