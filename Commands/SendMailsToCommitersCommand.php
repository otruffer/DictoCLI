<?php
namespace Dicto\Commands;

use Dicto\RuleResult;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SendMailsToCommitersCommand extends DictoCommand{

    protected function configure()
    {
        parent::configure();

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
                'homepageURL',
                null,
                InputArgument::OPTIONAL,
                'homepage url.'
            )
            ->addOption(
                'compareFile',
                null,
                InputArgument::OPTIONAL,
                'compareFile.'
            )

            ->setDescription('Sends results page Mail to all commiters between two commits');
    }

    //./dicto.php send-mails 630e550d87db272463af7435e113f9eea7e51732 30e05ac617b078869930bcfe5cdbeec148d91f50 --dicto-totalViolations=1 --dicto-removedViolations=1 --dicto-addedViolations=1 --homepageURL="https://ci.studer-raimann.ch"
    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        //git log --pretty=oneline --format='%ae' 630e550d87db272463af7435e113f9eea7e51732...630e550d87db272463af7435e113f9eea7e51732
        $c1 = $input->getArgument('commit');
        $c2 = $input->getArgument('compareCommit');

        $command = "git log --pretty=oneline --format='%an <%ae>' $c1...$c2";
        exec($command, $emails);
        $emails = array_unique($emails);

        $results = $this->dicto->getResults($input->getOption("suiteName"));
        $compareRules = $this->getCompareFile($input->getOption("compareFile"));

        if($compareRules) {
            //We first give each current rule their previous results.
            foreach($results as $rule) {
                if(array_key_exists($rule->getRule(), $compareRules)) {
                    $rule->setPreviousResult($compareRules[$rule->getRule()]);
                    unset($compareRules[$rule->getRule()]);
                }
            }
        }

        $total = $this->calculateViolationIndex($results);
        $added = $this->getAddedViolationIndex($results);
        $removed = $this->getResolvedViolationIndex($results);
        $url = $input->getOption('homepageURL');

        $message = "
        <html>
        <title>TeamCity ILIAS Build</title>
            <body>
                Dear Committer

                The current build on our TeamCity-Server found your E-Mail address among the committers.

                <a href='$url'>$url</a>

                Dicto
                Added Violations: $added
                Resolved Violations: $removed
                Total Violations: $total

                The comparison was made between git commits $c1 and $c2.

                Cheers & Happy Programming
                TeamCity
            </body>
        </html>
        ";

        $output->writeln($message);

        $output->writeln(var_export($emails, true));
//        mail(implode(', ', $emails), "TeamCity Build", $message);
//        mail("Oskar Truffer <ot@studer-raimann.ch>", "TeamCity Build", $message);
        
        $header  = "MIME-Version: 1.0\r\n";
        $header .= "Content-type: text/html; charset=iso-8859-1\r\n";
        $header .= "X-Mailer: PHP ". phpversion();

        mail("Oskar Truffer <ot@studer-raimann.ch>", "TeamCity Build", $message, $header);
    }

    /**
     * @param $rules \Dicto\RuleResult[]
     * @return int
     */
    protected function getAddedViolationIndex($rules) {
        $index = 0;
        foreach($rules as $rule) {
            $index += count($rule->getAddedViolations());
        }
        return $index;
    }

    /**
     * @param $rules \Dicto\RuleResult[]
     * @return int
     */
    protected function getResolvedViolationIndex($rules) {
        $index = 0;
        foreach($rules as $rule) {
            $index += count($rule->getResolvedViolations());
        }
        return $index;
    }

    /**
     * @param $rules \Dicto\RuleResult[]
     * @return int
     */
    protected function calculateViolationIndex($rules)
    {
        $index = 0;
        foreach ($rules as $rule) {
            $index += count($rule->getErrors());
        }
        return $index;
    }

    public function getCompareFile($filePath) {
        $content = file_get_contents($filePath);
        $rules = array();
        foreach(json_decode($content, true) as $ruleAsArray) {
            $rule = new RuleResult();
            $rule->readFromArray($ruleAsArray);
            $rules[$rule->getRule()] = $rule;
        }
        return $rules;
    }
}