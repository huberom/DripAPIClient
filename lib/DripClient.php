<?php

namespace HOMC;

class DripClient
{
    /**
    * Drip API key
    * @var string
    */
    protected $apiKey = '';

    /**
    * Drip API URL
    * @var string
    * @access private
    */
    private $apiURL = 'https://api.getdrip.com/v2';

    /**
    * Drip API Endpoints
    * @var array
    * @access private
    */
    private $endPoints = [];

    /**
    * CURL SSL connection
    *
    * @var boolean
    */
    protected $curlSSL = true;

    /**
    * Construct
    * @param string $apiKey GetResponse API key
    * @param string $apiURL Base Drip Api Url
    * @return void
    */
    public function __construct($apiKey = null, $apiURL = '')
    {
        $this->apiKey = $apiKey;
        $this->loadEndPoints();
        if (!empty($apiURL)) {
            $this->apiURL = $apiURL;
        }
    }

    /**
    * Load API Endpoints
    * @param array $endPoints An array of endpoints configuration
    * @return void
    */
    public function loadEndPoints($endPoints = [])
    {
        if (empty($endPoints)) {
            $this->endPoints = [
                'accounts_list' => [
                    'method' => 'GET',
                    'path' => '/accounts',
                    'response' => 'accounts',
                ], 
                'workflows_list' => [
                    'method' => 'GET',
                    'path' => '/<account_id>/workflows',
                    'response' => 'workflows',
                ],
                'campaigns_list' => [
                    'method' => 'GET',
                    'path' => '/<account_id>/campaigns',
                    'response' => 'campaigns',
                ],
                'add_to_workflow' => [
                    'method' => 'POST',
                    'path' => '/<account_id>/workflows/<workflow_id>/subscribers',
                    'response' => 'subscribers',
                    'arguments' => '{
                      "subscribers": [{
                        "email": "<email>",
                        "custom_fields": {
                          "first_name": "<name>"
                        }
                      }]
                    }',
                ],
                'remove_from_workflow' => [
                    'method' => 'DELETE',
                    'path' => '/<account_id>/workflows/<workflow_id>/subscribers/<email>',
                    'response' => '',
                ],
                'add_subscriber' => [
                    'method' => 'POST',
                    'path' => '/<account_id>/subscribers',
                    'response' => 'links',
                    'arguments' => '{"subscribers": [{"email": "<email>","custom_fields": {"first_name": "<name>"}, "tags": ["<tag>"]}]}',
                ],
                'remove_subscriber' => [
                    'method' => 'DELETE',
                    'path' => '/<account_id>/subscribers/<email>',
                    'response' => 'name',
                ],
                'tag_subscriber' => [
                    'method' => 'POST',
                    'path' => '/<account_id>/tags',
                    'response' => 'name',
                    'arguments' => '{
                      "tags": [{
                        "email": "<email>",
                        "tag": "<tag>"
                      }]
                    }',
                ],
                'remove_tag' => [
                    'method' => 'DELETE',
                    'path' => '/<account_id>/subscribers/<email>/tags/<tag>',
                    'response' => '',
                ],
                'add_to_campaign' => [
                    'method' => 'POST',
                    'path' => '/<account_id>/campaigns/<campaign_id>/subscribers',
                    'response' => 'subscribers',
                    'arguments' => '{
                      "subscribers": [{
                        "email": "<email>",
                        "double_optin": false,
                        "reactivate_if_removed": true,
                        "custom_fields": {
                          "first_name": "<name>"
                        }
                      }]
                    }',
                ],
                'remove_from_campaign' => [
                    'method' => 'POST',
                    'path' => '/<account_id>/subscribers/<email>/unsubscribe?campaign_id=<campaign_id>',
                    'response' => 'subscribers',
                ],
                'record_event' => [
                    'method' => 'POST',
                    'path' => '/<account_id>/events',
                    'response' => '',
                    'arguments' => '{
                      "events": [{
                        "email": "<email>",
                        "action": "<event>",
                        "properties": {
                            "source": "demio",
                            "webinar_id": "<webinar_id>",
                            "webinar_name": "<webinar_name>"
                        },
                        "occurred_at": "<occurred_at>"
                      }]
                    }',
                ],
            ];
        } else {
            $this->endPoints = $endPoints;
        }
    }

    /**
    * Get a list of accounts
    * @return object
    */
    public function getAccountsList()
    {
        $response = $this->execute('accounts_list');
        return $response;
    }

    /**
    * Get a list of Workflows
    * @param array $params Array of additional parameters
    * @return object
    */
    public function getWorkflowsList($params = [])
    {
        $type = 'workflows_list';
        $response = $this->execute($type, $params);
        return $response;
    }

    /**
    * Get a list of Campaigns
    * @param array $params Array of additional parameters
    * @return object
    */
    public function getCampaignsList($params = [])
    {
        $type = 'campaigns_list';
        $response = $this->execute($type, $params);
        return $response;
    }

    /**
    * Add the specified subscriber
    * @param array $params Array of additional parameters
    * @return object
    */
    public function addSubscriber($params = [])
    {
        $type = 'add_subscriber';
        $response = $this->execute($type, $params);
        return $response;
    }

    /**
    * Remove the specified subscriber
    * @param array $params Array of additional parameters
    * @return object
    */
    public function removeSubscriber($params = [])
    {
        $type = 'remove_subscriber';
        $response = $this->execute($type, $params);
        return $response;
    }

    /**
    * Tag the specified subscriber
    * @param array $params Array of additional parameters
    * @return object
    */
    public function addTag($params = [])
    {
        $type = 'tag_subscriber';
        $response = $this->execute($type, $params);
        return $response;
    }

    /**
    * Removes a Tag from the specified subscriber
    * @param array $params Array of additional parameters
    * @return object
    */
    public function removeTag($params = [])
    {
        $type = 'remove_tag';

        if (!empty($params['tag'])) {
            $string = urldecode($params['tag']);
            $params['tag'] = urlencode($string);
        }

        $response = $this->execute($type, $params);
        return $response;
    }

    /**
    * Removes a Tag from the specified subscriber
    * @param array $params Array of additional parameters
    * @return object
    */
    public function addToWorkflow($params = [])
    {
        $type = 'add_to_workflow';
        $response = $this->execute($type, $params);
        return $response;
    }

    /**
    * Removes a subscriber from a workflow
    * @param array $params Array of additional parameters
    * @return object
    */
    public function deleteFromWorkflow($params = [])
    {
        $type = 'remove_from_workflow';
        $response = $this->execute($type, $params);
        return $response;
    }

    /**
    * Adds subscriber to a Campaign
    * @param array $params Array of additional parameters
    * @return object
    */
    public function addToCampaign($params = [])
    {
        $type = 'add_to_campaign';
        $response = $this->execute($type, $params);
        return $response;
    }

    /**
    * Record the specified event to a subscriber
    * @param array $params Array of additional parameters
    * @return object
    */
    public function recordEvent($params = [])
    {
        $type = 'record_event';
        $response = $this->execute($type, $params);
        return $response;
    }

    /**
    * Validates if params arraya has required params for specific request api type
    * @param string $type   API request type identifier
    * @param array  $params Array of additional parameters
    * @return boolean
    */
    public function validParams($type, $params)
    {
        if (empty($this->endPoints[$type])) {
            return false;
        }

        // path
        $string = $this->endPoints[$type]['path'];
        preg_match_all("!\<(\w+)\>!", $string, $matches);

        foreach ($matches[1] as $key => $value) {
            if (empty($params[$value])) {
                return false;
            }
        }

        // arguments
        $matches = [0 => [], 1 => []];
        if (!empty($this->endPoints[$type]['arguments'])) {
            $string = $this->endPoints[$type]['arguments'];
            preg_match_all("!\<(\w+)\>!", $string, $matches);
            foreach ($matches[1] as $key => $value) {

                if ($type == 'add_subscriber' AND $value == 'tag') {
                    continue;
                }

                if (empty($params[$value])) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
    * Creates a full endpoint url for the ConvertKit API
    * @param string $type The type of request to do for the ConvertKit API
    * @param array $request An array of additional request params to append
    * @return string $url
    * @access private
    */
    private function createEndPointUrl($type, $params=[])
    {
        $url = $this->apiURL;
        $options = $this->endPoints[$type];
        
        $path = $options['path'];
        $binds = [];
        foreach ($params as $key => $value) {
            if (strpos($path, $key) !== false) {
                $binds[$key] = $value;
            }
        }
        
        if (in_array($options['method'], ['GET','POST','PUT','DELETE'])) {
            $rs = $this->parse($path, $binds);
            $path = $rs;
        }

        $url.=$path;

        return $url;
    }

    private function parseArguments($type, $params=[])
    {
        $string = '';

        if (!empty($this->endPoints[$type]['arguments'])) {
            $binds = [];
            foreach ($params as $key => $value) {
                if (strpos($this->endPoints[$type]['arguments'], $key) !== false) {
                    $binds[$key] = $value;
                }
            }
            $string = trim($this->parse($this->endPoints[$type]['arguments'], $binds));
        }

        return $string;
    }

    /**
    * Executes an API call
    * @param string $type The type of request to do for the ConvertKit API
    * @param array $request An array of additional request params to append
    * @return bool/array
    * @access private
    */
    private function execute($type, $params=[])
    {
        if (empty($this->endPoints[$type])) {
            return false;
        }

        $validParams = $this->validParams($type, $params);
        if (!$validParams) {
            return false;
        }

        if (!empty($params['name'])) {
            $params['name'] = $this->getFirstName($params['name']);
        }

        // Set CURL SSL false
        $this->curlSSL = false;
        $apiUrl    = $this->createEndPointUrl($type, $params);
        $arguments = $this->parseArguments($type, $params);

        // HTTP Basic Auth
        $headers = [
            "Content-Type" => "application/json",
            "Authorization" => "Basic ".base64_encode($this->apiKey),
        ];

        $response = $this->sendRequest($apiUrl, $arguments, $headers, $this->endPoints[$type]['method']);
        if (!empty($response[$this->endPoints[$type]['response']])) {
            return $response[$this->endPoints[$type]['response']];
        } else {
            if ($response === false) {
                return false;
            } else {
                return $response;
            }
        }
    }

    public function parse($subject, $variables, $escapeChar = '@', $errPlaceholder = null)
    {
        $esc = preg_quote($escapeChar);
        $expr = "/
            $esc$esc(?=$esc*+<)
          | $esc<
          | <(\w+)>
        /x";

        $callback = function($match) use($variables, $escapeChar, $errPlaceholder) {
            switch ($match[0]) {
                case $escapeChar . $escapeChar:
                    return $escapeChar;

                case $escapeChar . '<':
                    return '<';

                default:
                    if (isset($variables[$match[1]])) {
                        return $variables[$match[1]];
                    }

                    return isset($errPlaceholder) ? $errPlaceholder : $match[0];
            }
        };

        return preg_replace_callback($expr, $callback, $subject);
    }

    public function getFirstName($name)
    {
        $nameArray = explode(' ', $name);
        $firstName = (isset($nameArray[0])) ? $nameArray[0] : $name;

        return $firstName;
    }

    /**
     * Send request to server
     *
     * @param string       $apiEndpoint
     * @param array|string $args
     * @param array        $headers
     * @param string       $method
     * @param array        $otherOptions
     * @return bool|mixed
     */
    public function sendRequest($apiEndpoint, $args, $headers=[], $method='POST', $otherOptions=[]) {

        $method = strtoupper($method);

        $processedHeaders = array();
        if(!empty($headers) && is_array($headers) && count($headers) > 0) {
            foreach($headers as $key => $value) {
                $processedHeaders[] = $key . ': ' . $value;
            }
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiEndpoint);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $processedHeaders);

        if ($this->curlSSL === true) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        } else {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        }

        if (is_array($args) && count($args) > 0) {
            $query = http_build_query($args, '', '&');
        } else {
            $query = $args; //Should be json data format
        }

        switch ($method) {
            case 'POST':
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
                curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
                break;
            case 'GET':
                curl_setopt($ch, CURLOPT_URL, $apiEndpoint . '?' . $query);
                break;
            case 'PUT':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
                curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
                break;
            case 'DELETE':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
                curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
                break;
            case 'PATCH':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
                curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
                break;
        }

        if (is_array($otherOptions) && count($otherOptions) > 0) {
            foreach ($otherOptions as $optionKey => $optionVal) {
                curl_setopt($ch, $optionKey, $optionVal);
            }
        }

        $response     = curl_exec($ch);
        $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (false === $response) {
            $errNo  = curl_errno($ch);
            $errStr = curl_error($ch);
            curl_close($ch);

            if (empty($errStr)) {
                // $this->logErrors(sprintf("There was a problem requesting the resource. (Error Code:%s)", $responseCode));
            } else {
                // $this->logErrors(sprintf("%s \n cURL Error: (%s), %s", $errStr,  $errNo, $responseCode));
            }

            return false;
        } else {

            curl_close($ch);
            return json_decode($response, true);
        }

    }
}