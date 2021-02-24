<?php
/**
 * @package    DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComDocmanTemplateHelperAccess extends KTemplateHelperAbstract
{
    public function access_box($config = array())
    {
        $config = new KObjectConfigJson($config);
        $config->append(array(
        ));

        $entity   = $config->entity;

        $model    = $this->getObject('com://admin/docman.model.viewlevels');
        $entities = $model->fetch();

        $viewlevels = $entities->toArray();

        $default_access = $entities->find((int) (JFactory::getConfig()->get('access') || 1)) ?: $entities->create();
        $type           = KStringInflector::singularize($entity->getIdentifier()->name);

        return $this->getTemplate()->loadFile('com://admin/docman.document.access.html', 'php')
            ->render(array(
                'type'       => $type,
                'entity'     => $entity,
                'viewlevels' => $viewlevels,
                'default_access' => $default_access
            ));
    }

    public function rules($config = array())
    {
        $config = new KObjectConfigJson($config);
        $config->append(array(
            'component' => 'com_docman',
            'section' => 'component',
            'name' => 'rules',
            'asset' => null,
            'asset_id' => 0
        ))->append(array(
            'id' => $config->name
        ));

        // Add editor styles and scripts in JDocument to page when rendering
        $this->getIdentifier('com:koowa.view.page.html')->getConfig()->append(['template_filters' => ['document']]);

        $xml = <<<EOF
<form>
    <fieldset>
        <field name="asset_id" type="hidden" value="{$config->asset_id}" />
        <field name="{$config->name}" type="rules" label="JFIELD_RULES_LABEL"
            translate_label="false" class="inputbox" filter="rules"
            component="{$config->component}" section="{$config->section}" validate="rules"
            id="{$config->id}" 
        />
    </fieldset>
</form>
EOF;

        $form = JForm::getInstance('com_docman.document.acl', $xml);
        $form->setValue('asset_id', null, $config->asset_id);

        $html = '<div class="access-rules">'.$form->getInput('rules').'</div>';

        // Do not allow AJAX saving - it tries to guess the asset name with no way to override
        $html = preg_replace('#onchange="sendPermissions[^"]*"#i', '', $html);

        // Add necessary Bootstrap styles
        $html .= '<ktml:style src="media://com_docman/css/access.css" />';

        return $html;
    }
}
