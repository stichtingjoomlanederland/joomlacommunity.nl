<?php

class ComDocmanModelUsers extends ComKoowaModelUsers
{

    /**
     * Add group_id into state
     *
     * @param KObjectConfig $config
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->getState()
             ->insert('filter_group', 'int', '')
             ->insert('filter_range', 'cmd', '')
             ->insert('block', 'int')
             ->insert('unactivated', 'int');
    }

    protected function _buildQueryWhere(KDatabaseQueryInterface $query)
    {
        //Call parent
        parent::_buildQueryWhere($query);

        $state = $this->getState();

        if($state->filter_group) {
            $query->where("m.group_id IN :group")->bind(array("group"=>(array) $state->filter_group));
        }

        if($state->filter_range)
        {
            $range_map = array(
              'today' => '-1 day',
              'last-week' => '-7 day',
              'last-month' => '-1 month',
              'last-three-months' => '-3 month',
              'last-six-months' => '-6 month',
              'last-year' => '-1 year',
              'over-a-year' => '-100 year',
            );

            $range_start = $this->getObject('date');
            $range_start->modify($range_map[$state->filter_range]);
            $start_date = $range_start->format('Y-m-d H:i:s');

            $query->where(':start_date <= tbl.registerDate')->bind(array('start_date' => $start_date));
        }

        if($state->block){
          $query->where('tbl.block = 1');
        }

        if($state->unactivated){
          $query->where('tbl.activation NOT IN (\'\', 0)');
        }
    }

    protected function _buildQueryJoins(KDatabaseQueryInterface $query)
    {
        parent::_buildQueryJoins($query);

        $state = $this->getState();

        if($state->filter_group) {
            $query->join(array('m' => 'user_usergroup_map'), 'tbl.id = m.user_id');
        }
    }
}
