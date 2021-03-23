<?php

class SendinblueIntegration extends SendinblueClass
{
    public function getSettingsSendingMethodFromPlugin(&$data, $plugin, $method)
    {
        if ($method != plgAcymSendinblue::SENDING_METHOD_ID) return;

    }
}
