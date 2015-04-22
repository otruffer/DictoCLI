<?php

namespace Dicto;

class DictoHtmlOutput {

    /**
     * @var \Philo\Blade\Blade
     */
    protected $blade;

    /** @var \Illuminate\View\Factory $factory */
    protected $view;

    /** @var  RuleResult[] */
    protected $compareRules;

    /**
     * @var string
     */
    protected $githubRepo;

    /**
     * @var string
     */
    protected $commit;

    /**
     * @var string
     */
    protected $compareCommit;


    public function __construct() {
        $this->blade = new \Philo\Blade\Blade(array(__DIR__."/Templates/"), __DIR__.'/Templates/cache');
        $this->view = $this->blade->view();
    }

    /**
     * @param RuleResult[] $rules
     * @param string $filePath
     * @internal param null $oldRules
     */
    public function writeHtmlFile(&$rules, $filePath, $withPublic = true) {

        if($this->compareRules)
            $oldIndex = $this->calculateViolationIndex($this->compareRules);
        $index = $this->calculateViolationIndex($rules);

        if($withPublic)
            $this->copy(dirname($filePath));

        //If there is a previous file
        if($this->compareRules) {
            //We first give each current rule their previous results.
            foreach($rules as $rule) {
                if(array_key_exists($rule->getRule(), $this->compareRules)) {
                    $rule->setPreviousResult($this->compareRules[$rule->getRule()]);
                    unset($this->compareRules[$rule->getRule()]);
                }
            }
        }

        $addedViolationIndex = $this->getAddedViolationIndex($rules);
        $resolvedViolationIndex = $this->getResolvedViolationIndex($rules);

        usort($rules, array('\\Dicto\\DictoHtmlOutput', 'sorter'));
        $data = array (
            'rules' => $rules,
            'violationIndex' => $index,
            'addedViolationIndex' => $addedViolationIndex,
            'resolvedViolationIndex' => $resolvedViolationIndex,
            'violationIndexDiff' => isset($oldIndex) ? $index - $oldIndex: $index,
        );

        if($this->getGithubRepo()) {
            $data['githubRepo'] = $this->getGithubRepo();
            $data['githubCommitURL'] = $this->getCommitURL();
            $data['githubCommit'] = $this->getCommit();
        }

        if($this->getCompareCommit()) {
            $data['githubCompare'] = $this->getCompareCommit();
            $data['githubCompareURL'] = $this->getCompareCommitURL();
        }

        echo "##teamcity[buildStatisticValue key='dicto.violationIndex' value='".$index."']\n";
        echo "##teamcity[buildStatisticValue key='dicto.addedVionaltions' value='".$addedViolationIndex."']\n";
        echo "##teamcity[buildStatisticValue key='dicto.removedViolations' value='".$resolvedViolationIndex."']\n";

        $template = $this->view->make('index', $data);
        file_put_contents($filePath, $template->render());
    }

    /**
     * @param $rules RuleResult[]
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
     * @param $rules RuleResult[]
     * @return int
     */
    protected function getResolvedViolationIndex($rules) {
        $index = 0;
        foreach($rules as $rule) {
            $index += count($rule->getResolvedViolations());
        }
        return $index;
    }

    public function setCompareFile($filePath) {
        $content = file_get_contents($filePath);
        $rules = array();
        foreach(json_decode($content, true) as $ruleAsArray) {
            $rule = new RuleResult();
            $rule->readFromArray($ruleAsArray);
            $rules[$rule->getRule()] = $rule;
        }
        $this->compareRules = $rules;
    }

    /**
     * @param $rules RuleResult[]
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

    /**
     * @param $a RuleResult
     * @param $b RuleResult
     * @return int
     */
    public static function sorter(RuleResult $a, RuleResult $b) {
        $a = count($a->getAddedViolations()) + count($a->getResolvedViolations());
        $b = count($b->getAddedViolations()) + count($b->getResolvedViolations());
        if($a == $b)
            return 0;
        return abs($a) > abs($b) ? -1 : 1;
    }

    protected function copy($dest) {
        $src = __DIR__."/Public";
        $dest = $dest."/Public";
        $this->rcopy($src, $dest);
    }

    /**
     * Recursively copy files from one directory to another
     *
     * @param String $src - Source of files being moved
     * @param String $dest - Destination of files being moved
     * @return bool
     */
    protected function rcopy($src, $dest){

        // If source is not a directory stop processing
        if(!is_dir($src)) return false;

        // If the destination directory does not exist create it
        if(!is_dir($dest)) {
            if(!mkdir($dest)) {
                // If the destination directory could not be created stop processing
                return false;
            }
        }

        // Open the source directory to read in files
        $i = new \DirectoryIterator($src);
        foreach($i as $f) {
            if($f->isFile()) {
                copy($f->getRealPath(), "$dest/" . $f->getFilename());
            } else if(!$f->isDot() && $f->isDir()) {
                $this->rcopy($f->getRealPath(), "$dest/$f");
            }
        }
    }

    /**
     * @return string
     */
    public function getCommit()
    {
        return $this->commit;
    }

    /**
     * @param string $commit
     */
    public function setCommit($commit)
    {
        $this->commit = $commit;
    }

    /**
     * @return string
     */
    public function getCompareCommit()
    {
        return $this->compareCommit;
    }

    /**
     * @param string $compareCommit
     */
    public function setCompareCommit($compareCommit)
    {
        $this->compareCommit = $compareCommit;
    }

    protected function hasCompareCommit() {
        isset($this->compareCommit);
    }

    /**
     * @return string
     */
    public function getGithubRepo()
    {
        return $this->githubRepo;
    }

    /**
     * @param string $githubRepo
     */
    public function setGithubRepo($githubRepo)
    {
        $this->githubRepo = $githubRepo;
    }

    /**
     * @return string
     */
    public function getCommitURL() {
        return $this->getGithubRepo()."/commit/".$this->getCommit();
    }

    /**
     * @return string
     */
    public function getCompareCommitURL() {
        return $this->getGithubRepo()."/compare/".$this->getCompareCommit()."...".$this->getCommit();
    }
}