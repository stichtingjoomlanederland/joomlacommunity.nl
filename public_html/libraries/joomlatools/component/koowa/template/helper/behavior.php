<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Behavior Template Helper
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Component\Koowa\Template\Helper
 */
class ComKoowaTemplateHelperBehavior extends KTemplateHelperBehavior
{
    /**
     * Loads koowa.js
     *
     * @param array|KObjectConfig $config
     * @return string
     */
    public function koowa($config = array())
    {
        $config = new KObjectConfigJson($config);
        $config->append(array(
            'debug' => JFactory::getApplication()->getCfg('debug')
        ));

        return parent::koowa($config);
    }

    /**
     * Loads Modernizr
     *
     * @param array|KObjectConfig $config
     * @return string
     */
    public function modernizr($config = array())
    {
        $config = new KObjectConfigJson($config);
        $config->append(array(
            'debug' => JFactory::getApplication()->getCfg('debug')
        ));

        return parent::modernizr($config);
    }

    public function modal($config = array())
    {
        $config = new KObjectConfigJson($config);
        $config->append(array(
            'debug' => JFactory::getApplication()->getCfg('debug')
        ));

        return parent::modal($config);
    }

    /**
     * Loads jQuery under a global variable called kQuery.
     *
     * Loads it from Joomla in 3.0+ and our own sources in 2.5. If debug config property is set, an uncompressed
     * version will be included.
     *
     * You can do window.jQuery = window.$ = window.kQuery; to use the default names
     *
     * @param array|KObjectConfig $config
     * @return string
     */
    public function jquery($config = array())
    {
        $config = new KObjectConfigJson($config);
        $config->append(array(
            'debug' => JFactory::getApplication()->getCfg('debug')
        ));

        $html = '';

        if (!static::isLoaded('jquery'))
        {
            JHtml::_('jquery.framework');
            // Can't use JHtml here as it makes a file_exists call on koowa.kquery.js?version
            $path = JURI::root(true).'/media/koowa/framework/js/koowa.kquery.js?'.substr(md5(Koowa::VERSION), 0, 8);
            JFactory::getDocument()->addScript($path);

            static::setLoaded('jquery');
        }

        return $html;
    }

    /**
     * Add Bootstrap JS and CSS a modal box
     *
     * @param array|KObjectConfig $config
     * @return string   The html output
     */
    public function bootstrap($config = array())
    {
        $template = JPATH_THEMES.'/'.JFactory::getApplication()->getTemplate();

        $config = new KObjectConfigJson($config);
        $config->append(array(
            'debug' => JFactory::getApplication()->getCfg('debug'),
            'javascript' => false,
            'css' => file_exists($template.'/enable-koowa-bootstrap.txt')
        ));

        $html = '';

        if ($config->javascript && !static::isLoaded('bootstrap-javascript'))
        {
            $html .= $this->jquery($config);

            JHtml::_('bootstrap.framework');

            static::setLoaded('bootstrap-javascript');

            $config->javascript = false;
        }

        $html .= parent::bootstrap($config);

        return $html;
    }

    /**
     * Keeps session alive
     *
     * @param array|KObjectConfig $config
     * @return string
     */
    public function keepalive($config = array())
    {
        JHtml::_('behavior.keepalive');
        return '';
    }

    /**
     * Loads the Forms.Validator class and connects it to Koowa.Controller.Form
     *
     * @param array|KObjectConfig $config
     * @return string   The html output
     */
    public function validator($config = array())
    {
        $config = new KObjectConfigJson($config);
        $config->append(array(
            'debug' => JFactory::getApplication()->getCfg('debug')
        ));

        return parent::validator($config);
    }

    /**
     * Loads the select2 behavior and attaches it to a specified element
     *
     * @see    http://ivaynberg.github.io/select2/select-2.1.html
     *
     * @param  array|KObjectConfig $config
     * @return string   The html output
     */
    public function select2($config = array())
    {
        $config = new KObjectConfigJson($config);
        $config->append(array(
            'debug' => JFactory::getApplication()->getCfg('debug')
        ));

        return parent::select2($config);
    }

    /**
     * Loads the Koowa customized jQtree behavior and renders a sidebar-nav list useful in split views
     *
     * @see    http://mbraak.github.io/jqTree/
     *
     * @note   If no 'element' option is passed, then only assets will be loaded.
     *
     * @param  array|KObjectConfig $config
     * @throws InvalidArgumentException
     * @return string    The html output
     */
    public function tree($config = array())
    {
        $config = new KObjectConfigJson($config);
        $config->append(array(
            'debug' => JFactory::getApplication()->getCfg('debug')
        ));

        return parent::tree($config);
    }

    /**
     * Loads the calendar behavior and attaches it to a specified element
     *
     * @param array|KObjectConfig $config
     * @return string   The html output
     */
    public function calendar($config = array())
    {
        $config = new KObjectConfigJson($config);

        if ($config->filter) {
            $config->offset = strtoupper($config->filter); // @TODO Backwards compatibility
        }

        $config->append(array(
            'debug'          => JFactory::getApplication()->getCfg('debug'),
            'server_offset'  => JFactory::getConfig()->get('offset'),
            'first_week_day' => JFactory::getLanguage()->getFirstDay(),
            'options'        => array(
                'language' => JFactory::getLanguage()->getTag(),
            )
        ));

        return parent::calendar($config);
    }
}
