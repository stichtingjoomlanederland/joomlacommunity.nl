<?php
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

/**
 * Filterable Controller Behavior
 *
 * Modifies the request based on its current state, active page parameters and format.
 */
class ComDocmanControllerBehaviorFilterable extends KControllerBehaviorAbstract
{
    protected $_vars;

    protected $_params;

    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->_vars   = $config->vars;
        $this->_params = JFactory::getApplication()->getMenu()->getActive()->params;
    }

    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'priority' => KCommandHandlerAbstract::PRIORITY_LOW
        ));

        if (empty($config->vars)) {
            $config->vars = array('sort');
        }

        parent::_initialize($config);
    }

    protected function _beforeRender(KControllerContextInterface $context)
    {
        if ($context->getRequest()->isGet())
        {
            $controller = $context->getSubject();
            $request    = $controller->getRequest();

            foreach ($this->_vars as $name => $param)
            {
                if (is_numeric($name)) $name = $param;

                $method = '_set' . ucfirst($name);

                if (method_exists($this, $method)) {
                    call_user_func_array(array($this, $method), array($request, $param));
                } else {
                    $request->query->set($name, $this->_params->get($param));
                }
            }

            if ($request->getFormat() == 'rss')
            {
                $query  = $request->getQuery();
                $states = array('limit' => 20, 'offset' => 0, 'sort' => 'created_on', 'direction' => 'desc');

                foreach ($states as $name => $value)
                {
                    $query->set($name, $value);

                    // Set as internal.
                    $controller->getModel()->getState()->setProperty($name, 'internal', true);
                }
            }

            // Update the model state.
            $controller->getModel()->setState($request->getQuery()->toArray());
        }
    }

    protected function _setSort_categories(KControllerRequestInterface $request, $param)
    {
        $value = $this->_params->get($param);

        if (substr($value, 0, 8) === 'reverse_')
        {
            $sort      = substr($value, 8);
            $direction = 'desc';
        }
        else
        {
            $sort      = $value;
            $direction = 'asc';
        }

        $this->_params->set($param, $sort);
        $this->_params->set('direction_categories', $direction);
    }

    /**
     * Sort setter.
     *
     * @param KControllerRequestInterface $request  The controller request object.
     * @param string                      $param    The page parameter name containing the selected value.
     */
    protected function _setSort(KControllerRequestInterface $request, $param)
    {
        $query = $request->getQuery();
        $value = $this->_params->get($param);

        if (substr($value, 0, 8) === 'reverse_')
        {
            $sort      = substr($value, 8);
            $direction = 'desc';
        }
        else
        {
            $sort      = $value;
            $direction = 'asc';
        }

        // Page settings are considered as default.
        $this->getModel()->getState()->setProperty('sort', 'default', $sort)
             ->setProperty('direction', 'default', $direction);

        // Set from page settings if not set.
        $query->sort      = $query->sort ? $query->sort : $sort;
        $query->direction = $query->direction ? $query->direction : $direction;

        // Disallow arbitrary sorting.
        if (!in_array($query->sort, array('hits', 'title', 'created_on', 'touched_on'))) {
            $query->sort = $sort;
        }

        if (!$this->_params->get('show_document_sort_limit'))
        {
            $query->sort      = $sort;
            $query->direction = $direction;

            // Set as internal.
            $this->getModel()->getState()->setProperty('sort', 'internal', true)
                 ->setProperty('direction', 'internal', true);
        }
    }
}