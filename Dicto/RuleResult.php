<?php

namespace Dicto;

class RuleResult {

    /** @var  bool */
    public $failed;

    /** @var  string */
    public $rule;

    /** @var  string */
    public $testedBy;

    /** @var  string[][] array with of array. second array with keys "cause", "fix", "details" */
    public $errors;

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
     * @return \string[][] array with of array. second array with keys "cause", "fix", "details"
     */
    public function getErrors() {
        return $this->errors;
    }

    /**
     * @param $result RuleResult
     */
    public function setPreviousResult($result) {
        $this->previousResult = $result;
    }

    public function hasPreviousResult() {
        return isset($this->previousResult);
    }

    /**
     * @return array returns a string list of all violations that are present in this ruleResult which were not present in the previous ruleResult.
     */
    public function getAddedViolations() {
        if(!$this->previousResult)
            return $this->errors;
        return $this->error_diff($this->errors, $this->previousResult->getErrors());
    }

    /**
     * @return array returns a string list of all violations that are in the previous ruleResult but are no longer in this ruleResult.
     */
    public function getResolvedViolations() {
        if(!$this->previousResult)
            return array();
        return $this->error_diff($this->previousResult->getErrors(), $this->getErrors());
    }

    /**
     * @return int How much the index changes due to this rule: addedViolations - resolvedViolations.
     */
    public function getViolationDiff() {
        return count($this->getAddedViolations()) - count($this->getResolvedViolations());
    }

    /**
     * @param $errors
     */
    public function setError($errors) {
        //TODO remove.
        $this->errors = $errors;
    }

    /**
     * @param $array Reads from array.
     */
    public function readFromArray($array)
    {
        foreach ($array as $key => $value) {
            $this->$key = $value;
        }
    }

    /**
     * @param $array1 array
     * @param $array2 array
     * @return array
     */
    protected function error_diff($array1, $array2)
    {
        new DictoTalker("hello");
        if($array1 == null)
            return array();
        if($array2 == null)
            return $array1;
        $newArray = array();
        foreach($array1 as $arr) {
            $add = true;
            foreach($array2 as $arr2) {
                if($arr['cause'] == $arr2['cause'])
                    $add = false;
            }
            if($add)
                $newArray[] = $arr;
        }
        return $newArray;
    }
}