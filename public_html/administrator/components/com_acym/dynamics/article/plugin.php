<?php
defined('_JEXEC') or die('Restricted access');
?><?php

class plgAcymArticle extends acymPlugin
{
    public function __construct()
    {
        parent::__construct();
        $this->cms = 'Joomla';

        $this->pluginDescription->name = acym_translation('ACYM_ARTICLE');
        $this->pluginDescription->icon = '<i class="cell acymicon-joomla"></i>';
        $this->pluginDescription->icontype = 'raw';
    }

    public function getPossibleIntegrations()
    {
        return $this->pluginDescription;
    }

    public function insertionOptions($defaultValues = null)
    {
        $this->defaultValues = $defaultValues;

        $this->categories = acym_loadObjectList(
            'SELECT id, parent_id, title
            FROM `#__categories` 
            WHERE extension = "com_content"'
        );

        $tabHelper = acym_get('helper.tab');
        $identifier = $this->name;
        $tabHelper->startTab(acym_translation('ACYM_ONE_BY_ONE'), !empty($this->defaultValues->defaultPluginTab) && $identifier === $this->defaultValues->defaultPluginTab);

        $displayOptions = [
            [
                'title' => 'ACYM_DISPLAY',
                'type' => 'checkbox',
                'name' => 'display',
                'options' => [
                    'title' => ['ACYM_TITLE', true],
                    'content' => ['ACYM_CONTENT', true],
                    'cat' => ['ACYM_CATEGORY', false],
                    'readmore' => ['ACYM_READ_MORE', false],
                ],
            ],
            [
                'title' => 'ACYM_CLICKABLE_TITLE',
                'type' => 'boolean',
                'name' => 'clickable',
                'default' => true,
            ],
            [
                'title' => 'ACYM_TRUNCATE',
                'type' => 'intextfield',
                'isNumber' => 1,
                'name' => 'wrap',
                'text' => 'ACYM_TRUNCATE_AFTER',
                'default' => 0,
            ],
            [
                'title' => 'ACYM_DISPLAY_PICTURES',
                'type' => 'pictures',
                'name' => 'pictures',
            ],
        ];

        $zoneContent = $this->getFilteringZone().$this->prepareListing();
        echo $this->displaySelectionZone($zoneContent);
        echo $this->pluginHelper->displayOptions($displayOptions, $identifier, 'individual', $this->defaultValues);

        $tabHelper->endTab();
        $identifier = 'auto'.$this->name;
        $tabHelper->startTab(acym_translation('ACYM_BY_CATEGORY'), !empty($this->defaultValues->defaultPluginTab) && $identifier === $this->defaultValues->defaultPluginTab);

        $catOptions = [
            [
                'title' => 'ACYM_ORDER_BY',
                'type' => 'select',
                'name' => 'order',
                'options' => [
                    'id' => 'ACYM_ID',
                    'publish_up' => 'ACYM_PUBLISHING_DATE',
                    'modified' => 'ACYM_MODIFICATION_DATE',
                    'title' => 'ACYM_TITLE',
                    'rand' => 'ACYM_RANDOM',
                ],
            ],
            [
                'title' => 'ACYM_COLUMNS',
                'type' => 'number',
                'name' => 'cols',
                'default' => 1,
                'min' => 1,
                'max' => 10,
            ],
            [
                'title' => 'ACYM_MAX_NB_ELEMENTS',
                'type' => 'number',
                'name' => 'max',
                'default' => 20,
            ],
        ];

        $this->autoCampaignOptions($catOptions);

        $displayOptions = array_merge($displayOptions, $catOptions);

        echo $this->displaySelectionZone($this->getCategoryListing());
        echo $this->pluginHelper->displayOptions($displayOptions, $identifier, 'grouped', $this->defaultValues);

        $tabHelper->endTab();

        $tabHelper->display('plugin');
    }

    public function prepareListing()
    {
        $this->querySelect = 'SELECT element.id, element.title, element.publish_up ';
        $this->query = 'FROM #__content AS element ';
        $this->filters = [];
        $this->filters[] = 'element.state = 1';
        $this->searchFields = ['element.id', 'element.title'];
        $this->pageInfo->order = 'element.id';
        $this->elementIdTable = 'element';
        $this->elementIdColumn = 'id';

        parent::prepareListing();

        if (!empty($this->pageInfo->filter_cat)) {
            $this->filters[] = 'element.catid = '.intval($this->pageInfo->filter_cat);
        }

        $listingOptions = [
            'header' => [
                'title' => [
                    'label' => 'ACYM_TITLE',
                    'size' => '7',
                ],
                'publish_up' => [
                    'label' => 'ACYM_PUBLISHING_DATE',
                    'size' => '4',
                    'type' => 'date',
                ],
                'id' => [
                    'label' => 'ACYM_ID',
                    'size' => '1',
                    'class' => 'text-center',
                ],
            ],
            'id' => 'id',
            'rows' => $this->getElements(),
        ];

        return $this->getElementsListing($listingOptions);
    }

    public function replaceContent(&$email)
    {
        $this->replaceMultiple($email);
        $this->replaceOne($email);
    }

    protected function loadLibraries($email)
    {
        require_once JPATH_SITE.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'route.php';

        return true;
    }

    public function generateByCategory(&$email)
    {
        $tags = $this->pluginHelper->extractTags($email, 'auto'.$this->name);
        $this->tags = [];
        $time = time();

        if (empty($tags)) return $this->generateCampaignResult;

        foreach ($tags as $oneTag => $parameter) {
            if (isset($this->tags[$oneTag])) continue;

            $query = 'SELECT DISTINCT element.`id` FROM #__content AS element ';

            $where = [];

            $selectedArea = $this->getSelectedArea($parameter);
            if (!empty($selectedArea)) {
                $where[] = 'element.catid IN ('.implode(',', $selectedArea).')';
            }

            $where[] = 'element.state = 1';
            $where[] = '`publish_up` < '.acym_escapeDB(date('Y-m-d H:i:s', $time - date('Z')));
            $where[] = '`publish_down` > '.acym_escapeDB(date('Y-m-d H:i:s', $time - date('Z'))).' OR `publish_down` = 0';

            if (!empty($parameter->onlynew)) {
                $lastGenerated = $this->getLastGenerated($email->id);
                if (!empty($lastGenerated)) {
                    $where[] = 'element.publish_up > '.acym_escapeDB(acym_date($lastGenerated, 'Y-m-d H:i:s', false));
                }
            }

            $query .= ' WHERE ('.implode(') AND (', $where).')';

            $this->tags[$oneTag] = $this->finalizeCategoryFormat($query, $parameter, 'element');
        }

        return $this->generateCampaignResult;
    }

    public function replaceIndividualContent($tag)
    {
        $query = 'SELECT element.*
                    FROM #__content AS element
                    WHERE element.state = 1
                        AND element.id = '.intval($tag->id);

        $element = $this->initIndividualContent($tag, $query);

        if (empty($element)) return '';

        $varFields = $this->getCustomLayoutVars($element);

        $completeId = $element->id;
        if (!empty($element->alias)) $completeId .= ':'.$element->alias;

        $link = ContentHelperRoute::getArticleRoute($completeId, $element->catid, $element->language);
        $link = acym_frontendLink($link, false);
        $varFields['{link}'] = $link;

        $title = '';
        if (in_array('title', $tag->display)) $title = $element->title;

        $afterTitle = '';
        $afterArticle = '';

        $imagePath = '';
        if (!empty($tag->pict) && !empty($element->images)) {
            $images = json_decode($element->images);
            $pictVar = empty($images->image_fulltext) ? 'image_intro' : 'image_fulltext';
            if (!empty($images->$pictVar)) {
                $imagePath = acym_rootURI().$images->$pictVar;
                $varFields['{picthtml}'] = '<img alt="" src="'.acym_escape($imagePath).'" />';
            }
        }

        $contentText = '';
        if (in_array('content', $tag->display)) $contentText .= $element->introtext.$element->fulltext;

        $customFields = [];
        if (in_array('cat', $tag->display)) {
            $category = acym_loadResult('SELECT title FROM #__categories WHERE id = '.intval($element->catid));
            $customFields[] = [
                '<a href="index.php?option=com_content&view=category&id='.$element->catid.'" target="_blank">'.acym_escape($category).'</a>',
                acym_translation('ACYM_CATEGORY'),
            ];
        }

        $readMoreText = empty($tag->readmore) ? acym_translation('ACYM_READ_MORE') : $tag->readmore;
        $varFields['{readmore}'] = '<a class="acymailing_readmore_link" style="text-decoration:none;" target="_blank" href="'.$link.'"><span class="acymailing_readmore">'.acym_escape($readMoreText).'</span></a>';
        if (in_array('readmore', $tag->display)) $afterArticle .= $varFields['{readmore}'];

        $format = new stdClass();
        $format->tag = $tag;
        $format->title = $title;
        $format->afterTitle = $afterTitle;
        $format->afterArticle = $afterArticle;
        $format->imagePath = $imagePath;
        $format->description = $contentText;
        $format->link = empty($tag->clickable) ? '' : $link;
        $format->customFields = $customFields;
        $result = '<div class="acymailing_content">'.$this->pluginHelper->getStandardDisplay($format).'</div>';

        return $this->finalizeElementFormat($result, $tag, $varFields);
    }
}

