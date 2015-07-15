<?php
namespace Dicto\Commands;

use Dicto\RuleResult;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
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
                InputOption::VALUE_REQUIRED,
                'how many dicto violations were added.'
            )
            ->addOption(
                'homepageURL',
                null,
                InputOption::VALUE_REQUIRED,
                'homepage url.'
            )
            ->addOption(
                'compareFile',
                null,
                InputOption::VALUE_REQUIRED,
                'compareFile.'
            )->addOption(
                'saveToSqlite',
                null,
                InputOption::VALUE_REQUIRED,
                'save stats to sqlite database with the path given.'
            )->addOption(
                'emailSubject',
                null,
                InputOption::VALUE_REQUIRED,
                'The subject of the email sent out.'
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
        $emailSubject = $input->getOption('emailSubject') ? $input->getOption('emailSubject') : '[ILIAS-CI] Your contribution';

        $command = "git log --pretty=oneline --format='%ae' $c1...$c2";
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
                <h1>Dear ILIAS Contributor</h1>

                <p>The current build on our TeamCity-Server found your E-Mail address among the contributors. Have a look at the complete build:</p>

                <p><a href='$url'>$url</a></p>
                <p>
                Dicto <br/>
                Added Violations: $added <br/>
                Resolved Violations: $removed <br/>
                Total Violations: $total <br/>
                </p>
                <p>
                    The comparison was made between git commits $c1 and $c2.
                </p>
                <p>
                    If you have any feedback or suggestion for Dicto rules or the CI in general feel free to send an email to ot at studer-raimann dot ch.
                </p>
                <p>
                Cheers & Happy Programming <br/>
                TeamCity
                </p>
            </body>
        </html>
        ";

        $header  = "MIME-Version: 1.0\r\n";
        $header .= "Content-type: text/html; charset=iso-8859-1\r\n";
        $header .= "X-Mailer: PHP ". phpversion();
        if($added != 0 || $removed != 0) {
            mail(implode(', ', $emails), $emailSubject, $message, $header);
            $output->writeln('Sent Email:');
            $output->writeln($message);
            $output->writeln(var_export($emails, true));
        } else {
            $output->writeln('No changes: Mails not sent.');
        }

        if($input->getOption('saveToSqlite')){
            try {
                $this->saveToSqlite($input->getOption('saveToSqlite'), $emails, $removed, $added);
            } catch(\Exception $e) {
                $output->writeln($e->getMessage().": ".$e->getTraceAsString());
            }
        }
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
     * @param $sqlitePath string
     * @param $emails string[]
     * @param $points int
     * @throws \Exception
     */
    protected function saveToSqlite($sqlitePath, $emails, $points, $negative) {
        $db = new \SQLite3($sqlitePath);
        if(!$db) {
            throw new \Exception("sqlite Path given that could not be opened.");
        }
        foreach($emails as $email) {
            $this->addPointsForEmail($db, $email, $points);
            $this->removePointsForEmail($db, $email, $negative);
        }
    }

    /**
     * @param $db \SQLite3
     * @param $email string
     * @param $points int
     */
    protected function addPointsForEmail($db, $email, $points) {
        $points = (int) $points;
        $res = $db->query("SELECT * FROM stats WHERE email LIKE '$email'");
        if($row = $res->fetchArray()) {
            $points = (int) ($row['points'] + $points);
            $db->exec("UPDATE stats SET points = $points WHERE email LIKE '$email'");
        } else {
            $db->exec("INSERT INTO stats VALUES('$email', $points)");
        }
    }

    /**
     * @param $db \SQLite3
     * @param $email string
     * @param $points int
     */
    protected function removePointsForEmail($db, $email, $points) {
        $points = (int) $points;
        $res = $db->query("SELECT * FROM minus_stats WHERE email LIKE '$email'");
        if($row = $res->fetchArray()) {
            $points = (int) ($row['points'] + $points);
            $db->exec("UPDATE stats SET minus_points = $points WHERE email LIKE '$email'");
        } else {
            $db->exec("INSERT INTO minus_stats VALUES('$email', $points)");
        }
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