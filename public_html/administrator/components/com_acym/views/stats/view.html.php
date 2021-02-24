<?php

namespace AcyMailing\Views;

use AcyMailing\Libraries\acymView;

class StatsViewStats extends acymView
{
    var $tabs = [];

    public function __construct()
    {
        parent::__construct();

        $this->tabs = [
            'globalStats' => 'ACYM_OVERVIEW',
        ];
    }

    public function isMailSelected($mailId)
    {
        if (acym_level(1)) {
            $this->tabs['detailedStats'] = 'ACYM_DETAILED_STATS';
            if (!empty($mailId)) {
                $this->tabs['clickMap'] = 'ACYM_CLICK_MAP';
                $this->tabs['linksDetails'] = 'ACYM_LINKS_DETAILS';
                $this->tabs['userClickDetails'] = 'ACYM_USER_CLICK_DETAILS';
            }
        }
    }
}
