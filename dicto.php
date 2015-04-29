#!/usr/bin/env php
<?php
require_once __DIR__.'/vendor/autoload.php';
require_once __DIR__.'/Commands/DictoCommand.php';
require_once __DIR__.'/Commands/CreateSuiteCommand.php';
require_once __DIR__.'/Commands/CheckCommand.php';
require_once __DIR__.'/Dicto/DictoTalker.php';
require_once __DIR__.'/Dicto/Exceptions/DictoAPIException.php';
require_once __DIR__.'/Dicto/RuleResult.php';
require_once __DIR__.'/Dicto/Drawer/DictoHtmlOutput.php';
require_once __DIR__.'/Commands/ListSuitesCommand.php';
require_once __DIR__.'/Commands/DeleteSuiteCommand.php';
require_once __DIR__.'/Commands/DefineRulesCommand.php';
require_once __DIR__.'/Commands/ListRulesCommand.php';
require_once __DIR__.'/Commands/GenerateResultsCommand.php';
require_once __DIR__.'/Commands/ListResultsCommand.php';
require_once __DIR__.'/Commands/HTMLOutputCommand.php';
require_once __DIR__.'/Commands/CompleteCheckCommand.php';
require_once __DIR__.'/Commands/SendMailsToCommitersCommand.php';

use Dicto\Commands\HTMLOutputCommand;
use Symfony\Component\Console\Application;
use Dicto\Commands\ListSuitesCommand;
use Dicto\Commands\CreateSuiteCommand;
use Dicto\Commands\DeleteSuiteCommand;
use Dicto\Commands\DefineRulesCommand;
use Dicto\Commands\ListRulesCommand;
use Dicto\Commands\GenerateResultsCommand;
use Dicto\Commands\ListResultsCommand;
use Dicto\Commands\CompleteTestCommand;
use Dicto\Commands\SendMailsToCommitersCommand;

$application = new Application();
$application->add(new ListSuitesCommand());
$application->add(new CreateSuiteCommand());
$application->add(new DeleteSuiteCommand());
$application->add(new DefineRulesCommand());
$application->add(new ListRulesCommand());
$application->add(new GenerateResultsCommand());
$application->add(new ListResultsCommand());
$application->add(new HTMLOutputCommand());
$application->add(new CompleteTestCommand());
$application->add(new SendMailsToCommitersCommand());

$application->run();