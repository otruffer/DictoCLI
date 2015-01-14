<?php
namespace Dicto\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CheckCommand extends Command {
    protected function configure() {
        $this->setName('check')
            ->setDescription('Runs some dicto commands.')
            ->addArgument(
                'dictoRules',
                InputArgument::REQUIRED,
                'The file with the dicto rules.'
            )
            ->addArgument(
              'projectFolder',
               InputArgument::REQUIRED,
               'The project folder with the source in it.'
            )
            ->addOption(
                'suiteName',
                null,
                InputOption::VALUE_REQUIRED,
                'The name of the Test Suite.'
            )
            ->addOption(
                'port',
                'p',
                InputOption::VALUE_REQUIRED,
                'Port of the Dicto Server.'
            )
            ->addOption(
                'dictoServer',
                null,
                InputOption::VALUE_REQUIRED,
                'The dicto servers address. localhost by default.'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dictoRulesFile = $input->getArgument('dictoRules');
        $projectFolder = $input->getArgument('projectFolder');

        $suiteName = $input->getOption('suiteName') ? $input->getOption('suiteName') : 'DictoCommandLineSuite';
        $port = $input->getOption('port') ? $input->getOption('port') : 8010;
        $dictoServer = $input->getOption('dictoServer') ? $input->getOption('dictoServer') : 'localhost';

        $output->writeln($dictoRulesFile);
        $output->writeln($projectFolder);
        $output->writeln($suiteName);
        $output->writeln($port);
        $output->writeln($dictoServer);
    }
}