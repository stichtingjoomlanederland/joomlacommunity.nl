<?php
/**
 * @package    DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComDocmanTemplateHelperPaginator extends KTemplateHelperPaginator
{
    public function sort_documents($config = array())
    {
        $config = new KObjectConfig($config);
        $config->append(array(
            'options'   => array(),
            'attribs'   => array(
                'onchange' => 'window.location = this.value;'
            )
        ));

        $translator = $this->getObject('translator');

        $options = array_merge(array(
            $translator->translate('Title Alphabetical')         => array(
                'sort'      => 'title',
                'direction' => 'asc'),
            $translator->translate('Title Reverse Alphabetical') => array(
                'sort'      => 'title',
                'direction' => 'desc'),
            $translator->translate('Most Recent First')          => array(
                'sort'      => 'created_on',
                'direction' => 'desc'),
            $translator->translate('Oldest First')               => array(
                'sort'      => 'created_on',
                'direction' => 'asc'),
            $translator->translate('Most popular first')         => array(
                'sort'      => 'hits',
                'direction' => 'desc'),
            $translator->translate('Last modified first')         => array(
                'sort'      => 'touched_on',
                'direction' => 'desc')
        ), KObjectConfig::unbox($config->options));

        $html     = '';
        $selected = null;
        $state    = $this->getTemplate()->getParameters();
        $current = array(
            'sort'      => $state->sort,
            'direction' => $state->direction,
        );

        $select = array();
        foreach($options as $text => $value)
        {
            $route = $this->getTemplate()->route(http_build_query($value, '', '&'));

            if ($selected === null && $value === $current) {
                $selected = $route;
            }

            $select[] = $this->option(array('label' => $text, 'value' => $route));
        }

        $html .= $this->optionlist(array(
            'options' => $select,
            'name' => '',
            'attribs' => $config->attribs,
            'selected' => $selected
        ));

        return $html;
    }

    /**
     * Render item pagination
     *
     * @param   array|KObjectConfig   $config An optional array with configuration options
     * @return string Html
     * @see     http://developer.yahoo.com/ypatterns/navigation/pagination/
     */
    public function pagination($config = array())
    {
        $config = new KObjectConfig($config);
        $config->append(array(
            'attribs' => array('onchange' => 'this.form.submit();')
        ));

        return parent::pagination($config);
    }
}
