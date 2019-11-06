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

class acymentitySelectHelper
{
    var $svg;

    public function __construct()
    {
        $this->svg = acym_getSvg(ACYM_IMAGES.'loader.svg');
    }

    private function _getListing($type, $allSelector, $entity, $columnsToDisplay = [])
    {
        $display = '<div class="cell medium-auto grid-x acym_area acym__entity_select__'.$type.'">
                        <h5 class="cell font-bold acym_area_title text-center">'.acym_translation('ACYM_'.strtoupper($type).'_'.strtoupper($entity)).'</h5>
                        <div class="cell grid-x">
                        <div class="cell grid-x acym__entity_select__header">
                            <div class="cell grid-x">
                                <div class="cell"><input type="text" class="acym__light__input" v-model="'.$type.'Search" placeholder="'.acym_translation('ACYM_SEARCH').'"></div>
                                <div class="cell align-right grid-x acym__entity_select__select__all">
                                    <button type="button" v-show="!loading" v-if="displaySelectAll_'.$type.'" v-on:click="moveAll('.acym_escapeDB($type).')" class="cell shrink acym__entity_select__select__all__button acym__entity_select__select__all__button__'.$type.'">'.acym_translation('ACYM_'.strtoupper($allSelector).'_ALL').'</button>
                                </div>
                            </div>
                        </div>
                        <div v-infinite-scroll="loadMoreEntity'.ucfirst($type).'" :infinite-scroll-disabled="busy" class="acym__listing cell acym__entity_select__'.$type.'__listing acym__content">';
        $display .= '<div class="cell text-center acym__entity_select__title margin-top-1" v-show="Object.keys(entitiesToDisplay_'.$type.').length == 0 && !loading">'.acym_translation_sprintf('ACYM_THERE_ARE_NO_DATA_X', strtolower(acym_translation('ACYM_'.strtoupper($type)))).'</div>
                    <div class="cell acym_vcenter acym__listing__row grid-x acym__listing__row__header" v-if="Object.keys(entitiesToDisplay_'.$type.').length != 0">';
        foreach ($columnsToDisplay as $column) {
            $display .= '<div class="cell grid-x auto">'.acym_translation('ACYM_'.strtoupper($column)).'</div>';
        }
        $display .= '<div class="cell small-1"></div>
                    </div>
                    <div v-for="(entity, index) in entitiesToDisplay_'.$type.'" class="cell acym_vcenter acym__listing__row grid-x acym__entity_select__'.$type.'__listing__row" >';

        $display .= '<div v-for="column in columnsToDisplay" class="cell auto align-center acym__entity_select__columns">{{ entity[column] }}</div>';

        $display .= '<div class="cell small-1 vertical-align-middle text-center">
                        <div class="plus-container acym__entity_select__available__listing__row__select" v-on:click="selectEntity(entity.id)">
                          <div class="top-plus plus-bar"></div>
                          <div class="plus plus-bar"></div>
                          <div class="bottom-plus plus-bar"></div>
                        </div>
                        <div class="plus-container acym__entity_select__selected__listing__row__unselect" v-on:click="unselectEntity(entity.id)">
                          <div class="top-plus plus-bar"></div>
                          <div class="plus plus-bar"></div>
                          <div class="bottom-plus plus-bar"></div>
                        </div>
        			</div>
        		</div>
                    <div class="cell grid-x align-center acym__entity_select__loading margin-top-1"  v-show="loading"><div class="cell text-center acym__entity_select__title">'.acym_translation('ACYM_WE_ARE_LOADING_YOUR_DATA').'</div><div class="cell grid-x shrink margin-top-1">'.$this->svg.'</div></div>';
        $display .= '</div>';
        $display .= '</div>
                    </div>';

        return $display;
    }

    public function entitySelect($entity, $entityParams = [], $columnsToDisplay = ['name'], $buttonSubmit = ['text' => '', 'action' => '', 'class' => ''], $displaySelected = true)
    {
        $entityClass = acym_get('class.'.$entity);
        $entitySelectController = acym_get('controller.entitySelect');

        $columnJoin = '';
        if (!empty($columnsToDisplay['join'])) $columnJoin = explode('.', $columnsToDisplay['join']);

        $data = $entitySelectController->loadEntityBack($entity, 0, 500, $entityParams['join'], $columnsToDisplay);

        unset($columnsToDisplay['join']);

        if (empty($entityClass)) return false;

        if (empty($entityParams['elementsPerPage']) || $entityParams['elementsPerPage'] < 1) {
            $entityParams['elementsPerPage'] = acym_getCMSConfig('list_limit', 20);
        }

        if (!empty($columnJoin)) $columnJoin = 'data-column-join="'.$columnJoin[1].'" data-table-join="'.$columnJoin[0].'"';

        $display = '<div style="display: none;" id="acym__entity_select" class="acym__entity_select cell grid-x" data-display-selected="'.($displaySelected ? 'true' : 'false').'" data data-entity="'.acym_escape($entity).'" data-type="select" data-columns="'.implode(',', $columnsToDisplay).'" data-join="'.$entityParams['join'].'" '.$columnJoin.'>';

        $display .= $this->_getListing('available', 'select', $entity, $columnsToDisplay);

        $display .= '<div class="cell medium-shrink text-center grid-x acym_vcenter"><i class="fa fa-arrows-h cell"></i></div>';

        $display .= $this->_getListing('selected', 'unselect', $entity, $columnsToDisplay);

        if (!empty($buttonSubmit['text'])) {
            $class = !empty($buttonSubmit['action']) ? 'acy_button_submit' : 'acym__entity_select__button__close';
            if (!empty($buttonSubmit['class'])) $class .= ' '.$buttonSubmit['class'];
            $buttonSubmit['action'] = !empty($buttonSubmit['action']) ? 'data-task="'.$buttonSubmit['action'].'"' : '';
            $display .= '<div class="cell grid-x align-center"><button type="button" id="acym__entity_select__button__submit" class="cell shrink grid-x '.$class.' button" '.$buttonSubmit['action'].'>'.$buttonSubmit['text'].'</button></div>';
        }

        $display .= '<input type="hidden" class="acym__entity_select__selected" name="acym__entity_select__selected" value="">';
        $display .= '<input type="hidden" class="acym__entity_select__unselected" name="acym__entity_select__unselected" value="">';

        if (!empty($data)) $display .= '<input type="hidden" value="'.acym_escape(json_encode($data['data'])).'" id="acym__entity_select__data">';

        $display .= '</div>';

        return $display;
    }

}

