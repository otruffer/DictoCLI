<?php
namespace Dicto\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateResultsCommand extends DictoCommand {

    protected function configure()
    {
        parent::configure();
        $this->setName('generateResults')
            ->setDescription('Generates the Results of some Dicto rules in a test suite. This may take quite some time.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);
        $summary = $this->dicto->generateResults($this->suiteName);
        $total = $summary['summary']['rules']['total'];
        $failed = $summary['summary']['rules']['failed'];
        $output->writeln("Results generated. Total Rules: $total Rules Failed: $failed");
    }
}