<?php
defined('_JEXEC') or die('Restricted access');
?><?php

class AutomationController extends acymController
{
    public function __construct()
    {
        parent::__construct();
        $this->breadcrumb[acym_translation('ACYM_AUTOMATION')] = acym_completeLink('automation');
        $this->loadScripts = [
            'all' => ['datepicker'],
        ];
        acym_setVar('edition', '1');
    }

    public function listing()
    {
        if (!acym_level(2)) {
            acym_redirect(acym_completeLink('dashboard&task=upgrade&version=enterprise', false, true));
        }

        if (acym_level(2)) {
            acym_session();
            $_SESSION['massAction'] = ['filters' => [], 'actions' => []];
            acym_setVar('layout', 'listing');
            $pageIdentifier = 'automation';
            $pagination = acym_get('helper.pagination');

            $searchFilter = acym_getVar('string', 'automation_search', '');
            $status = acym_getVar('string', 'automation_status', '');
            $tagFilter = acym_getVar('string', 'automation_tag', '');
            $ordering = acym_getVar('string', 'automation_ordering', 'id');
            $orderingSortOrder = acym_getVar('string', 'automation_ordering_sort_order', 'asc');

            $automationsPerPage = $pagination->getListLimit();
            $page = acym_getVar('int', 'automation_pagination_page', 1);


            $requestData = [
                'ordering' => $ordering,
                'search' => $searchFilter,
                'elementsPerPage' => $automationsPerPage,
                'offset' => ($page - 1) * $automationsPerPage,
                'tag' => $tagFilter,
                'ordering_sort_order' => $orderingSortOrder,
                'status' => $status,
            ];
            $matchingAutomations = $this->getMatchingElementsFromData($requestData, $status, $page);

            $pagination->setStatus($matchingAutomations['total']->total, $page, $automationsPerPage);

            $filters = [
                'all' => $matchingAutomations['total']->total,
                'active' => $matchingAutomations['total']->totalActive,
                'inactive' => $matchingAutomations['total']->total - $matchingAutomations['total']->totalActive,
            ];


            $data = [
                'allAutomations' => $matchingAutomations['elements'],
                'allTags' => acym_get('class.tag')->getAllTagsByType('automation'),
                'pagination' => $pagination,
                'search' => $searchFilter,
                'ordering' => $ordering,
                'tag' => $tagFilter,
                'status' => $status,
                'orderingSortOrder' => $orderingSortOrder,
                'automationNumberPerStatus' => $filters,
            ];

            parent::display($data);
        }
    }

    public function info()
    {
        if (!acym_level(2)) {
            acym_redirect(acym_completeLink('dashboard&task=upgrade&version=enterprise', false, true));
        }

        acym_setVar('layout', 'info');
        acym_setVar('step', 'info');

        $automationId = acym_getVar('int', 'id');
        $automationClass = acym_get('class.automation');
        $stepClass = acym_get('class.step');

        if (empty($automationId)) {
            $automation = new stdClass();
            $step = new stdClass();

            $automation->name = '';
            $automation->description = '';
            $automation->active = 0;
            $this->breadcrumb[acym_translation('ACYM_NEW_AUTOMATION')] = acym_completeLink('automation&task=edit&step=info');
        } else {
            $automation = $automationClass->getOneById($automationId);
            $this->breadcrumb[acym_translation($automation->name)] = acym_completeLink('automation&task=edit&step=info&id='.$automation->id);

            $step = $stepClass->getOneStepByAutomationId($automationId);
        }

        $defaultValues = empty($step->triggers) ? [] : json_decode($step->triggers, true);
        $triggers = ['classic' => [], 'user' => []];
        acym_trigger('onAcymDeclareTriggers', [&$triggers, &$defaultValues]);

        $data = [
            'automation' => $automation,
            'step' => $step,
            'user' => $triggers['user'],
            'classic' => $triggers['classic'],
            'defaultValues' => !empty($defaultValues) ? array_keys($defaultValues) : [],
            'type_trigger' => !empty($defaultValues) ? $defaultValues['type_trigger'] : '',
        ];

        parent::display($data);
    }

    public function condition()
    {
        if (!acym_level(2)) {
            acym_redirect(acym_completeLink('dashboard&task=upgrade&version=enterprise', false, true));
        }

        acym_setVar('layout', 'condition');
        acym_setVar('layout', 'condition');
        $id = acym_getVar('int', 'id');
        $stepId = acym_getVar('int', 'stepId');
        $automationClass = acym_get('class.automation');
        $stepClass = acym_get('class.step');
        $conditionClass = acym_get('class.condition');

        $conditionObject = new stdClass();
        $step = new stdClass();

        if (!empty($id)) {
            $automation = $automationClass->getOneById($id);
            $this->breadcrumb[acym_translation($automation->name)] = acym_completeLink('automation&task=edit&step=condition&id='.$automation->id);

            $steps = $stepClass->getStepsByAutomationId($id);
            if (!empty($steps)) {
                $step = $steps[0];
                $conditions = $conditionClass->getConditionsByStepId($step->id);
                if (!empty($conditions)) $conditionObject = $conditions[0];
            }
        } else {
            $automation = new stdClass();
            $this->breadcrumb[acym_translation('ACYM_NEW_MASS_ACTION')] = acym_completeLink('automation&task=edit&step=condition');

            $conditionObject->conditions = json_encode($_SESSION['massAction']['conditions']);
        }

        if (empty($conditionObject->conditions)) $conditionObject->conditions = '[]';

        $currentConditions = empty($conditionObject->conditions) ? [] : json_decode($conditionObject->conditions, true);
        $currentTriggers = empty($step->triggers) ? [] : json_decode($step->triggers, true);
        $typeCondition = 'classic';
        if (empty($currentConditions['type_condition'])) {
            $typeCondition = (empty($currentTriggers) || $currentTriggers['type_trigger'] != 'user') ? 'classic' : 'user';
        } elseif ($currentConditions['type_condition'] == $currentTriggers['type_trigger']) {
            $typeCondition = $currentConditions['type_condition'];
        } elseif ($currentConditions['type_condition'] == 'user' && $currentTriggers['type_trigger'] == 'classic') {
            $conditionObject->conditions = [];
            $typeCondition = $currentTriggers['type_trigger'];
        }

        $conditions = ['user' => [], 'classic' => []];
        $conditions['user'] = [];
        $conditions['classic'] = [];
        acym_trigger('onAcymDeclareConditions', [&$conditions]);


        $selectCondition = new stdClass();
        $selectCondition->name = acym_translation('ACYM_SELECT_CONDITION');
        $selectCondition->option = '';
        array_unshift($conditions['both'], $selectCondition);

        $conditionsUser = ['name' => [], 'option' => []];
        $conditionsClassic = ['name' => [], 'option' => []];
        foreach ($conditions['both'] as $key => $condition) {
            $conditionsUser['name'][$key] = $condition->name;
            $conditionsUser['option'][$key] = $condition->option;
            $conditionsClassic['name'][$key] = $condition->name;
            $conditionsClassic['option'][$key] = $condition->option;
        }

        foreach ($conditions['user'] as $key => $condition) {
            $conditionsUser['name'][$key] = $condition->name;
            $conditionsUser['option'][$key] = $condition->option;
        }

        foreach ($conditions['classic'] as $key => $condition) {
            $conditionsClassic['name'][$key] = $condition->name;
            $conditionsClassic['option'][$key] = $condition->option;
        }

        $data = [
            'automation' => $automation,
            'step' => $step,
            'condition' => $conditionObject,
            'id' => $id,
            'step_automation_id' => empty($step->id) ? 0 : $step->id,
            'user_name' => $conditionsUser['name'],
            'user_option' => json_encode(preg_replace_callback('#(data\-switch=")(switch_.+id=")(switch_.+for=")(switch_)#Uis', [$this, 'switches'], $conditionsUser['option'])),
            'classic_name' => $conditionsClassic['name'],
            'classic_option' => json_encode(preg_replace_callback('#(data\-switch=")(switch_.+id=")(switch_.+for=")(switch_)#Uis', [$this, 'switches'], $conditionsClassic['option'])),
            'type_trigger' => empty($step->triggers) ? 'classic' : json_decode($step->triggers, true)['type_trigger'],
            'type_condition' => $typeCondition,
        ];

        parent::display($data);
    }

    public function filter()
    {
        acym_session();
        acym_setVar('layout', 'filter');
        $id = acym_getVar('int', 'id');
        $stepId = acym_getVar('int', 'stepId');
        $automationClass = acym_get('class.automation');
        $stepClass = acym_get('class.step');
        $actionClass = acym_get('class.action');
        $conditionClass = acym_get('class.condition');

        $action = new stdClass();
        $step = new stdClass();
        $condition = new stdClass();

        if (!empty($id)) {
            $automation = $automationClass->getOneById($id);
            $this->breadcrumb[acym_translation($automation->name)] = acym_completeLink('automation&task=edit&step=filter&id='.$automation->id);

            $steps = $stepClass->getStepsByAutomationId($id);
            if (!empty($steps)) {
                $step = $steps[0];
                $conditions = $conditionClass->getConditionsByStepId($step->id);
                if (empty($conditions)) {
                    acym_setVar('stepId', $stepId);
                    acym_setVar('id', $id);
                    acym_enqueueMessage(acym_translation('ACYM_PLEASE_SET_CONDITION_OR_SAVE'), 'warning');

                    return $this->condition();
                }

                $condition = $conditions[0];
                $actions = $actionClass->getActionsByConditionId($condition->id);
                if (!empty($actions)) $action = $actions[0];
            }
        } else {
            $automation = new stdClass();
            $this->breadcrumb[acym_translation('ACYM_NEW_MASS_ACTION')] = acym_completeLink('automation&task=edit&step=filter');

            $action->filters = json_encode($_SESSION['massAction']['filters']);
        }

        if (empty($action->filters)) $action->filters = '[]';

        $currentFilters = empty($action->filters) ? [] : json_decode($action->filters, true);
        $currentTriggers = empty($step->triggers) ? [] : json_decode($step->triggers, true);
        if (empty($currentFilters)) {
            if (empty($currentTriggers) || $currentTriggers['type_trigger'] != 'user') {
                $typeFilter = 'classic';
            } else {
                $typeFilter = 'user';
            }
        } else {
            $typeFilter = $currentFilters['type_filter'];
        }

        $filters = [];
        acym_trigger('onAcymDeclareFilters', [&$filters]);

        uasort(
            $filters,
            function ($a, $b) {
                return strcmp(strtolower($a->name), strtolower($b->name));
            }
        );

        $selectFilter = new stdClass();
        $selectFilter->name = acym_translation('ACYM_SELECT_FILTER');
        $selectFilter->option = '';
        array_unshift($filters, $selectFilter);

        $filtersClassic = ['name' => [], 'option'];

        foreach ($filters as $key => $filter) {
            $filtersClassic['name'][$key] = $filter->name;
            $filtersClassic['option'][$key] = $filter->option;
        }

        $data = [
            'automation' => $automation,
            'step' => $step,
            'action' => $action,
            'id' => $id,
            'condition' => $condition,
            'step_automation_id' => empty($step->id) ? 0 : $step->id,
            'classic_name' => $filtersClassic['name'],
            'classic_option' => json_encode(preg_replace_callback('#(data\-switch=")(switch_.+id=")(switch_.+for=")(switch_)#Uis', [$this, 'switches'], $filtersClassic['option'])),
            'type_trigger' => empty($step->triggers) ? 'classic' : json_decode($step->triggers, true)['type_trigger'],
            'type_filter' => $typeFilter,
        ];

        parent::display($data);

        if (!acym_level(2)) {
            acym_redirect(acym_completeLink('dashboard&task=upgrade&version=enterprise', false, true));
        }
    }

    public function switches($matches)
    {
        return $matches[1].'__numand__'.$matches[2].'__numand__'.$matches[3].'__numand__'.$matches[4];
    }

    public function action()
    {
        acym_session();
        acym_setVar('layout', 'action');
        $id = acym_getVar('int', 'id');
        $mailId = acym_getVar('string', 'mailid');
        $andMailEditor = acym_getVar('int', 'and');
        $stepClass = acym_get('class.step');
        $automationClass = acym_get('class.automation');
        $actionClass = acym_get('class.action');
        $conditionClass = acym_get('class.condition');
        $mailClass = acym_get('class.mail');

        $actionObject = new stdClass();
        $step = new stdClass();
        $condition = new stdClass();

        if (!empty($id)) {
            $automation = $automationClass->getOneById($id);
            $this->breadcrumb[acym_translation($automation->name)] = acym_completeLink('automation&task=edit&step=action&id='.$automation->id);
            $steps = $stepClass->getStepsByAutomationId($id);

            if (!empty($steps)) {
                $step = $steps[0];
                $conditions = $conditionClass->getConditionsByStepId($step->id);
                if (empty($conditions)) {
                    acym_setVar('stepId', $step->id);
                    acym_setVar('id', $id);
                    acym_enqueueMessage(acym_translation('ACYM_PLEASE_SET_CONDITION_OR_SAVE'), 'warning');

                    return $this->condition();
                }

                $condition = $conditions[0];
                $actions = $actionClass->getActionsByConditionId($condition->id);
                if (!empty($actions)) $actionObject = $actions[0];
            }
        } else {
            $automation = new stdClass();
            $this->breadcrumb[acym_translation('ACYM_NEW_MASS_ACTION')] = acym_completeLink('automation&task=edit&step=action');

            $actionObject->actions = $_SESSION['massAction']['actions'];
        }

        if (!empty($actionObject->actions) && !is_array($actionObject->actions)) $actionObject->actions = json_decode($actionObject->actions, true);

        if (!empty($actionObject->actions[$andMailEditor]) && !empty($mailId) || !empty($actionObject->actions[$andMailEditor]['acy_add_queue']['mail_id'])) {
            $mail = $mailClass->getOneById(empty($mailId) ? $actionObject->actions[$andMailEditor]['acy_add_queue']['mail_id'] : $mailId);
            if (!empty($mail)) {
                $actionObject->actions[$andMailEditor]['acy_add_queue']['mail_id'] = $mail->id;
                $actionObject->actions[$andMailEditor]['acy_add_queue']['mail_name'] = empty($mail->subject) ? $mail->name : $mail->subject;
            }
        }

        if (!empty($actionObject->actions)) {
            foreach ($actionObject->actions as $and => $actions) {
                foreach ($actions as $name => $actionOption) {
                    if ('acy_add_queue' == $name && !empty($actionObject->actions[$and][$name]['mail_id'])) {
                        $mail = $mailClass->getOneById($actionObject->actions[$and][$name]['mail_id']);
                        if (!empty($mail)) {
                            $actionObject->actions[$and][$name]['mail_id'] = $mail->id;
                            $actionObject->actions[$and][$name]['mail_name'] = $mail->name;
                        } else {
                            $actionObject->actions[$and][$name]['mail_id'] = '';
                        }
                    }
                }
            }
        }

        $actionObject->actions = empty($actionObject->actions) ? '[]' : json_encode($actionObject->actions);

        $actions = [];
        acym_trigger('onAcymDeclareActions', [&$actions]);

        uasort(
            $actions,
            function ($a, $b) {
                return strcmp(strtolower($a->name), strtolower($b->name));
            }
        );

        $firstAction = new stdClass();
        $firstAction->name = acym_translation('ACYM_CHOOSE_ACTION');
        $firstAction->option = '';
        array_unshift($actions, $firstAction);

        $actionsOption = [];

        foreach ($actions as $key => $action) {
            $actionsOption[$key] = $action->name;
        }

        $data = [
            'automation' => $automation,
            'step' => $step,
            'condition' => $condition,
            'action' => $actionObject,
            'actionsOption' => $actionsOption,
            'actions' => json_encode($actions),
            'id' => empty($id) ? '' : $id,
            'step_automation_id' => empty($step->id) ? 0 : $step->id,
        ];

        parent::display($data);

        if (!acym_level(2)) {
            acym_redirect(acym_completeLink('dashboard&task=upgrade&version=enterprise', false, true));
        }
    }

    public function summary()
    {
        acym_session();
        acym_setVar('layout', 'summary');
        $automationClass = acym_get('class.automation');
        $stepClass = acym_get('class.step');
        $conditionClass = acym_get('class.condition');
        $actionClass = acym_get('class.action');
        $id = acym_getVar('int', 'id');
        $massAction = empty($_SESSION['massAction']) ? '' : $_SESSION['massAction'];

        $automation = new stdClass();
        $step = new stdClass();
        $action = new stdClass();
        $condition = new stdClass();

        if (!empty($id)) {
            $automation = $automationClass->getOneById($id);
            $this->breadcrumb[acym_translation($automation->name)] = acym_completeLink('automation&task=edit&step=summary&id='.$automation->id);
            $steps = $stepClass->getStepsByAutomationId($id);

            if (!empty($steps)) {
                $step = $steps[0];
                if (!empty($step->triggers)) $step->triggers = json_decode($step->triggers, true);
                acym_trigger('onAcymDeclareSummary_triggers', [&$step]);

                $conditions = $conditionClass->getConditionsByStepId($step->id);
                if (!empty($conditions)) {
                    $condition = $conditions[0];
                    $condition->conditions = json_decode($condition->conditions, true);
                    $actions = $actionClass->getActionsByConditionId($condition->id);
                    if (!empty($actions)) $action = $actions[0];
                    foreach ($condition->conditions as $or => $orValues) {
                        if ($or === 'type_condition') continue;
                        foreach ($orValues as $and => $andValues) {
                            acym_trigger('onAcymDeclareSummary_conditions', [&$condition->conditions[$or][$and]]);
                        }
                    }
                }

                if (!empty($action->filters)) $action->filters = json_decode($action->filters, true);

                if (!empty($action->actions)) $action->actions = json_decode($action->actions, true);
            }
        } elseif (!empty($massAction)) {
            $action->filters = !empty($massAction['filters']) ? $massAction['filters'] : '';
            $action->actions = !empty($massAction['actions']) ? $massAction['actions'] : '';
            $this->breadcrumb[acym_translation('ACYM_NEW_MASS_ACTION')] = acym_completeLink('automation&task=edit&step=summary');
        }


        if (!empty($action->filters)) {
            foreach ($action->filters as $or => $orValues) {
                if ($or === 'type_filter') continue;
                foreach ($orValues as $and => $andValues) {
                    acym_trigger('onAcymDeclareSummary_filters', [&$action->filters[$or][$and]]);
                }
            }
        }
        if (!empty($action->actions)) {
            foreach ($action->actions as $and => $andValue) {
                acym_trigger('onAcymDeclareSummary_actions', [&$action->actions[$and]]);
            }
        }

        $data = [
            'id' => $id,
            'automation' => $automation,
            'step' => $step,
            'action' => $action,
            'condition' => $condition,
        ];

        parent::display($data);

        if (!acym_level(2)) {
            acym_redirect(acym_completeLink('dashboard&task=upgrade&version=enterprise', false, true));
        }
    }

    private function _saveInfos($isMassAction = false)
    {
        if ($isMassAction) {
            acym_session();
        }

        $automationId = acym_getVar('int', 'id');
        $automation = acym_getVar('array', 'automation');
        $automationClass = acym_get('class.automation');

        $stepAutomationId = acym_getVar('int', 'stepAutomationId');
        $stepAutomation = acym_getVar('array', 'stepAutomation');
        $stepClass = acym_get('class.step');

        if (!empty($automationId)) {
            $automation['id'] = $automationId;
        }

        if (!empty($stepAutomationId)) {
            $stepAutomation['id'] = $stepAutomationId;
        }

        $typeTrigger = acym_getVar('string', 'type_trigger');

        if (empty($automation['admin']) && empty($automation['name'])) {
            return false;
        }

        if (empty($stepAutomation['triggers'][$typeTrigger])) {
            acym_enqueueMessage(acym_translation('ACYM_PLEASE_SELECT_ONE_TRIGGER'), 'error');

            $this->info();

            return false;
        }

        $stepAutomation['triggers'][$typeTrigger]['type_trigger'] = $typeTrigger;
        $stepAutomation['triggers'] = json_encode($stepAutomation['triggers'][$typeTrigger]);

        $stepAutomation['automation_id'] = $automationId;

        foreach ($automation as $column => $value) {
            acym_secureDBColumn($column);
        }

        foreach ($stepAutomation as $stepColumn => $stepValue) {
            acym_secureDBColumn($stepColumn);
        }

        $automation = (object)$automation;
        $stepAutomation = (object)$stepAutomation;

        $automation->id = $automationClass->save($automation);
        $stepAutomation->automation_id = $automation->id;
        $stepAutomation->id = $stepClass->save($stepAutomation);

        $returnIds = [
            "automationId" => $automation->id,
            "stepId" => $stepAutomation->id,
            "typeTrigger" => $typeTrigger,
        ];

        if ($isMassAction) {
            return true;
        } elseif (!empty($returnIds['automationId']) && !empty($returnIds['stepId'])) {
            return $returnIds;
        } else {
            return false;
        }
    }

    private function _saveConditions($isMassAction = false)
    {
        $automationID = acym_getVar('int', 'id');
        $conditionId = acym_getVar('int', 'conditionId');
        $condition = acym_getVar('array', 'acym_condition', []);
        $conditionClass = acym_get('class.condition');

        $stepAutomationId = acym_getVar('int', 'stepAutomationId');

        if (!empty($stepAutomationId)) {
            $stepAutomation['id'] = $stepAutomationId;
        }

        if (!empty($conditionId)) {
            $condition['id'] = $conditionId;
        }

        $condition['conditions']['type_condition'] = acym_getVar('string', 'type_condition');

        if ($isMassAction) {
            acym_session();
            $_SESSION['massAction']['conditions'] = $condition['conditions'];

            return true;
        }

        $condition['conditions'] = json_encode($condition['conditions']);

        $condition['step_id'] = $stepAutomationId;

        foreach ($condition as $column => $value) {
            acym_secureDBColumn($column);
        }

        $condition = (object)$condition;

        $condition->id = $conditionClass->save($condition);

        $returnIds = [
            'automationId' => $automationID,
            'stepId' => $stepAutomationId,
            'conditionId' => $condition->id,
        ];

        return $returnIds;
    }

    private function _saveFilters($isMassAction = false)
    {
        $automationID = acym_getVar('int', 'id');
        $actionId = acym_getVar('int', 'actionId');
        $action = acym_getVar('array', 'acym_action', []);
        $actionClass = acym_get('class.action');
        $conditionId = acym_getVar('int', 'conditionId');

        $stepAutomationId = acym_getVar('int', 'stepAutomationId');

        if (!empty($stepAutomationId)) {
            $stepAutomation['id'] = $stepAutomationId;
        }

        if (!empty($conditionId)) {
            $action['condition_id'] = $conditionId;
        }

        if (!empty($actionId)) {
            $action['id'] = $actionId;
        }

        $action['filters']['type_filter'] = acym_getVar('string', 'type_filter');

        if ($isMassAction) {
            acym_session();
            $_SESSION['massAction']['filters'] = $action['filters'];

            return true;
        }

        $action['filters'] = json_encode($action['filters']);

        $action['order'] = 1;

        foreach ($action as $column => $value) {
            acym_secureDBColumn($column);
        }

        $action = (object)$action;

        $action->id = $actionClass->save($action);

        $returnIds = [
            'automationId' => $automationID,
            'stepId' => $stepAutomationId,
            'actionId' => $action->id,
        ];

        return $returnIds;
    }

    private function _saveActions($isMassAction = false)
    {
        if ($isMassAction) {
            acym_session();
        }

        $automationID = acym_getVar('int', 'id');
        $stepID = acym_getVar('int', 'id');
        $actionId = acym_getVar('int', 'actionId');
        $action = acym_getVar('array', 'acym_action');
        $actionClass = acym_get('class.action');
        $stepAutomationId = acym_getVar('int', 'stepAutomationId');
        $conditionId = acym_getVar('int', 'conditionId');

        if (!empty($stepAutomationId)) {
            $stepAutomation['id'] = $stepAutomationId;
        }

        if ((!empty($conditionId))) {
            $action['condition_id'] = $conditionId;
        }

        if (!empty($actionId)) {
            $action['id'] = $actionId;
        }

        if (empty($action['actions'])) {
            $action['actions'] = [];
        }

        if ($isMassAction) {
            $_SESSION['massAction']['actions'] = $action['actions'];

            return true;
        }

        $action['actions'] = json_encode($action['actions']);

        foreach ($action as $column => $value) {
            acym_secureDBColumn($column);
        }

        $action = (object)$action;

        $action->id = $actionClass->save($action);

        $returnIds = [
            'automationId' => $automationID,
            'stepId' => $stepAutomationId,
            'actionId' => $action->id,
        ];

        return $returnIds;
    }

    private function _saveAutomation($from, $isMassAction = false)
    {
        if ($isMassAction) {
            acym_session();
        }

        $automationId = acym_getVar('int', 'id');
        $automation = acym_getVar('array', 'automation');
        $automationClass = acym_get('class.automation');

        $stepAutomationId = acym_getVar('int', 'stepAutomationId');
        $stepAutomation = acym_getVar('array', 'stepAutomation');
        $stepClass = acym_get('class.step');

        if (!empty($automationId)) {
            $automation['id'] = $automationId;
        }

        if (!empty($stepAutomationId)) {
            $stepAutomation['id'] = $stepAutomationId;
        }

        if ($from == 'info') {
            $typeTrigger = acym_getVar('string', 'type_trigger');

            if (empty($automation['name'])) {
                return false;
            }

            if (empty($stepAutomation['triggers'][$typeTrigger])) {
                acym_enqueueMessage(acym_translation('ACYM_PLEASE_SELECT_ONE_TRIGGER'), 'error');

                $this->info();

                return false;
            }

            $stepAutomation['triggers'][$typeTrigger]['type_trigger'] = $typeTrigger;
            $stepAutomation['triggers'] = json_encode($stepAutomation['triggers'][$typeTrigger]);

            $stepAutomation['automation_id'] = $automationId;

            foreach ($automation as $column => $value) {
                acym_secureDBColumn($column);
            }

            foreach ($stepAutomation as $stepColumn => $stepValue) {
                acym_secureDBColumn($stepColumn);
            }

            $automation = (object)$automation;
            $stepAutomation = (object)$stepAutomation;

            $saveIdStepAutomation = $stepClass->save($stepAutomation);
            $saveIdAutomation = $automationClass->save($automation);

            $returnIds = [
                "automationId" => $saveIdAutomation,
                "stepId" => $saveIdStepAutomation,
            ];

            if ($isMassAction) {
                return true;
            } elseif (!empty($returnIds['automationId']) && !empty($returnIds['stepId'])) {
                return $returnIds;
            } else return false;
        } elseif ($from == 'filters') {
            $stepAutomation['filters']['type_filter'] = acym_getVar('string', 'type_filter');
            if ($isMassAction) {
                $_SESSION['massAction']['filters'] = $stepAutomation['filters'];
            }
            $stepAutomation['filters'] = json_encode($stepAutomation['filters']);
        } elseif ($from == 'actions') {
            if (empty($stepAutomation['actions'])) {
                acym_enqueueMessage(acym_translation('ACYM_PLEASE_SET_ACTIONS'), 'error');
                if (!empty($automationId)) acym_setVar('id', $automationId);
                $this->action();

                return false;
            }
            if ($isMassAction) {
                $_SESSION['massAction']['actions'] = $stepAutomation['actions'];
            }
            $stepAutomation['actions'] = json_encode($stepAutomation['actions']);
        } elseif ($from == 'summary') {
            $automation = $automationClass->getOneById($automationId);
            $automation->active = 1;
        }

        if ($isMassAction) {
            return true;
        } else {
            switch ($from) {
                case 'info':
                case 'summary':
                    foreach ($automation as $column => $value) {
                        acym_secureDBColumn($column);
                    }

                    $automation = (object)$automation;

                    return $automationClass->save($automation);
                case 'filters':
                case 'actions':
                    $stepAutomation['automation_id'] = $automationId;
                    $stepAutomation['order'] = 1;

                    foreach ($stepAutomation as $column => $value) {
                        acym_secureDBColumn($column);
                    }

                    $stepAutomation = (object)$stepAutomation;

                    return $stepClass->save($stepAutomation);
                default:
                    return false;
            }
        }
    }

    public function saveExitInfo()
    {
        $ids = $this->_saveInfos();

        if (empty($ids)) {
            return;
        }

        acym_enqueueMessage(acym_translation('ACYM_SUCCESSFULLY_SAVED'), 'success');

        acym_setVar('id', $ids['automationId']);
        acym_setVar('stepId', $ids['stepId']);
        $this->listing();
    }

    public function saveInfo()
    {
        $ids = $this->_saveInfos();

        if (empty($ids)) {
            return;
        }

        acym_setVar('id', $ids['automationId']);
        acym_setVar('stepId', $ids['stepId']);
        $this->condition();
    }

    public function saveExitConditions()
    {
        $ids = $this->_saveConditions();

        if (empty($ids)) {
            return;
        }

        acym_enqueueMessage(acym_translation('ACYM_SUCCESSFULLY_SAVED'), 'success');

        $this->listing();
    }

    public function saveConditions()
    {
        $ids = $this->_saveConditions();

        if (empty($ids)) {
            return;
        }

        acym_setVar('id', $ids['automationId']);
        acym_setVar('stepId', $ids['stepId']);
        acym_setVar('conditionId', $ids['conditionId']);
        $this->action();
    }

    public function saveExitFilters()
    {
        $ids = $this->_saveFilters();

        if (empty($ids)) {
            return;
        }

        acym_enqueueMessage(acym_translation('ACYM_SUCCESSFULLY_SAVED'), 'success');

        $this->listing();
    }

    public function saveFilters()
    {
        $ids = $this->_saveFilters();

        if (empty($ids)) {
            return;
        }

        acym_setVar('id', $ids['automationId']);
        acym_setVar('stepId', $ids['stepId']);
        acym_setVar('actionId', $ids['actionId']);
        $this->summary();
    }

    public function saveExitActions()
    {
        $ids = $this->_saveActions();

        if (empty($ids)) {
            return;
        }

        acym_enqueueMessage(acym_translation('ACYM_SUCCESSFULLY_SAVED'), 'success');

        $this->listing();
    }

    public function saveActions()
    {
        $ids = $this->_saveActions();

        if (empty($ids)) {
            return;
        }

        acym_setVar('id', $ids['automationId']);
        acym_setVar('stepId', $ids['stepId']);
        acym_setVar('actionId', $ids['actionId']);
        $this->filter();
    }

    public function activeAutomation()
    {
        $automationClass = acym_get('class.automation');
        $automation = $automationClass->getOneById(acym_getVar('int', 'id'));
        $automation->active = 1;
        $saved = $automationClass->save($automation);
        if (!empty($saved)) {
            acym_enqueueMessage(acym_translation('ACYM_SUCCESSFULLY_SAVED'), 'success');
            $this->listing();
        } else {
            acym_enqueueMessage(acym_translation('ACYM_ERROR_SAVING'), 'error');
            $this->listing();
        }
    }


    public function setFilterMassAction()
    {
        $this->_saveFilters(true);
        $this->summary();
    }

    public function setActionMassAction()
    {
        $res = $this->_saveActions(true);
        if (!$res) return false;
        $this->filter();
    }

    public function processMassAction()
    {
        acym_session();
        $automationClass = acym_get('class.automation');
        $massAction = empty($_SESSION['massAction']) ? '' : $_SESSION['massAction'];
        if (!empty($massAction)) {
            $automation = new stdClass();
            $automation->filters = json_encode($massAction['filters']);
            $automation->actions = json_encode($massAction['actions']);
            $automationClass->execute($automation);

            if (!empty($automationClass->report)) {
                foreach ($automationClass->report as $oneReport) {
                    acym_enqueueMessage($oneReport, 'info');
                }
            }
        }
        $this->listing();
    }

    public function createMail()
    {
        $id = acym_getVar('int', 'id');
        $idAdmin = acym_getVar('boolean', 'automation_admin');
        $type = 'automation';
        if ($idAdmin) $type = 'automation_admin';
        $and = acym_getVar('string', 'and_action');
        $this->_saveActions(empty($id));
        $actions = acym_getVar('array', 'acym_action');
        $mailId = $actions['actions'][$and]['acy_add_queue']['mail_id'];
        acym_redirect(acym_completeLink('mails&task=edit&step=editEmail&type='.$type.'&from='.$mailId.'&return='.urlencode(acym_completeLink('automation&task=edit&step=action&id='.$id.'&fromMailEditor=1&mailid={mailid}&and='.$and)), false, true));
    }


    public function countresults()
    {
        $or = acym_getVar('int', 'or');
        $and = acym_getVar('int', 'and');
        $stepAutomation = acym_getVar('array', 'acym_action');

        if (empty($stepAutomation['filters'][$or][$and])) die(acym_translation('ACYM_AUTOMATION_NOT_FOUND'));

        $query = acym_get('class.query');
        $messages = '';

        foreach ($stepAutomation['filters'][$or][$and] as $filterName => $options) {
            $messages = acym_trigger('onAcymProcessFilterCount_'.$filterName, [&$query, &$options, &$and]);
            break;
        }

        echo implode(' | ', $messages);
        exit;
    }

    public function countResultsOrTotal()
    {
        $or = acym_getVar('int', 'or');
        $stepAutomation = acym_getVar('array', 'acym_action');

        $query = acym_get('class.query');

        if (!empty($stepAutomation) && !empty($stepAutomation['filters'][$or])) {
            foreach ($stepAutomation['filters'][$or] as $and => $andValues) {
                $and = intval($and);
                foreach ($andValues as $filterName => $options) {
                    acym_trigger('onAcymProcessFilter_'.$filterName, [&$query, &$options, &$and]);
                }
            }
        }

        $result = $query->count();

        echo acym_translation_sprintf('ACYM_SELECTED_USERS_TOTAL', $result);
        exit;
    }
}

