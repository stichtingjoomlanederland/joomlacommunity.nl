<?php
defined('_JEXEC') or die('Restricted access');
?><?php

class acymactionClass extends acymClass
{
    var $table = 'action';
    var $pkey = 'id';

    public function getActionsByStepId($stepId)
    {
        $query = 'SELECT action.* FROM #__acym_action AS action LEFT JOIN #__acym_condition AS conditionT ON action.condition_id = conditionT.id WHERE conditionT.step_id = '.intval($stepId).' ORDER BY action.order';

        return acym_loadObjectList($query);
    }

    public function getActionsByConditionId($id)
    {
        $query = 'SELECT action.* FROM #__acym_action as action LEFT JOIN #__acym_condition as acycondition ON acycondition.id = action.condition_id WHERE acycondition.id = '.intval($id);

        return acym_loadObjectList($query);
    }
}

