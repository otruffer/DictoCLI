<?php
namespace Dicto\Commands;

use Dicto\DictoHtmlOutput;
use Illuminate\Support\Traits\MacroableTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CompleteTestCommand extends DictoCommand {

    protected function configure()
    {
        parent::configure();
        $this->setName('full-test')
            ->setDescription('Make a complete test.')
            ->addArgument(
                'project-dir',
                InputArgument::REQUIRED,
                'Project directory.'
            )->addArgument(
                'dicto-rules',
                InputArgument::REQUIRED,
                'Path to the rules file.'
            )->addArgument(
                'results-folder',
                InputArgument::REQUIRED,
                'Folder where results are stored. Make sure it exists.'
            )->addOption(
                'compare',
                'c',
                InputOption::VALUE_REQUIRED,
                "Compare to a previous result."
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
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        $this->addArgument('suiteName');
        $input->setArgument('suiteName', $input->getOption('suiteName'));

        $this->addArgument('outputFile');
        $input->setArgument('outputFile', $input->getArgument('results-folder').'/index.html');

        $this->addOption('save');
        $input->setOption('save', $input->getArgument('results-folder').'/result.json');

        //CREATE SUITE
        $suiteName = $input->getArgument('suiteName');
        $projectFolder = $input->getArgument('project-dir');
        $projectSource = $input->getOption('projectSource') ? $input->getOption('projectSource') : './';
        $projectBinaries = $input->getOption('projectBinaries')? $input->getOption('projectSource') : './';
        $this->dicto->createSuite($suiteName, $projectFolder, $projectSource, $projectBinaries);
        $output->writeln("Suite $suiteName created.");

        //DEFINE RULES
        $file = $input->getArgument('dicto-rules');
        $rules = file_get_contents($file);
        $this->dicto->defineRules($rules);
        $output->writeln("Rules from file $file defined.");

        //GENERATE RESULST
        $output->writeln("Generating Results, this may take a while...");
        $this->dicto->generateResults($this->suiteName);

        //GENERATE JSON OUTPUT
        $output->writeln("Getting json results from server, this may take a while...");
        $rules = $this->dicto->getResults($this->suiteName);
        $outputFile = $input->getArgument('results-folder').'/result.json';
        file_put_contents($outputFile, json_encode($rules));
        $output->writeln("JSON Results saved to: $outputFile");

        //CREATE HTML OUTPUT
        $output->writeln("Creating HTML Output, this may take a while...");
        $file = $input->getArgument('results-folder').'/index.html';
        $html = new DictoHtmlOutput();
        if($compare = $input->getOption('compare')) {
            $html->setCompareFile($compare);
        }
        /** @noinspection PhpInternalEntityUsedInspection */
        $html->writeHtmlFile($rules, $file, true); //Ignore the warning. It's there because $rules will be altered.

        //DELETE SUITE
        $output->writeln("Deleting used suite.");
        $this->dicto->deleteSuite($this->suiteName);

        $output->writeln('finished.');
    }
}