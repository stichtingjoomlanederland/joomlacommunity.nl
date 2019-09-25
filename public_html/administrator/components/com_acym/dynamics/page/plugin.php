<?php
/**
 * @package	AcyMailing for Joomla
 * @version	6.3.0
 * @author	acyba.com
 * @copyright	(C) 2009-2019 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

defined('_JEXEC') or die('Restricted access');
?><?php

class plgAcymPage extends acymPlugin
{
    public function __construct()
    {
        parent::__construct();
        $this->cms = 'WordPress';
        $this->name = 'page';
    }

    public function insertOptions()
    {
        $plugin = new stdClass();
        $plugin->name = acym_translation('ACYM_PAGE');
        $plugin->icon = '<div class="wp-menu-image dashicons-before dashicons-admin-page"></div>';
        $plugin->icontype = 'raw';
        $plugin->plugin = __CLASS__;

        return $plugin;
    }

    public function contentPopup($defaultValues = null)
    {
        $this->defaultValues = $defaultValues;

        $displayOptions = [
            [
                'title' => 'ACYM_DISPLAY',
                'type' => 'checkbox',
                'name' => 'display',
                'options' => [
                    'title' => ['ACYM_TITLE', true],
                    'image' => ['ACYM_FEATURED_IMAGE', true],
                    'content' => ['ACYM_CONTENT', true],
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

        $zoneContent = $this->getFilteringZone(false).$this->prepareListing();
        echo $this->displaySelectionZone($zoneContent);
        echo $this->acympluginHelper->displayOptions($displayOptions, $this->name, 'individual', $this->defaultValues);
    }

    public function prepareListing()
    {
        $this->querySelect = 'SELECT page.ID, page.post_title, page.post_date, page.post_content ';
        $this->query = 'FROM #__posts AS page ';
        $this->filters = [];
        $this->filters[] = 'page.post_type = "page"';
        $this->filters[] = 'page.post_status = "publish"';
        $this->searchFields = ['page.ID', 'page.post_title'];
        $this->pageInfo->order = 'page.ID';
        $this->elementIdTable = 'page';
        $this->elementIdColumn = 'ID';

        parent::prepareListing();

        $rows = $this->getElements();
        foreach ($rows as $i => $row) {
            if (str_replace(['wp:core-embed', 'wp:shortcode'], '', $row->post_content) !== $row->post_content) {
                $rows[$i]->post_title = acym_tooltip('<i class="fa fa-warning"></i>', acym_translation('ACYM_SPECIAL_CONTENT_WARNING')).$rows[$i]->post_title;
            }
        }

        $listingOptions = [
            'header' => [
                'post_title' => [
                    'label' => 'ACYM_TITLE',
                    'size' => '7',
                ],
                'post_date' => [
                    'label' => 'ACYM_DATE_CREATED',
                    'size' => '4',
                    'type' => 'date',
                ],
                'ID' => [
                    'label' => 'ACYM_ID',
                    'size' => '1',
                    'class' => 'text-center',
                ],
            ],
            'id' => 'ID',
            'rows' => $rows,
        ];

        return $this->getElementsListing($listingOptions);
    }

    public function replaceContent(&$email)
    {
        $this->_replaceOne($email);
    }

    public function _replaceContent($tag, &$email)
    {
        $query = 'SELECT page.*
                    FROM #__posts AS page
                    WHERE page.post_type = "page" 
                        AND page.post_status = "publish"
                        AND page.ID = '.intval($tag->id);

        $element = acym_loadObject($query);

        if (empty($element)) {
            if (acym_isAdmin()) {
                acym_enqueueMessage('The page "'.$tag->id.'" could not be found', 'notice');
            }

            return '';
        }

        if (empty($tag->display)) {
            $tag->display = [];
        } else {
            $tag->display = explode(',', $tag->display);
        }

        $varFields = [];
        foreach ($element as $fieldName => $oneField) {
            $varFields['{'.$fieldName.'}'] = $oneField;
        }

        $link = $element->guid;
        $varFields['{link}'] = $link;

        $title = '';
        if (in_array('title', $tag->display)) $title = $element->post_title;

        $afterTitle = '';

        $imagePath = '';
        if (in_array('image', $tag->display)) {
            $imageId = get_post_thumbnail_id($tag->id);
            if (!empty($imageId)) {
                $imagePath = get_the_post_thumbnail_url($tag->id);
            }
        }

        $contentText = '';
        if (in_array('content', $tag->display)) $contentText .= $element->post_content;

        $customFields = [];

        $format = new stdClass();
        $format->tag = $tag;
        $format->title = $title;
        $format->afterTitle = $afterTitle;
        $format->afterArticle = '';
        $format->imagePath = $imagePath;
        $format->description = $contentText;
        $format->link = empty($tag->clickable) ? '' : $link;
        $format->cols = empty($tag->nbcols) ? 1 : intval($tag->nbcols);
        $format->customFields = $customFields;
        $result = '<div class="acymailing_content">'.$this->acympluginHelper->getStandardDisplay($format).'</div>';

        return $this->finalizeElementFormat($this->name, $result, $tag, $varFields);
    }
}

