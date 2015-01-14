<?php

namespace Dicto;

class RuleResult {

    /** @var  bool */
    public $failed;

    /** @var  string */
    public $rule;

    /** @var  string */
    public $testedBy;

    /** @var  string[] */
    public $violations;

    /** @var  RuleResult */
    protected $previousResult;

    /**
     * @return boolean
     */
    public function isFailed()
    {
        return $this->failed;
    }

    /**
     * @param boolean $failed
     */
    public function setFailed($failed)
    {
        $this->failed = $failed;
    }

    /**
     * @return string
     */
    public function getRule()
    {
        return $this->rule;
    }

    /**
     * @param string $rule
     */
    public function setRule($rule)
    {
        $this->rule = $rule;
    }

    /**
     * @return string
     */
    public function getTestedBy()
    {
        return $this->testedBy;
    }

    /**
     * @param string $testedBy
     */
    public function setTestedBy($testedBy)
    {
        $this->testedBy = $testedBy;
    }

    /**
     * @return \string[]
     */
    public function getViolations()
    {
        return $this->violations;
    }

    /**
     * @param \string[] $violations
     */
    public function setViolations($violations)
    {
        $this->violations = $violations;
    }

    /**
     * @param $result RuleResult
     */
    public function setPreviousResult($result) {
        $this->previousResult = $result;
    }

    /**
     * @return array returns a string list of all violations that are present in this ruleResult which were not present in the previous ruleResult.
     */
    public function getAddedViolations() {
        if(!$this->previousResult)
            return $this->violations;
        return array_diff($this->violations, $this->previousResult->getViolations());
    }

    /**
     * @return array returns a string list of all violations that are in the previous ruleResult but are no longer in this ruleResult.
     */
    public function getResolvedViolations() {
        if(!$this->previousResult)
            return array();
        return array_diff($this->previousResult->getViolations(), $this->getViolations());
    }

    /**
     * @return int How much the index changes due to this rule: addedViolations - resolvedViolations.
     */
    public function getViolationDiff() {
        return count($this->getAddedViolations()) - count($this->getResolvedViolations());
    }

    /**
     * @param $dictoErrorMessage
     */
    public function parseViolations($dictoErrorMessage) {
        if(!$dictoErrorMessage)
            $this->setViolations(array());
        $dictoErrorMessage = substr($dictoErrorMessage, 18);
        $violations = explode('; ', $dictoErrorMessage);
        $this->setViolations(array_filter($violations));
    }

    /**
     * @param $array Reads from array.
     */
    public function readFromArray($array) {
        foreach($array as $key => $value) {
            $this->$key = $value;
        }
    }
}