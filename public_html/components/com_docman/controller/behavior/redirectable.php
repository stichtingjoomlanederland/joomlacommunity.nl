<?php
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

/**
 * Handle redirects to the remote document locations
 *
 * If the document is being downloaded this method offers support for rewriting dropbox, google drive and google
 * docs urls to the correct download URL
 *
 * @param  KControllerContextInterface $context
 * @return void
 */
class ComDocmanControllerBehaviorRedirectable extends KControllerBehaviorAbstract
{
    protected $_redirect_schemes;

    protected $_redirect_unknown;

    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->_redirect_schemes = KObjectConfig::unbox($config->redirect_schemes);
        $this->_redirect_unknown = $config->redirect_unknown;
    }

    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'redirect_schemes' => array('http', 'https'),
            'redirect_unknown' => true,
        ));

        parent::_initialize($config);
    }

    protected function _beforeRender(KControllerContextInterface $context)
    {
        $result = true;

        $document = $this->getModel()->fetch();

        if ($document instanceof KModelEntityInterface && !$document->isNew())
        {
            if ($document->storage_type == 'remote')
            {
                // Use browser redirection for http/https links or if the path does not have a whitelisted stream wrapper
                $valid_scheme   =  in_array($document->storage->scheme, $this->_redirect_schemes);
                $unknown_scheme =  !array_key_exists($document->storage->scheme, $document->getSchemes());

                if ($valid_scheme || ($this->_redirect_unknown && $unknown_scheme))
                {
                    $context->document = $document;
                    $this->redirect($context);

                    $result = false;
                }
            }
        }
        else throw new KControllerExceptionResourceNotFound('Document not found');

        return $result;
    }

    /**
     * Redirect to the remote url
     *
     * If the file is being downloaded this method offers support for rewriting dropbox, google drive and google
     * docs urls to their correct download URL's.
     *
     * @param  KControllerContextInterface $context
     * @return void
     */
    protected function _actionRedirect(KControllerContextInterface $context)
    {
        $document = $context->document;
        $url      = $document->storage_path;

        if ($document->isHittable()) {
            $document->hit();
        }

        //Rewrite the url for when downloading
        $url = $this->_rewriteUrl($url);

        $context->response->setRedirect($url);
    }


    /**
     * Changes URL to direct to download URL for specific cloud services.
     *
     * @param  string $url The URL of the remote file
     * @return mixed|string
     */
    protected function _rewriteUrl($url)
    {
        if(filter_var($url, FILTER_VALIDATE_URL))
        {
            if($host = parse_url($url, PHP_URL_HOST))
            {
                if (strpos($host, 'dropbox.com') !== false) {
                    return $this->_rewriteDropboxUrl($url);
                }

                if (strpos($host, 'drive.google.com') !== false) {
                    return $this->_rewriteGoogleDriveUrl($url);

                }
                if (strpos($host, 'docs.google.com') !== false) {
                    return $this->_rewriteGoogleDocsUrl($url);
                }
            }
        }

        return $url;
    }

    /**
     * Bypass dropbox preview page
     *
     * @param string $url The file url
     * @return mixed
     */
    protected function _rewriteDropboxUrl($url)
    {
        if (strpos($url, 'dl=') === false) {
            $url .= strpos($url, '?') === false ? '?dl=1' : '&dl=1';
        }

        //Switch out dl=0 for dl=1
        return str_replace('dl=0', 'dl=1', $url);
    }

    /**
     * Bypass preview page for Google Drive Links
     *
     * @param string $url The file url
     * @return string
     */
    protected function _rewriteGoogleDriveUrl($url)
    {
        $url_path  = parse_url($url, PHP_URL_PATH);

        if($id = $this->_getGoogleId($url_path)) {
            $url = 'https://drive.google.com/uc?export=download&id='.$id;
        }

        return $url;
    }

    /**
     * Will alter the url to download automatically rather than show the preview page provided by google
     *
     * @param $init_url
     * @param $url
     * @return string
     */
    protected function _rewriteGoogleDocsUrl($url)
    {
        $url_path  = parse_url($url, PHP_URL_PATH);
        $file_type = null;

        if($id = $this->_getGoogleId($url_path))
        {

            foreach(array('presentation','document','spreadsheets') as $type)
            {
                $segments = explode("/", $url_path);

                // Do not rewrite it for copy URLs
                // See: https://www.makeuseof.com/tag/make-copy-trick-sharing-google-drive-documents/
                if (count($segments) && in_array($segments[count($segments)-1], ['copy', 'preview', 'pubhtml'])) {
                    return $url;
                }

                if(in_array($type, $segments))
                {
                    $file_type = $type;
                    break;
                }
            }

            switch($file_type)
            {
                //Choose doc instead of pdf (assule that the user would have used a pdf file instead of a google doc)
                case 'document':
                    $url = 'https://docs.google.com/document/d/' . $id . '/export?format=doc';
                    break;

                //Choose pptx instead of pdf. See above.
                case 'presentation':
                    $url = 'https://docs.google.com/presentation/d/' . $id . '/export/pptx';
                    break;

                //Choose xlsx instead of pdf. See above.
                case 'spreadsheets':
                    $url = 'https://docs.google.com/spreadsheets/d/' . $id . '/export?format=xlsx';
                    break;
            }
        }

        return $url;
    }

    /**
     * Get a file identifier
     *
     * Function will validate the provide path based on the standard google format
     *
     * @param  string $path An url path
     * @return string|false Returns the file identifier
     */
    protected function _getGoogleId($path)
    {
        if(!preg_match('/[-\w]{25,}/', $path, $matches)) {
            return false;
        }

        return $matches[0];
    }
}
