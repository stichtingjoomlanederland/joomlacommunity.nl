<?php
/**
 * @package    DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComDocmanTemplateHelperListbox extends ComKoowaTemplateHelperListbox
{
    public function tags($config = array())
    {
        $config = new KObjectConfigJson($config);
        $config->append(array(
            'identifier' => 'com://admin/docman.model.tags',
            'component'  => 'docman'
        ));

        return $this->getTemplate()->helper('com:tags.listbox.tags', $config->toArray());
    }

    public function registration_date($config = array())
    {
        $config = new KObjectConfigJson($config);
        $config->append(array(
            'name' => 'filter_range',
            'select2' => true,
            'options' => array(),
            'deselect' => true
        ));

        $translator = $this->getObject('translator');
        $options    = array();
        $values     = array(
            ''                  => $translator->translate('Select'),
            'today'             => $translator->translate('Today'),
            'last-week'         => $translator->translate('In the last week'),
            'last-month'        => $translator->translate('In the last month'),
            'last-three-months' => $translator->translate('In the last 3 months'),
            'last-six-months'   => $translator->translate('In the last 6 months'),
            'last-year'         => $translator->translate('In the last year'),
            'over-a-year'       => $translator->translate('More than a year ago'),
        );

        foreach ($values as $value => $label) {
            $options[] = $this->option(array('label' => $label, 'value' => $value));
        }
        $config->options->append($options);

        return $this->optionlist($config);
    }


    /**
     * Generates an HTML enabled listbox
     *
     * @param   array   $config An optional array with configuration options
     * @return  string  Html
     */
    public function status($config = array())
    {
        $config = new KObjectConfigJson($config);
        $config->append(array(
            'attribs'   => array('class' => 'js-select-status'),
            'deselect'  => true,
            'select2'   => true
        ))->append(array(
            'selected'  => $config->status
        ));

        if (empty($config->status) && $config->enabled === 0) {
            $config->selected = 'unpublished';
        }

        // todo: js
        $translator = $this->getObject('translator');
        $options    = array();

        $options[] = $this->option(array('label' => $translator->translate('Published') , 'value' => 'published' ));
        $options[] = $this->option(array('label' => $translator->translate('Unpublished'), 'value' => 'unpublished' ));
        $options[] = $this->option(array('label' => $translator->translate('Pending'), 'value' => 'pending' ));
        $options[] = $this->option(array('label' => $translator->translate('Expired'), 'value' => 'expired' ));

        //Add the options to the config object
        $config->options = $options;

        $html = $this->optionlist($config);

        $html .= sprintf('<input type="hidden" name="enabled" class="js-hidden-enabled" value="%s" />', $config->enabled);
        $html .= sprintf('<input type="hidden" name="status" class="js-hidden-status" value="%s" />', $config->status);

        $html .= '<script>
        kQuery(function($) {
            var enabled = $(".js-hidden-enabled");
            var status = $(".js-hidden-status");
            $(".js-select-status").on("change", function() {
                var value = $(this).val();

                if (value === "published") {
                    enabled.val("1");
                    status.val("published");
                } else if (value === "unpublished") {
                    enabled.val("0");
                    status.val("");
                } else if (value === "pending") {
                    enabled.val("1");
                    status.val("pending");
                } else if (value === "expired") {
                    enabled.val("1");
                    status.val("expired");
                } else {
                    enabled.val("");
                    status.val("");
                }
            });
        });
        </script>';

        return $html;
    }

    /**
     * Generates an HTML access listbox
     *
     * @param   array $config  An optional array with configuration options
     * @return string Html
     */
    public function access($config = array())
    {
        $translator = $this->getObject('translator');

        $config = new KObjectConfigJson($config);
        $config->append(array(
            'inheritable' => false,
        ))->append(array(
            'deselect_value' => $config->inheritable ? '0' : '',
            'prompt'         => '- '.($config->inheritable ? $translator->translate('Inherit') : $translator->translate('Select')).' -',
        ));

        if ($config->inheritable) {
            $config->deselect = true;
        }

        return parent::access($config);
    }
    /**
     * Rendering a simple day range select list
     */
    public function day_range($config = array())
    {
        $config = new KObjectConfigJson($config);
        $config->append(array(
            'name'   => 'day_range',
            'values' => array(1, 3, 7, 14, 30)
        ))->append(array(
            'selected'  => $config->{$config->name}
        ));

        $html   = array();
        $html[] = '<input type="hidden" id="day_range" name="'.$config->name.'" value="'.$config->selected.'" '. $this->buildAttributes($config->attribs) .'>';
        $html[] = '<div class="k-input-group" data-active-class="k-is-active">';
        $html[] = '<div class="k-input-group__button">';

        foreach($config->values as $value)
        {
            $button = new KObjectConfigJson(array(
                'text'    => $value,
                'value'   => $value,
                'attribs' => array()
            ));

            $button->attribs->type = 'button';
            $button->attribs->class = 'k-js-buttongroup-button k-button k-button--default';

            if($config->selected && $value == $config->selected) {
                $button->attribs->class .= ' k-is-active';
            }

            $button->attribs->value = $button->value;

            $attributes = $this->buildAttributes($button->attribs);

            $html[] = '<button '.$attributes.'>'.$button->text.'</button>';
        }

        $value = in_array($config->selected, KObjectConfig::unbox($config->values)) ? '' : $config->selected;

        $html[] = '</div>';

        $html[] = '<input value="'.$value.'" class="k-form-control k-js-custom-amount" type="text" placeholder="&hellip;" />';

        $html[] = '</div>';

        $html[] = $this->getTemplate()->helper('behavior.buttongroup', array(
            'element' => '#day_range'
        ));

        return implode(PHP_EOL, $html);
    }

    public function folders($config = array())
    {
        $config = new KObjectConfigJson($config);
        $config->append(array(
            'model' => 'com://admin/docman.model.folders',
            'filter' => array('container' => 'docman-files', 'tree' => true),
            'label' => 'path',
            'value' => 'path',
            'name' => 'folder',
            'attribs'	  => array(),
            'deselect'    => true,
            'prompt'      => $this->getObject('translator')->translate('Root folder'),
            'selected'   => $config->{$config->name},
        ));

        try {
            $model   = $this->getObject($config->model);
            $options = array();
            $state   = KObjectConfig::unbox($config->filter);
            $count   = $model->setState($state)->count();
            $offset  = 0;
            $limit   = 50;

            while ($offset < $count)
            {
                $entities = $model->setState($state)->limit($limit)->offset($offset)->fetch();

                foreach ($entities as $entity) {
                    if (substr($entity->path, 0, 3) === 'tmp') {
                        continue;
                    }

                    $options[] = $this->option(array('label' => $entity->{$config->label}, 'value' => $entity->{$config->value}));
                    //$this->_recurseChildFolders($entity, $options, $config);
                }

                $offset += $limit;
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }

        //Add the options to the config object
        $config->options = $options;

        return $this->optionlist($config);
    }

    protected function _recurseChildFolders($item, &$options, $config)
    {
        static $level = 1;

        $level++;
        foreach ($item->getChildren() as $child)
        {
            $options[] = $this->option(array('label' => str_repeat('-', $level).' '.$child->{$config->label}, 'value' => $child->{$config->value}));
            if ($child->hasChildren()) {
                $this->_recurseChildFolders($child, $options, $config);
            }
        }
        $level--;
    }

    public function documents($config = array())
    {
        $config = new KObjectConfigJson($config);
        $config->append(array(
            'model'		=> 'documents',
            'value'		=> 'id',
            'label'		=> 'title'
        ));

        return $this->_render($config);
    }

    public function categories($config = array())
    {
        $config = new KObjectConfigJson($config);
        $config->append(array(
            'model'    => 'categories',
            'value'    => 'id',
            'label'    => 'title',
            'select2'  => true,
            'filter'   => array(
                'sort' => 'custom'
            )
        ));

        return $this->_treelistbox($config);
    }

    public function groups($config = array())
    {
        $config = new KObjectConfigJson($config);
        $config->append(array(
            'select2' => true,
            'options' => array(),
            'prompt' => '- '.$this->getObject('translator')->translate('Select').' -',
        ))->append(array(
            'selected'  => $config->{$config->name}
        ));

        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('a.id AS value, a.title AS text, COUNT(DISTINCT b.id) AS level');
        $query->from($db->quoteName('#__usergroups') . ' AS a');
        $query->join('LEFT', $db->quoteName('#__usergroups') . ' AS b ON a.lft > b.lft AND a.rgt < b.rgt');
        $query->group('a.id, a.title, a.lft, a.rgt');
        $query->order('a.lft ASC');
        $db->setQuery($query);
        $groups = $db->loadObjectList();

        $options = array();

        foreach ($groups as $group)
        {
            $label     = str_repeat('- ', $group->level) . $group->text;
            $options[] = array('value' => $group->value, 'label' => $label);
        }

        $config->options->append($options);

        return $this->optionlist($config);
    }


    public function pages($config = array())
    {
        $config = new KObjectConfigJson($config);
        $config->append(array(
            'select2' => true,
            'options' => array(),
        ));

        $types = KObjectConfig::unbox($config->types);

        if (empty($types)) {
            $types = array('document', 'tree', 'list', 'flat');
        }

        $pages = $this->getObject('com://admin/docman.model.pages')
                    ->language('all')
                    ->view($types)
                    ->access(-1)
                    ->fetch();

        $options = array();
        foreach ($pages as $page)
        {
            if (!isset($options[$page->menutype])) {
                $options[$page->menutype] = array();
            }

            $options[$page->menutype][] = array('value' => $page->id, 'label' => $page->title);
        }

        $config->options->append($options);

        return $this->optionlist($config);
    }

    public function pagecategories($config = array())
    {
        $config = new KObjectConfigJson($config);

        $config->append(array(
            'model'   => 'categories',
            'select2' => true,
            'page'    => 'all',
            'value'   => 'id',
            'label'   => 'title',
            'filter'  => array('page' => $config->page),
            'tree'    => false
        ))->append(array(
            'identifier' => 'com://' . $this->getIdentifier()->domain . '/' .
                $this->getIdentifier()->package . '.model.' . KStringInflector::pluralize($config->model)
        ));

        if ($config->tree)
        {
            $config->indent = '- ';
            $list           = $this->_treelistbox($config);
        }
        else
        {
            $categories = $this->getObject($config->identifier)->setState(KObjectConfig::unbox($config->filter))->fetch();

            $options = array();

            foreach ($categories as $category)
            {
                $options[] = array('value' => $category->{$config->value}, 'label' => $category->{$config->label});
            }

            $config->options = $options;

            $list = parent::optionlist($config);
        }

        return $list;
    }

    public function users2($config = array())
    {
        $config = new KObjectConfigJson($config);

        $config->append(array(
            'select2' => true,
            'name' => 'user-id'
        ));

        $users = $this->getObject('com://admin/docman.model.users')->fetch();

        $options = array();
        foreach($users as $user)
        {
            $options[] = array('value'=>$user->id,'label' => $user->name);
        }
        $config->options = $options;

        return $this->optionlist($config);
    }

    protected function _treelistbox($config = array())
    {
        $config = new KObjectConfigJson($config);
        $config->append(array(
            'name'		    => '',
            'attribs'	    => array(),
            'model'		    => KStringInflector::pluralize($this->getIdentifier()->package),
            'deselect'      => true,
            'prompt'        => '- '.$this->getObject('translator')->translate('Select').' -',
            'unique'	    => false, // Overridden since there can be categories in different levels with the same name
            'check_access'  => false
        ))->append(array(
            'select2'         => false,
            'value'	  => $config->name,
            'selected'   => $config->{$config->name},
            'identifier' => 'com://'.$this->getIdentifier()->domain.'/'.$this->getIdentifier()->package.'.model.'.KStringInflector::pluralize($config->model)
        ))->append(array(
            'label'		=> $config->value,
        ))->append(array(
            'filter' 	=> array('sort' => $config->label),
        ))->append(array(
            'indent'     => '- ',
            'ignore' 	 => array(),
        ));


        //Add the options to the config object

        $ignore = KObjectConfig::unbox($config->ignore);
        $filter = function($category, $config) use ($ignore) {
            if ($config->check_access && $category->isPermissible() && !$category->canPerform('add')) {
                return false;
            }

            if (in_array($category->id, $ignore)) {
                return false;
            }

            return true;
        };

        $self = $this;
        $map = function(&$data, $category, $config) use ($self) {
            $data[$category->id] = $self->option(array(
                'label' => str_repeat($config->indent, $category->level - 1) . $category->{$config->label},
                'value' => $category->{$config->value}
            ));
        };

        $config->options = $this->fetchCategories($config, $map, $filter);

        if ($config->disable_if_empty && !count($config->options)) {
            $config->required = false;
            $config->attribs->disabled = true;
        }

        $html = '';

        if($config->autocomplete) {
            $html .= $this->_autocomplete($config);
        } else {
            $html .= $this->optionlist($config);
        }

        return $html;
    }

    /**
     * Returns an array of categories for listbox
     *
     * Fetches categories in batches of 100 to not load every category row into memory at once
     *
     * @param mixed $config
     * @param callable $filter Filters categories
     * @param callable $map Maps categories into an array
     *
     * @return array $options
     */
    public function fetchCategories($config, $map, $filter = null)
    {
        $config = new KObjectConfig($config);
        $config->append(array(
            'identifier' => 'com://admin/docman.model.categories',
            'state'      => $config->filter
        ));

        $state  = KObjectConfig::unbox($config->state);
        $ignore = KObjectConfig::unbox($config->ignore);

        /** @var $model KModelInterface */
        $model = $this->getObject($config->identifier);
        $model->setState($state);

        $key = $config->identifier.'-'.$config->value.'-'.$config->label.'-'.$config->indent.'-'.$config->document_count;
        $key .= '-'.$this->getObject('user')->getId();

        if (is_array($ignore) && count($ignore)) {
            sort($ignore);
            $key .= md5(implode(',', $ignore));
        }
        $key .= md5(serialize($model->getState()->getValues()));

        $signature = md5($key);
        $cache     = $model->getTable()->getCache();

        if ($config->cache !== false && ($data = $cache->get($signature))) {
            $data = unserialize($data);
        }
        else
        {
            $count  = $model->setState($state)->count();
            $offset = 0;
            $limit  = 100;
            $data   = array();

            while ($offset < $count)
            {
                $entities = $model->setState($state)->limit($limit)->offset($offset)->fetch();

                foreach ($entities as $entity)
                {
                    if (is_callable($filter) && !call_user_func_array($filter, array($entity, $config, $entities))) {
                        continue;
                    }

                    call_user_func_array($map, array(&$data, $entity, $config, $entities));
                }

                $offset += $limit;
            }

            $cache->store(serialize($data), $signature);
        }

        return $data;

    }
}
