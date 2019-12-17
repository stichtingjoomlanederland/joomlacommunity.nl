<?php
/**
 * @package	AcyMailing for Joomla
 * @version	6.6.1
 * @author	acyba.com
 * @copyright	(C) 2009-2019 ACYBA SAS - All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

defined('_JEXEC') or die('Restricted access');
?>
<?php

class acymPlugin extends acymObject
{
    var $pluginHelper;
    var $cms = 'all';
    var $name = '';

    var $installed = true;
    var $pluginsPath = '';

    var $rootCategoryId = 1;
    var $categories;
    var $catvalues = [];
    var $cats = [];

    var $tags = [];
    var $pageInfo;
    var $searchFields = [];
    var $querySelect = '';
    var $query = '';
    var $filters = [];
    var $elementIdTable = '';
    var $elementIdColumn = '';

    var $pluginDescription;
    var $generateCampaignResult;

    var $defaultValues;

    public function __construct()
    {
        parent::__construct();

        $this->pluginHelper = acym_get('helper.plugin');
        $this->pluginsPath = acym_getPluginsPath(__FILE__, __DIR__);
        $this->pageInfo = new stdClass();

        $this->pluginDescription = new stdClass();
        $this->pluginDescription->plugin = get_class($this);

        $this->name = strtolower(substr($this->pluginDescription->plugin, 7));

        $this->generateCampaignResult = new stdClass();
        $this->generateCampaignResult->status = true;
        $this->generateCampaignResult->message = '';
    }

    protected function displaySelectionZone($zoneContent)
    {
        $output = '<p class="acym__wysid__right__toolbar__p acym__wysid__right__toolbar__p__open">'.acym_translation('ACYM_CONTENT_TO_INSERT').'<i class="acymicon-keyboard_arrow_up"></i></p>';
        $output .= '<div class="acym__wysid__right__toolbar__design--show acym__wysid__right__toolbar__design acym__wysid__context__modal__container">';
        $output .= $zoneContent;
        $output .= '</div>';

        return $output;
    }

    public function displayListing()
    {
        echo $this->prepareListing();
    }

    public function prepareListing()
    {
        $this->pageInfo->limit = 5;
        $this->pageInfo->page = acym_getVar('int', 'pagination_page_ajax', 1);
        $this->pageInfo->start = ($this->pageInfo->page - 1) * $this->pageInfo->limit;
        $this->pageInfo->search = acym_getVar('string', 'plugin_search', '');
        $this->pageInfo->filter_cat = acym_getVar('int', 'plugin_category', 0);
        $this->pageInfo->orderdir = 'DESC';

        if (!empty($this->pageInfo->search) && !empty($this->searchFields)) {
            $searchVal = '%'.acym_getEscaped($this->pageInfo->search, true).'%';
            $this->filters[] = implode(' LIKE '.acym_escapeDB($searchVal).' OR ', $this->searchFields).' LIKE '.acym_escapeDB($searchVal);
        }

        return '';
    }

    public function getElements()
    {
        $conditions = '';
        $ordering = '';
        if (!empty($this->filters)) $conditions = ' WHERE ('.implode(') AND (', $this->filters).')';
        if (!empty($this->pageInfo->order)) $ordering = ' ORDER BY '.acym_secureDBColumn($this->pageInfo->order).' '.acym_secureDBColumn($this->pageInfo->orderdir);

        $rows = acym_loadObjectList($this->querySelect.$this->query.$conditions.$ordering, '', $this->pageInfo->start, $this->pageInfo->limit);
        $this->pageInfo->total = acym_loadResult('SELECT COUNT(*) '.$this->query.$conditions.$ordering);

        if (!empty($this->defaultValues->id)) {
            $found = false;
            foreach ($rows as $oneRow) {
                if ($oneRow->{$this->elementIdColumn} === $this->defaultValues->id) $found = true;
            }

            if (!$found) {
                $this->filters[] = $this->elementIdTable.'.'.$this->elementIdColumn.' = '.intval($this->defaultValues->id);
                $row = acym_loadObject($this->querySelect.$this->query.$conditions);
                if (!empty($row)) $rows[] = $row;
            }
        }

        return $rows;
    }

    protected function getFilteringZone($categoryFilter = true)
    {
        $result = '<div class="grid-x" id="plugin_listing_filters">
                    <div class="cell medium-6">
                        <input type="text" name="plugin_search" placeholder="'.acym_escape(acym_translation('ACYM_SEARCH')).'"/>
                    </div>
                    <div class="cell medium-6 grid-x">
                        <div class="cell hide-for-small-only medium-auto"></div>
                        <div class="cell medium-shrink">';

        if ($categoryFilter) $result .= $this->getCategoryFilter();

        $result .= '</div>
                    </div>
                </div>';

        return $result;
    }

    protected function getCategoryFilter()
    {
        $filter_cat = acym_getVar('int', 'plugin_category', 0);

        $this->cats = [];
        if (!empty($this->categories)) {
            foreach ($this->categories as $oneCat) {
                $this->cats[$oneCat->parent_id][] = $oneCat;
            }
        }
        $this->catvalues = [];
        $this->catvalues[] = acym_selectOption(0, 'ACYM_ALL');
        $this->handleChildrenCategories($this->rootCategoryId);

        return acym_select($this->catvalues, 'plugin_category', intval($filter_cat), 'class="plugin_category_select"', 'value', 'text');
    }

    protected function handleChildrenCategories($parent_id, $level = 0)
    {
        if (empty($this->cats[$parent_id])) return;

        foreach ($this->cats[$parent_id] as $cat) {
            $this->catvalues[] = acym_selectOption($cat->id, str_repeat(' - - ', $level).$cat->title);
            $this->handleChildrenCategories($cat->id, $level + 1);
        }
    }

    protected function autoCampaignOptions(&$options)
    {
        $options[] = [
            'title' => 'ACYM_ONLY_NEWLY_CREATED',
            'type' => 'boolean',
            'name' => 'onlynew',
            'default' => true,
            'tooltip' => 'ACYM_ONLY_NEWLY_CREATED_DESC',
            'section' => 'ACYM_AUTO_CAMPAIGNS_OPTIONS',
        ];
        $options[] = [
            'title' => 'ACYM_MIN_NB_ELEMENTS',
            'type' => 'number',
            'name' => 'min',
            'default' => 0,
            'tooltip' => 'ACYM_MIN_NB_ELEMENTS_DESC',
            'section' => 'ACYM_AUTO_CAMPAIGNS_OPTIONS',
        ];
    }

    protected function getElementsListing($options)
    {
        $listing = '<div id="plugin_listing" class="acym__popup__listing">';
        $listing .= '<input type="hidden" name="plugin" value="'.acym_escape(get_class($this)).'" />';

        $listing .= '<div class="cell grid-x hide-for-small-only plugin_listing_headers">';
        foreach ($options['header'] as $oneColumn) {
            $class = empty($oneColumn['class']) ? '' : ' '.$oneColumn['class'];
            $listing .= '<div class="cell medium-'.$oneColumn['size'].$class.'">'.acym_translation($oneColumn['label']).'</div>';
        }
        $listing .= '</div>';

        if (empty($options['rows'])) {
            $listing .= '<h1 class="cell acym__listing__empty__search__modal text-center">'.acym_translation('ACYM_NO_RESULTS_FOUND').'</h1>';
        } else {
            $selected = explode(',', acym_getVar('string', 'selected', ''));
            if (!empty($this->defaultValues->id)) $selected = [$this->defaultValues->id];

            foreach ($options['rows'] as $row) {
                $class = 'cell grid-x acym__listing__row__popup';
                if (in_array($row->{$options['id']}, $selected)) $class .= ' selected_row';

                $listing .= '<div class="'.$class.'" data-id="'.intval($row->{$options['id']}).'" onclick="applyContent'.acym_escape($this->name).'('.intval($row->{$options['id']}).', this);">';

                foreach ($options['header'] as $column => $oneColumn) {
                    $value = $row->$column;

                    if (!empty($oneColumn['type']) && $oneColumn['type'] == 'date') {
                        if (!is_numeric($value)) $value = strtotime($value);
                        $tooltip = acym_date($value, acym_translation('ACYM_DATE_FORMAT_LC2'));
                        $value = acym_tooltip(acym_date($value, acym_translation('ACYM_DATE_FORMAT_LC5')), $tooltip);
                    }

                    $class = empty($oneColumn['class']) ? '' : ' '.$oneColumn['class'];
                    $listing .= '<div class="cell medium-'.$oneColumn['size'].$class.'">'.$value.'</div>';
                }

                $listing .= '</div>';
            }
        }

        $pagination = acym_get('helper.pagination');
        $pagination->setStatus($this->pageInfo->total, $this->pageInfo->page, $this->pageInfo->limit);
        $listing .= $pagination->displayAjax();
        $listing .= '</div>';

        return $listing;
    }

    protected function getCategoryListing()
    {
        $listing = '';
        if (empty($this->catvalues)) return $listing;

        $listing .= '<div class="acym__popup__listing padding-0">';
        $selected = [];
        if (!empty($this->defaultValues->id) && strpos($this->defaultValues->id, '-')) {
            $selected = explode('-', $this->defaultValues->id);
        }
        foreach ($this->catvalues as $oneCat) {
            if (empty($oneCat->value)) continue;

            $class = 'cell grid-x acym__listing__row acym__listing__row__popup';
            if (in_array($oneCat->value, $selected)) $class .= ' selected_row';
            $listing .= '<div class="'.$class.'" data-id="'.intval($oneCat->value).'" onclick="applyContentauto'.acym_escape($this->name).'('.intval($oneCat->value).', this);">
                        <div class="cell medium-5">'.acym_escape($oneCat->text).'</div>
                    </div>';
        }
        $listing .= '</div>';

        return $listing;
    }

    protected function replaceMultiple(&$email)
    {
        $this->generateByCategory($email);
        if (empty($this->tags)) return;
        $this->pluginHelper->replaceTags($email, $this->tags, true);
    }

    protected function handleOrderBy(&$query, $parameter, $table = null)
    {
        if (empty($parameter->order)) return;

        $ordering = explode(',', $parameter->order);
        if ($ordering[0] == 'rand') {
            $query .= ' ORDER BY rand()';
        } else {
            $table = null === $table ? '' : $table.'.';
            $query .= ' ORDER BY '.$table.'`'.acym_secureDBColumn(trim($ordering[0])).'` '.acym_secureDBColumn(trim($ordering[1]));
        }
    }

    protected function handleMax(&$query, $parameter)
    {
        if (empty($parameter->max)) $parameter->max = 20;
        $query .= ' LIMIT '.intval($parameter->max);
    }

    protected function getLastGenerated($mailId)
    {
        $campaignClass = acym_get('class.campaign');

        return $campaignClass->getLastGenerated($mailId);
    }

    protected function finalizeCategoryFormat($query, $parameter, $table = null)
    {
        $this->handleOrderBy($query, $parameter, $table);
        $this->handleMax($query, $parameter);

        $elements = acym_loadResultArray($query);

        if (!empty($parameter->min) && count($elements) < $parameter->min) {
            $this->generateCampaignResult->status = false;
            $this->generateCampaignResult->message = acym_translation_sprintf('ACYM_GENERATE_CAMPAIGN_NOT_ENOUGH_CONTENT', $this->pluginDescription->name, count($elements), $parameter->min);
        }

        if (empty($elements)) return '';

        $customLayout = ACYM_CUSTOM_PLUGIN_LAYOUT.$this->name.'_auto.php';
        if (file_exists($customLayout)) {
            ob_start();
            require $customLayout;

            return ob_get_clean();
        }

        $arrayElements = [];
        unset($parameter->id);
        foreach ($elements as $oneElementId) {
            $args = [];
            $args[] = $this->name.':'.$oneElementId;
            foreach ($parameter as $oneParam => $val) {
                if (is_bool($val)) {
                    $args[] = $oneParam;
                } else {
                    $args[] = $oneParam.':'.$val;
                }
            }
            $arrayElements[] = '{'.implode('|', $args).'}';
        }

        return $this->pluginHelper->getFormattedResult($arrayElements, $parameter);
    }

    protected function getSelectedArea($parameter)
    {
        $allcats = explode('-', $parameter->id);
        $selectedArea = [];
        foreach ($allcats as $oneCat) {
            if (empty($oneCat)) continue;
            $selectedArea[] = intval($oneCat);
        }

        return $selectedArea;
    }

    protected function replaceOne(&$email)
    {
        $tags = $this->pluginHelper->extractTags($email, $this->name);
        if (empty($tags)) return;

        if (false === $this->loadLibraries($email)) return;

        $tagsReplaced = [];
        foreach ($tags as $i => $oneTag) {
            if (isset($tagsReplaced[$i])) continue;
            $tagsReplaced[$i] = $this->replaceIndividualContent($oneTag, $email);
        }

        $this->pluginHelper->replaceTags($email, $tagsReplaced, true);
    }

    protected function loadLibraries($email)
    {
        return true;
    }

    protected function initIndividualContent(&$tag, $query)
    {
        $element = acym_loadObject($query);

        if (empty($element)) {
            if (acym_isAdmin()) {
                acym_enqueueMessage(acym_translation_sprintf('ACYM_CONTENT_NOT_FOUND', $tag->id), 'notice');
            }

            return false;
        }

        if (empty($tag->display)) {
            $tag->display = [];
        } else {
            $tag->display = explode(',', $tag->display);
        }

        return $element;
    }

    protected function getCustomLayoutVars($element)
    {
        $varFields = [];
        $varFields['{picthtml}'] = '';
        foreach ($element as $fieldName => $oneField) {
            $varFields['{'.$fieldName.'}'] = $oneField;
        }

        return $varFields;
    }

    protected function finalizeElementFormat($result, $options, $data)
    {
        $customLayoutPath = ACYM_CUSTOM_PLUGIN_LAYOUT.$this->name.'.php';
        if (file_exists($customLayoutPath)) {
            ob_start();
            require $customLayoutPath;
            $result = ob_get_clean();
            $result = str_replace(array_keys($data), $data, $result);
        }

        return $this->pluginHelper->managePicts($options, $result);
    }

    protected function filtersFromConditions(&$filters)
    {
        $newFilters = [];

        $this->onAcymDeclareConditions($newFilters);
        foreach ($newFilters as $oneType) {
            foreach ($oneType as $oneFilterName => $oneFilter) {
                if (!empty($oneFilter->option)) $oneFilter->option = str_replace(['acym_condition', '[conditions]'], ['acym_action', '[filters]'], $oneFilter->option);
                $filters[$oneFilterName] = $oneFilter;
            }
        }
    }
}

