<?php
/**
 * @package    DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComDocmanViewJson extends KViewJson
{
    protected static $_public_document_properties = array(
        'id',
        'itemid',
        'uuid',
        'title',
        'slug',
        'category_slug',
        'alias',
        'docman_category_id',
        'description',
        'publish_date',
        'access_title',
        'created_by',
        'created_by_name',
        'image',
        'icon',
        'links',
        'storage_type',
        'storage_path',
    );

    protected static $_public_category_properties = array(
        'id',
        'itemid',
        'uuid',
        'title',
        'slug',
        'path',
        'description',
        'access',
        'access_title',
        'image',
        'icon',
        'links'
    );

    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'behaviors' => array('pageable')
        ));

        parent::_initialize($config);
    }


    /**
     * Returns an array representing the category row
     *
     * @param KModelEntityInterface $category
     *
     * @return array
     */
    protected function _getCategory(KModelEntityInterface $category)
    {
        $data = $category->toArray();
        $data['parameters'] = $category->getParameters()->toArray();
        $data['access_title'] = $category->access_title;
        $data['icon']  = $category->icon;
        $data['image'] = $category->image;
        $data['links'] = array();

        if ($category->image && $category->image_path)
        {
            $data['links']['image'] = array(
                'href' => $category->image_path
            );
        }

        if ($category->icon && $category->icon_path)
        {
            $data['links']['icon'] = array(
                'href' => $category->icon_path
            );
        }

        $this->_filterArray($data, self::$_public_category_properties);

        return $data;
    }

    /**
     * Returns an array representing the document row and with additional properties for download links and thumbnails
     *
     * @param KModelEntityInterface $document Document row
     *
     * @return array
     */
    protected function _getDocument(KModelEntityInterface $document)
    {
        $params = $this->getParameters();
        $this->prepareDocument($document, $params);

        $router = $this->getObject('com://admin/docman.template.helper.route');
        $router->setRouter(array($this, 'getRoute'));

        $category_link = $router->category(array('entity' => $document->category, 'format' => 'json'));

        $data = $document->toArray();

        $data['parameters'] = $document->getParameters()->toArray();
        $data['access_title'] = $document->access_title;
        $data['icon']  = $document->icon;
        $data['image'] = $document->image;
        $data['links'] = array(
            'file' => array(
                'href'  => (string)$document->download_link,
                'type'  => $document->mimetype ?: 'application/octet-stream',
            ),
            'category' => array(
                'href'  => (string)$category_link,
                'type'  => $this->mimetype,
            )
        );

        if ($document->image && $document->image_path)
        {
            $data['links']['image'] = array(
                'href' => (string)$document->image_path
            );
        }

        if ($document->icon && $document->icon_path)
        {
            $data['links']['icon'] = array(
                'href' => (string)$document->icon_path
            );
        }

        $this->_filterArray($data, self::$_public_document_properties);

        $data['category'] = array(
            'id'    => $document->docman_category_id,
            'title' => $document->category_title,
            'slug'  => $document->category_slug
        );

        $data['file'] = array();

        if ($document->storage_type === 'file')
        {
            if ($document->mimetype) {
                $data['file']['type'] = $document->mimetype;
            }

            if ($document->extension) {
                $data['file']['extension'] = $document->extension;
            }

            if ($document->size) {
                $data['file']['size'] = $document->size;
            }
        }

        return $data;
    }

    protected function _getEntityRoute(KModelEntityInterface $entity)
    {
        if ($entity instanceof ComDocmanModelEntityDocument)
        {
            $router = $this->getObject('com://admin/docman.template.helper.route');
            $router->setRouter(array($this, 'getRoute'));

            return $router->document(array('entity' => $entity, 'format' => 'json'));
        }

        return parent::_getEntityRoute($entity);
    }

    /**
     * Takes an array and removes unwanted properties based on the second argument
     *
     * @param array $array              Source array
     * @param array $allowed_properties A list of allowed keys
     */
    protected function _filterArray(array &$array, array $allowed_properties)
    {
        foreach(array_keys($array) as $key)
        {
            if (!in_array($key, $allowed_properties)) {
                unset($array[$key]);
            }
        }
    }
}
