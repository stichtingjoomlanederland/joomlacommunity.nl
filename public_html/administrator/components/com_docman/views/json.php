<?php
/**
 * @package    DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComDocmanViewJson extends KViewJson
{
    /**
     * Returns an array representing the category entity
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
        $router = $this->getObject('com://admin/docman.template.helper.route');
        $router->setRouter(array($this, 'getRoute'));

        // TODO optimize this as it generates a query per document
        $category_link = $router->category(array('entity' => $document->category, 'format' => 'json'));

        $data = $document->toArray();

        $data['parameters'] = $document->getParameters()->toArray();
        $data['access_title'] = $document->access_title;
        $data['icon']  = $document->icon;
        $data['image'] = $document->image;
        $data['links'] = array(
            'file' => array(
                'href'  => $document->download_link,
                'type'  => $this->mimetype,
            ),
            'category' => array(
                'href'  => $category_link,
                'type'  => $this->mimetype,
            )
        );

        if ($document->image && $document->image_path)
        {
            $data['links']['image'] = array(
                'href' => $document->image_path
            );
        }

        if ($document->icon && $document->icon_path)
        {
            $data['links']['icon'] = array(
                'href' => $document->icon_path
            );
        }

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
}
