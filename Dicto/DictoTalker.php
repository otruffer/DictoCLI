<?php

namespace Dicto;

use Dicto\DictoAPIException;

class DictoTalker {

    const POST = 1;
    const GET = 2;
    const DELETE = 3;

    /**
     * @var string
     */
    protected $server;

    /**
     * @var int
     */
    protected $port;

    /**
     * @var string
     */
    protected $defaultSuiteName;

    /**
     * @var string
     */
    protected $basicAuth;

    /**
     * @var bool
     */
    protected $https = false;

    /**
     * @param string $server
     * @param int $port
     * @param string $defaultSuiteName
     */
    public function __construct($server, $port = 8010, $defaultSuiteName = null) {
        $this->server = $server;
        $this->port = $port;
        $this->defaultSuiteName = $defaultSuiteName;
    }

    /**
     * @param string $method
     * @param string $request
     * @param string $suiteName
     * @param string $data
     * @param bool $jsonDecode
     * @return mixed
     * @throws DictoAPIException
     */
    protected function callApi($method, $request, $suiteName, $data = null, $jsonDecode = true, $extendedTimeout = false) {
        $url = "http".($this->https ? 's' : '')."://{$this->server}:{$this->port}/".($suiteName ? $suiteName.'/' : '')."{$request}";

        $curl = curl_init();

        switch ($method)
        {
            case self::POST:
                curl_setopt($curl, CURLOPT_POST, 1);

                if ($data) {
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                            'Content-Type: application/json',
                            'Content-Length: ' . strlen($data))
                    );
                }
                break;
            case self::DELETE:
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
                break;
        }

        // Optional Authentication:
        if($this->basicAuth) {
            curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($curl, CURLOPT_USERPWD, $this->basicAuth);
        }

        if($extendedTimeout !== false) {
            curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $extendedTimeout);
            curl_setopt($curl, CURLOPT_TIMEOUT, $extendedTimeout);
        }

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($curl);
        curl_close($curl);
        if($jsonDecode) {
            $result = json_decode($result, true);
        }
        if(is_array($result) && isset($result['error']))
            throw new DictoAPIException("API returned with an error: " . print_r($result['error'], true));
        if(is_array($result) &&isset($result['status-code']) && $result['status-code'] >= 400)
            throw new DictoAPIException($result['status-code'].": ".$result['status-message']);

        return $result;
    }

    /**
     * @return string[]
     */
    public function listSuites() {
       return $this->callApi(self::GET, 'suites', null);
    }

    /**
     * @param string $suiteName
     * @param string $projectFolder
     * @param string $projectSource
     * @param string $projectBin
     * @return mixed
     * @throws DictoAPIException
     */
    public function createSuite($suiteName, $projectFolder, $projectSource = "./", $projectBin = "./") {
        $data = array(
            '$DICTO.SUITEID' => $suiteName,
            '$DICTO.PROJECT-ROOT' => $projectFolder,
            '$DICTO.PROJECT-SRC' => $projectSource,
            '$DICTO.PROJECT-BIN' => $projectBin,
        );
        return $this->callApi(self::POST, 'suite', null, json_encode($data, JSON_UNESCAPED_SLASHES));
    }

    /**
     * @param $suiteName
     * @return mixed
     * @throws DictoAPIException
     */
    public function generateResults($suiteName) {
        return $this->callApi(self::POST, 'run', $this->getSuiteName($suiteName), null, true, 3600);
    }

    /**
     * @param $suiteName
     * @return mixed
     * @throws DictoAPIException
     */
    public function deleteSuite($suiteName) {
        return $this->callApi(self::DELETE, 'suite', $suiteName);
    }

    public function defineRules($rules, $suiteName = null) {
        $suiteName = $this->getSuiteName($suiteName);
        return $this->callApi(self::POST, 'rules', $suiteName, $this->encodeRules($rules));
    }

    /**
     * @param string $rules
     * @return string
     */
    protected function encodeRules($rules) {
        $rules = array(
            'rules' => $rules
        );
        return json_encode($rules);
    }

    /**
     * generate Results must have been successful before this outputs anything reliable.
     * @param $suiteName
     * @return RuleResult[]
     * @throws DictoAPIException
     */
    public function getResults($suiteName) {
        $jsonResults = $this->callApi(self::GET, 'results', $this->getSuiteName($suiteName), null, true, 3600);
        return $this->fillJsonResults(json_decode($jsonResults, true));
    }

    /**
     * @param $jsonResults
     * @return RuleResult[]
     */
    protected function fillJsonResults($jsonResults) {
        $results = array();
        foreach($jsonResults['results']['rules'] as $jsonRule) {
            $rule = new RuleResult();
            $rule->setFailed($jsonRule['failed'] == 'true');
            $rule->setRule($jsonRule['value']);
            $subrule = array_pop($jsonRule["subrules"]);
            $rule->setTestedBy($subrule['testedBy']);
            if(array_key_exists('errors', $subrule))
                $rule->setError($subrule['errors']);
            $results[] = $rule;
        }
        return $results;
    }

    /**
     * @param string $suiteName
     * @return mixed
     * @throws DictoAPIException
     */
    public function getRules($suiteName) {
        return $this->callApi(self::GET, 'rules', $this->getSuiteName($suiteName));
    }

    /**
     * @param string $suiteName
     * @return mixed
     * @throws DictoAPIException
     */
    public function textRules($suiteName) {
        return $this->callApi(self::GET, 'rulesText', $this->getSuiteName($suiteName), null, false);
    }

    /**
     * @return string
     */
    public function getServer()
    {
        return $this->server;
    }

    protected function getSuiteName($suiteName) {
        return $suiteName ? $suiteName : $this->defaultSuiteName;
    }

    /**
     * @param string $server
     */
    public function setServer($server)
    {
        $this->server = $server;
    }

    /**
     * @return int
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @param int $port
     */
    public function setPort($port)
    {
        $this->port = $port;
    }

    /**
     * @return string
     */
    public function getDefaultSuiteName()
    {
        return $this->defaultSuiteName;
    }

    /**
     * @param string $defaultSuiteName
     */
    public function setDefaultSuiteName($defaultSuiteName)
    {
        $this->defaultSuiteName = $defaultSuiteName;
    }

    /**
     * @return string
     */
    public function getBasicAuth()
    {
        return $this->basicAuth;
    }

    /**
     * @param string $basicAuth
     */
    public function setBasicAuth($basicAuth)
    {
        $this->basicAuth = $basicAuth;
    }

    /**
     * @return boolean
     */
    public function isHttps()
    {
        return $this->https;
    }

    /**
     * @param boolean $https
     */
    public function setHttps($https)
    {
        $this->https = $https;
    }

}