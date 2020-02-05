<?php
defined('_JEXEC') or die('Restricted access');
?><?php

class FrontcampaignsViewFrontcampaigns extends acymView
{
    public function __construct()
    {
        global $Itemid;
        $this->Itemid = $Itemid;

        $this->steps = [
            'chooseTemplate' => 'ACYM_CHOOSE_TEMPLATE',
            'editEmail' => 'ACYM_EDIT_EMAIL',
            'recipients' => 'ACYM_RECIPIENTS',
            'sendSettings' => 'ACYM_SEND_SETTINGS',
            'summary' => 'ACYM_SUMMARY',
        ];

        parent::__construct();
    }
}

