<?php
namespace Dicto\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ListRulesCommand extends DictoCommand {

    protected function configure()
    {
        parent::configure();
        $this->setName('listRules')
            ->setDescription('Lists all rules of a Dicto suite.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);
        $output->writeln($this->dicto->textRules($this->suiteName));
    }
}