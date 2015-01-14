<?php
namespace Dicto\Commands;

use Dicto\DictoAPIException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CreateSuiteCommand extends DictoCommand {

    protected function configure()
    {
        parent::configure();
        $this->setName('createSuite')
            ->setDescription('Create a new Dicto suite.')
            ->addArgument(
                'suiteName',
                InputArgument::REQUIRED,
                "The suite's name and id"
            )
            ->addArgument(
                'projectFolder',
                InputArgument::REQUIRED,
                "The absolute path to the project directiory."
            )
            ->addOption(
                'projectSource',
                null,
                InputOption::VALUE_REQUIRED,
                "The relative path to the source."
            )
            ->addOption(
                'projectBinaries',
                null,
                InputOption::VALUE_REQUIRED,
                "The relative path to the binaries."
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        $suiteName = $input->getArgument('suiteName');
        $projectFolder = $input->getArgument('projectFolder');
        $projectSource = $input->getOption('projectSource') ? $input->getOption('projectSource') : './';
        $projectBinaries = $input->getOption('projectBinaries')? $input->getOption('projectSource') : './';

        try{
            $feedback = $this->dicto->createSuite($suiteName, $projectFolder, $projectSource, $projectBinaries);
        } catch (DictoAPIException $e) {
            $output->writeln("There was a problem in the API: ".$e->getMessage());
            exit(1);
        }
        $output->writeln($feedback);
    }
}