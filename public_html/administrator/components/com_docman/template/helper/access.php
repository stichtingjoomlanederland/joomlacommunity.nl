<?php
/**
 * @package    DOCman
 * @copyright   Copyright (C) 2011 - 2014 Timble CVBA (http://www.timble.net)
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

        $access = (int) (JFactory::getConfig()->get('access') || 1);
        $default_access = $entities->find($access) ?: $entities->create();

        return $this->getTemplate()->loadFile('com://admin/docman.document.access.html', 'php')
            ->render(array(
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

        $html = $form->getInput('rules');

        // Do not allow AJAX saving - it tries to guess the asset name with no way to override
        $html = preg_replace('#onchange="sendPermissions[^"]*"#i', '', $html);

        return $html;
    }
}
