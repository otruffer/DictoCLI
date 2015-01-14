<?php
namespace Dicto\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Dicto\DictoTalker;

class DictoCommand extends Command {

    protected $server;
    protected $port;
    protected $suiteName;

    /**
     * @var DictoTalker
     */
    protected $dicto;

    protected function configure() {
        $this
            ->addOption(
                'suiteName',
                null,
                InputOption::VALUE_REQUIRED,
                'The name of the Test Suite. By default: DictoCommandLineSuite'
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
        $this->suiteName = $input->getOption('suiteName') ? $input->getOption('suiteName') : 'DictoCommandLineSuite';
        $this->port = $input->getOption('port') ? $input->getOption('port') : 8010;
        $this->server = $input->getOption('dictoServer') ? $input->getOption('dictoServer') : 'localhost';
        $this->dicto = new DictoTalker($this->server, $this->port, $this->suiteName);
    }
}