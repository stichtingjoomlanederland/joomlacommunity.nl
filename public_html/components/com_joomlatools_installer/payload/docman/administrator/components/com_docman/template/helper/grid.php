<?php
/**
 * @package    DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComDocmanTemplateHelperGrid extends ComKoowaTemplateHelperGrid
{
    /**
     * Render an state field
     *
     * @param 	array $config An optional array with configuration options
     * @return string Html
     */
    public function state($config = array())
    {
        $config = new KObjectConfigJson($config);
        $config->append(array(
            'entity'  		=> null,
            'field'		=> 'enabled',
            'clickable'  => true
        ));

        if (!($config->entity instanceof ComDocmanModelEntityDocument)) {
            return parent::publish($config);
        }

        $config->append(array(
            'data'		=> array($config->field => $config->entity->{$config->field})
        ));

        $entity     = $config->entity;
        $translator = $this->getObject('translator');

        // Enabled, but pending
        if ($entity->status === 'pending')
        {
            $access = 0;
            $group  = $translator->translate('Pending');
            $date   = $this->getTemplate()->helper('date.humanize', array('date' => $entity->publish_on));
            $tip    = $translator->translate('Will be published {date}, click to unpublish item', array(
                          'date' => $date));
            $class  = 'k-table__item--state-pending';
        }
        // Enabled, but expired
        else if ($entity->status === 'expired')
        {
            $access = 0;
            $group  = $translator->translate('Expired');
            $date   = $this->getTemplate()->helper('date.humanize', array('date' => $entity->unpublish_on));
            $tip    = $translator->translate('Expired {on}, click to unpublish item', array('on' => $date));
            $class  = 'k-table__item--state-expired';
        }
        elseif ($entity->status === 'unpublished')
        {
            $access = 1;
            $group  = $translator->translate('Unpublished');
            $tip    = $translator->translate('Publish item');
            $class  = 'k-table__item--state-unpublished';
        }
        else
        {
            $access = 0;
            $group  = $translator->translate('Published');
            $tip    = $translator->translate('Unpublish item');
            $class  = 'k-table__item--state-published';
        }

        $config->data->{$config->field} = $access;
        $data = str_replace('"', '&quot;', $config->data);

        $html = '<span style="cursor: pointer" class="k-table__item--state '.$class.'" data-k-tooltip=\'{"container":".k-ui-container","delay":{"show":500,"hide":50}}\' data-action="edit" data-data="'.$data.'" data-original-title="'.$tip.'">'.$group.'</span>';
        
        return $html;
    }

    public function document_category($config = array())
    {
        $config = new KObjectConfig($config);

        $translator = $this->getObject('translator');

        $entity = $config->entity;

        $url = $this->getTemplate()->route('view=category&id=' . $entity->docman_category_id);
        $tip = $translator->translate('Edit {title}', array('title' => $entity->category_title));

        return '<a data-k-tooltip=\'{"container":".k-ui-container","delay":{"show":500,"hide":50}}\' data-original-title="'.$tip.'" href="' . $url . '" >' . $this->getTemplate()->escape($entity->category_title) . '</a>';
    }
}
