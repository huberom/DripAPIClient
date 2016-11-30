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
    protected function __construct($apiKey = null, $apiURL = '')
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
                    'arguments' => '{"subscribers": [{"email": "<email>","custom_fields": {"first_name": "<name>"}}]}',
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
    protected function getAccountsList()
    {
        $response = $this->execute('accounts_list');
        return $response;
    }

    /**
    * Get a list of Workflows
    * @param array $params Array of additional parameters
    * @return object
    */
    protected function getWorkflowsList($params = [])
    {
        $response = $this->execute('workflows_list', $params);
        return $response;
    }

    /**
    * Get a list of Campaigns
    * @param array $params Array of additional parameters
    * @return object
    */
    protected function getCampaignsList($params = [])
    {
        $response = $this->execute('campaigns_list', $params);
        return $response;
    }

    /**
    * Add the specified subscriber
    * @param array $params Array of additional parameters
    * @return object
    */
    protected function addSubscriber($params = [])
    {
        $response = $this->execute('add_subscriber', $params);
        return $response;
    }

    /**
    * Remove the specified subscriber
    * @param array $params Array of additional parameters
    * @return object
    */
    protected function removeSubscriber($params = [])
    {
        $response = $this->execute('remove_subscriber', $params);
        return $response;
    }

    /**
    * Tag the specified subscriber
    * @param array $params Array of additional parameters
    * @return object
    */
    protected function addTag($params = [])
    {
        $response = $this->execute('tag_subscriber', $params);
        return $response;
    }

    /**
    * Removes a Tag from the specified subscriber
    * @param array $params Array of additional parameters
    * @return object
    */
    protected function removeTag($params = [])
    {
        $response = $this->execute('remove_tag', $params);
        return $response;
    }

    /**
    * Removes a Tag from the specified subscriber
    * @param array $params Array of additional parameters
    * @return object
    */
    protected function addToWorkflow($params = [])
    {
        $response = $this->execute('add_to_workflow', $params);
        return $response;
    }

    /**
    * Removes a subscriber from a workflow
    * @param array $params Array of additional parameters
    * @return object
    */
    protected function deleteFromWorkflow($params = [])
    {
        $response = $this->execute('remove_from_workflow', $params);
        return $response;
    }

    /**
    * Adds subscriber to a Campaign
    * @param array $params Array of additional parameters
    * @return object
    */
    protected function addToCampaign($params = [])
    {
        $response = $this->execute('add_to_campaign', $params);
        return $response;
    }

    /**
    * Record the specified event to a subscriber
    * @param array $params Array of additional parameters
    * @return object
    */
    protected function recordEvent($params = [])
    {
        $response = $this->execute('record_event', $params);
        return $response;
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
        curl_setopt($ch, CURLOPT_VERBOSE, true);

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