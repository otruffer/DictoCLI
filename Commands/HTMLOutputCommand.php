<?php
namespace Dicto\Commands;

use Dicto\DictoHtmlOutput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class HTMLOutputCommand extends DictoCommand {

    protected function configure()
    {
        parent::configure();
        $this->setName('htmlOutput')
            ->setDescription('Lists all configured Dicto suites.')
            ->addArgument(
                'outputFile',
                InputArgument::REQUIRED,
                'The html file path.'
            )->addOption(
                'compareTo',
                'c',
                InputOption::VALUE_REQUIRED,
                'Compare the results to a previous result.'
            )->addOption(
                'noPublicFolder',
                null,
                InputOption::VALUE_NONE,
                'Should the public folder with css and js be left out?'
            )
        ;

    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);
        $file = $input->getArgument('outputFile');
        $output->writeln("Querying results from the server.");
        $rules = $this->dicto->getResults($this->suiteName);
        $output->writeln("Writing results into file.");
        $html = new DictoHtmlOutput();
        if($compare = $input->getOption('compareTo')) {
            $html->setCompareFile($compare);
        }
        $withPublic = $input->getOption('noPublicFolder') != true;
        /** @noinspection PhpInternalEntityUsedInspection */
        $html->writeHtmlFile($rules, $file, $withPublic); //Ignore the warning. It's there because $rules will be altered.
        $output->writeln("File has been written.");
    }
}