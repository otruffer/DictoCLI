<?php
namespace Dicto\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ListResultsCommand extends DictoCommand {

    protected function configure()
    {
        parent::configure();
        $this->setName('listResults')
            ->setDescription('Lists the results of the rules. Be sure to generate the results first.')
            ->addOption(
                'listViolations',
                'l',
                InputOption::VALUE_NONE,
                'If set the violations are listed.'
            )->addOption(
                'save',
                's',
                InputOption::VALUE_REQUIRED,
                'Save results as json into a file.'
            )
        ;

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);
        $rules = $this->dicto->getResults($this->suiteName);
        foreach($rules as $rule) {
            $output->writeln($rule->getRule(). " has " . ($rule->isFailed() ? 'failed' : 'passed'));
            if($rule->isFailed()) {
                $output->writeln("There were " . count($rule->getErrors()) ." violations.");
                if($input->getOption('listViolations')) {
                    foreach($rule->getErrors() as $error) {
                        $output->writeln(print_r($error, true));
                    }
                }
            }
        }
        if($saveTo = $input->getOption('save')) {
            file_put_contents($saveTo, json_encode($rules));
            $output->writeln("Results saved in: $saveTo");
        }
    }
}