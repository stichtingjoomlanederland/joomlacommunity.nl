<?php
/**
 * @package	AcyMailing for Joomla
 * @version	6.5.0
 * @author	acyba.com
 * @copyright	(C) 2009-2019 ACYBA SAS - All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

defined('_JEXEC') or die('Restricted access');
?>
<?php

class EntitySelectController extends acymController
{
    var $entitySelectHelper;

    public function __construct()
    {
        parent::__construct();
        $this->entitySelectHelper = acym_get('helper.entitySelect');
        $this->loadScripts = [
            'all' => ['vue-applications'],
        ];
    }

    public function loadEntityFront()
    {
        $entity = acym_getVar('string', 'entity');
        $offset = acym_getVar('int', 'offset');
        $perCalls = acym_getVar('int', 'perCalls');
        $join = acym_getVar('string', 'join');
        $joinColumnGet = acym_getVar('string', 'join_table', '');
        $columnsToDisplay = explode(',', acym_getVar('string', 'columns', ''));
        if (!empty($joinColumnGet)) $columnsToDisplay['join'] = $joinColumnGet;
        echo json_encode($this->loadEntityBack($entity, $offset, $perCalls, $join, $columnsToDisplay));
        exit;
    }

    public function loadEntityBack($entity, $offset, $perCalls, $join, $columnsToDisplay)
    {
        if (empty($entity) || (empty($offset) && 0 !== $offset) || empty($perCalls)) {
            return ['error' => acym_translation('ACYM_MISSING_PARAMETERS')];
        }

        $entityParams = ['offset' => $offset, 'elementsPerPage' => $perCalls];
        if (!empty($join)) $entityParams['join'] = $join;
        if (!empty($columnsToDisplay)) $entityParams['columns'] = $columnsToDisplay;

        $entityClass = acym_get('class.'.$entity);
        $availableEntity = $entityClass->getMatchingElements($entityParams);

        return ['data' => empty($availableEntity) ? 'end' : $availableEntity];
    }

    public function loadEntitySelect()
    {
        $join = acym_getVar('string', 'join');
        if (empty($join)) {
            echo json_encode(['data' => 'end']);
            exit;
        } else {
            $this->loadEntity();
        }
    }

    public function getEntityNumber()
    {
        $entity = acym_getVar('string', 'entity');
        $join = acym_getVar('string', 'join');

        if (empty($entity)) {
            echo json_encode(['error' => acym_translation('ACYM_MISSING_PARAMETERS')]);
            exit;
        }

        $entityClass = acym_get('class.'.$entity);

        $joinQuery = '';
        if (!empty($join)) $joinQuery = $entityClass->getJoinForQuery($join);

        $query = 'SELECT COUNT(id) FROM #__acym_'.acym_escape($entity).' AS '.$entity.$joinQuery;

        echo json_encode(['data' => acym_loadResult($query)]);
        exit;
    }
}

