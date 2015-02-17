<?php
namespace Dicto\Commands;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DefineRulesCommand extends DictoCommand {

    protected function configure()
    {
        parent::configure();
        $this->setName('defineRules')
            ->setDescription('Define rules for a test suite.')
            ->addArgument(
                'dicto-rules',
                InputArgument::REQUIRED,
                'The file that contains the rules.'
            );

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);
        $file = $input->getArgument('dicto-rules');
        $rules = file_get_contents($file);
        $output->writeln($this->dicto->defineRules($rules));
    }
}