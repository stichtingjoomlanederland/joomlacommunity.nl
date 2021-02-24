<?php
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComDocmanModelEntityRemote extends KModelEntityAbstract
{
    public function getPropertyExtension()
    {
        $path = parse_url($this->path, PHP_URL_PATH);

        return pathinfo($path, PATHINFO_EXTENSION);
    }

    public function getPropertyFilename()
    {
        return pathinfo($this->path, PATHINFO_EXTENSION);
    }

    public function getPropertyScheme()
    {
        return parse_url($this->path, PHP_URL_SCHEME);
    }

    public function getPropertyFullpath()
    {
        return $this->path;
    }
}
