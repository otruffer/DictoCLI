<?php
namespace Dicto\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SendMailsToCommitersCommand extends Command{

    protected function configure()
    {
        $this->setName('send-mails')
            ->addArgument(
                'commit',
                InputArgument::REQUIRED,
                "Current Github commit id."
            )
            ->addArgument(
                'compareCommit',
                InputArgument::REQUIRED,
                "Compared github commit id."
            )
            ->addOption(
                'dicto-addedViolations',
                null,
                InputArgument::OPTIONAL,
                'how many dicto violations were added.'
            )
            ->addOption(
                'dicto-removedViolations',
                null,
                InputArgument::OPTIONAL,
                'how many dicto violations were removed.'
            )
            ->addOption(
                'dicto-totalViolations',
                null,
                InputArgument::OPTIONAL,
                'total dicto violations.'
            )
            ->addOption(
                'homepageURL',
                null,
                InputArgument::OPTIONAL,
                'homepage url.'
            )

            ->setDescription('Sends results page Mail to all commiters between two commits');
    }

    //./dicto.php send-mails 630e550d87db272463af7435e113f9eea7e51732 30e05ac617b078869930bcfe5cdbeec148d91f50 --dicto-totalViolations=1 --dicto-removedViolations=1 --dicto-addedViolations=1 --homepageURL="https://ci.studer-raimann.ch"
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        //git log --pretty=oneline --format='%ae' 630e550d87db272463af7435e113f9eea7e51732...630e550d87db272463af7435e113f9eea7e51732
        $c1 = $input->getArgument('commit');
        $c2 = $input->getArgument('compareCommit');

        $command = "git log --pretty=oneline --format='%an <%ae>' $c1...$c2";
        exec($command, $emails);
        $emails = array_unique($emails);

        $total = $input->getOption('dicto-totalViolations');
        $added = $input->getOption('dicto-addedViolations');
        $removed = $input->getOption('dicto-removedViolations');
        $url = $input->getOption('homepageURL');

        $message = "
        Dear Committer

        The current build on our TeamCity-Server found your E-Mail address among the committers.

        <a href=\"$url\">$url</a>

        Dicto
        Added Violations: $added
        Resolved Violations: $removed
        Total Violations: $total

        The comparison was made between git commits $c1 and $c2.

        Cheers & Happy Programming
        TeamCity
        ";

//        $output->writeln($message);

        $output->writeln(var_export($emails, true));
//        mail(implode(', ', $emails), "TeamCity Build", $message);
        mail("Oskar Truffer <ot@studer-raimann.ch>", "TeamCity Build", $message);
    }
}