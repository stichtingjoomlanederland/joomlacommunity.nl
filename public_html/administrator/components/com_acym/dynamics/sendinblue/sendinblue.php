<?php

use AcyMailing\Libraries\acymPlugin;

class SendinblueClass extends acymPlugin
{
    protected $headers;
    public $plugin;

    public function __construct(&$plugin, $headers = null)
    {
        parent::__construct();
        $this->plugin = &$plugin;
        $this->headers = $headers;
    }

    protected function callApiSendingMethod($url, $data = [], $headers = [], $type = 'GET', $authentication = [], $dataDecoded = false)
    {
        $response = parent::callApiSendingMethod(plgAcymSendinblue::SENDING_METHOD_API_URL.$url, $data, $headers, $type, $authentication, $dataDecoded);
        if (!empty($response['error_curl'])) {
            $this->plugin->errors[] = $response['error_curl'];
        } elseif (!empty($response['message']) && strpos($response['message'], 'Contact already in list') === false) {
            $backtrace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 2);
            if (!$backtrace[0]['file'] && !empty($backtrace[1]['function'])) $this->plugin->errors[] = $backtrace[0]['file'].': '.$backtrace[1]['function'];
            $this->plugin->errors[] = $response['message'];
        }

        return $response;
    }
}
