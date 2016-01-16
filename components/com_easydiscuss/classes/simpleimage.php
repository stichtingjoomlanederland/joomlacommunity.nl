<?php
/**
 * @package		EasyDiscuss
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyDiscuss is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

/*
* File: SimpleImage.php
* Author: Simon Jarvis
* Copyright: 2006 Simon Jarvis
* Date: 08/11/06
* Link: http://www.white-hat-web-design.co.uk/articles/php-image-resizing.php
*
* This program is free software; you can redistribute it and/or
* modify it under the terms of the GNU General Public License
* as published by the Free Software Foundation; either version 2
* of the License, or (at your option) any later version.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details:
* http://www.gnu.org/licenses/gpl.html
*
*/
defined('_JEXEC') or die('Restricted access');

class SimpleImage
{
	var $image;
	var $image_type;

	function getInstance()
	{
		$image = new SimpleImage();
		return $image;
	}

	function load($filename)
	{
		$image_info = getimagesize($filename);

		$this->image_type = $image_info[2];

		if( $this->image_type == IMAGETYPE_JPEG )
		{
			$this->image = imagecreatefromjpeg($filename);
		}
		elseif( $this->image_type == IMAGETYPE_GIF )
		{
			$this->image = imagecreatefromgif($filename);
		}
		elseif( $this->image_type == IMAGETYPE_PNG )
		{
			$this->image = imagecreatefrompng($filename);
		}
	}

	function save($filename, $image_type=IMAGETYPE_JPEG, $compression=75, $permissions=null)
	{
		$contents	= '';

		if( $image_type == IMAGETYPE_JPEG )
		{
			ob_start();
			imagejpeg( $this->image , null , $compression );
			$contents	= ob_get_contents();
			ob_end_clean();
		}
		elseif( $image_type == IMAGETYPE_GIF )
		{
			ob_start();
			imagegif( $this->image , null );
			$contents	= ob_get_contents();
			ob_end_clean();
		}
		elseif( $image_type == IMAGETYPE_PNG )
		{
			ob_start();
			imagepng( $this->image , null );
			$contents	= ob_get_contents();
			ob_end_clean();
		}

		if( !$contents )
		{
			return false;
		}

		jimport( 'joomla.filesystem.file' );
		$status	= JFile::write( $filename , $contents );

		return $status;
	}

	function output($image_type=IMAGETYPE_JPEG)
	{
		if( $image_type == IMAGETYPE_JPEG ) {
			imagejpeg($this->image);
		} elseif( $image_type == IMAGETYPE_GIF ) {
			imagegif($this->image);
		} elseif( $image_type == IMAGETYPE_PNG ) {
			imagepng($this->image);
		}
	}
	function getWidth()
	{
		return imagesx($this->image);
	}

	function getHeight()
	{
		return imagesy($this->image);
	}

	function resizeToHeight($height)
	{
		$ratio = $height / $this->getHeight();
		$width = $this->getWidth() * $ratio;
		$this->resize($width,$height);
	}

	function resizeToWidth($width)
	{
		$ratio = $width / $this->getWidth();
		$height = $this->getHeight() * $ratio;
		$this->resize($width,$height);
	}

	function scale($scale)
	{
		$width = $this->getWidth() * $scale/100;
		$height = $this->getheight() * $scale/100;
		$this->resize($width,$height);
	}

	public function crop( $width , $height , $x , $y )
	{
		if( $this->image_type == IMAGETYPE_JPEG )
		{
			$new_image = imagecreatetruecolor($width, $height);
			imagecopyresampled($new_image, $this->image, 0 , 0 , $x , $y , $width, $height, $width , $height );
		}
		elseif( $this->image_type == IMAGETYPE_GIF )
		{
			$new_image = imagecreatetruecolor($width, $height);
			$transparent = imagecolortransparent($this->image);
			imagepalettecopy( $new_image , $this->image );
			imagefill($new_image, 0, 0, $transparent);
			imagecolortransparent($new_image, $transparent);
			imagetruecolortopalette($new_image, true, 256);
			imagecopyresized($new_image, $this->image, 0, 0, $x, $y, $width , $height , $width , $height );
		}
		elseif( $this->image_type == IMAGETYPE_PNG )
		{
			$new_image = imagecreatetruecolor( $width , $height );
			$transparent	= imagecolorallocatealpha($new_image, 255, 255, 255, 127);
			imagealphablending($new_image , false);
			imagesavealpha($new_image,true);
			imagefilledrectangle($new_image, 0, 0, $width, $height, $transparent);
			imagecopyresampled($new_image , $this->image, 0, 0, $x, $y, $width, $height, $width , $height );
		}
		$this->image = $new_image;
	}

	function square($size)
	{
		$new_image = imagecreatetruecolor($size, $size);

		if($this->getWidth() > $this->getHeight()){
			$this->resizeToHeight($size);

			imagecolortransparent($new_image, imagecolorallocate($new_image, 0, 0, 0));
			imagealphablending($new_image, false);
			imagesavealpha($new_image, true);
			imagecopy($new_image, $this->image, 0, 0, ($this->getWidth() - $size) / 2, 0, $size, $size);
		} else {
			$this->resizeToWidth($size);

			imagecolortransparent($new_image, imagecolorallocate($new_image, 0, 0, 0));
			imagealphablending($new_image, false);
			imagesavealpha($new_image, true);
			imagecopy($new_image, $this->image, 0, 0, 0, ($this->getHeight() - $size) / 2, $size, $size);
		}

		$this->image = $new_image;
	}

	function resize($width, $height)
	{
		if( $this->image_type == IMAGETYPE_JPEG )
		{
			$new_image = imagecreatetruecolor($width, $height);
			imagecopyresampled($new_image, $this->image, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());
		}
		elseif( $this->image_type == IMAGETYPE_GIF )
		{
			$new_image = imagecreatetruecolor($width, $height);
			$transparent = imagecolortransparent($this->image);
			imagepalettecopy( $new_image , $this->image );
			imagefill($new_image, 0, 0, $transparent);
			imagecolortransparent($new_image, $transparent);
			imagetruecolortopalette($new_image, true, 256);
			imagecopyresized($new_image, $this->image, 0, 0, 0, 0, $width , $height , $this->getWidth() , $this->getHeight() );
		}
		elseif( $this->image_type == IMAGETYPE_PNG )
		{
			$new_image = imagecreatetruecolor( $width , $height );
			$transparent	= imagecolorallocatealpha($new_image, 255, 255, 255, 127);
			imagealphablending($new_image , false);
			imagesavealpha($new_image,true);
			imagefilledrectangle($new_image, 0, 0, $width, $height, $transparent);
			imagecopyresampled($new_image , $this->image, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight() );
		}
		$this->image = $new_image;
	}

	function resizeToFit($maxWidth, $maxHeight)
	{
		$sourceWidth	= $this->getWidth();
		$sourceHeight = $this->getHeight();
		$targetWidth	= $sourceWidth;
		$targetHeight = $sourceHeight;

		if (!empty($maxWidth) && $targetWidth > $maxWidth)
		{
			$ratio = $maxWidth / $sourceWidth;

			$targetWidth	= $sourceWidth	* $ratio;
			$targetHeight = $sourceHeight * $ratio;
		}

		if (!empty($maxHeight) && $targetHeight > $maxHeight)
		{
			$ratio = $maxHeight / $sourceHeight;

			$targetWidth	= $sourceWidth	* $ratio;
			$targetHeight = $sourceHeight * $ratio;
		}

		$this->resize($targetWidth, $targetHeight);
	}

	// TODO: Ability to expand original image source if dimension is smaller.
	function resizeToFill($maxWidth, $maxHeight) {

		$sourceWidth   = $this->getWidth();
		$sourceHeight  = $this->getHeight();
		$targetWidth   = $sourceWidth;
		$targetHeight  = $sourceHeight;

		$ratio = $maxWidth / $sourceWidth;
		$targetWidth = $sourceWidth * $ratio;
		$targetHeight = $sourceHeight * $ratio;

		if ($targetHeight < $maxHeight) {
			$ratio = $maxHeight / $sourceHeight;
			$targetWidth = $sourceWidth * $ratio;
			$targetHeight = $sourceHeight * $ratio;
		}

		$targetTop = $maxHeight - $targetHeight;
		$targetLeft = $maxWidth - $targetWidth;
		$targetWidth = ($targetWidth + $targetLeft) / $ratio;
		$targetHeight = ($targetHeight + $targetTop) / $ratio;

		$targetTop = abs($targetTop / 2) / $ratio;
		$targetLeft = abs($targetLeft / 2) / $ratio;

		//rebuilding new image
		$new_image = imagecreatetruecolor($maxWidth, $maxHeight);

		if( $this->image_type == IMAGETYPE_JPEG ) {

			imagecopyresampled($new_image, $this->image, 0, 0, $targetLeft, $targetTop, $maxWidth, $maxHeight, $targetWidth, $targetHeight);

		} elseif( $this->image_type == IMAGETYPE_GIF ) {

			$transparent = imagecolortransparent($this->image);
			imagepalettecopy($this->image, $new_image);
			imagefill($new_image, 0, 0, $transparent);
			imagecolortransparent($new_image, $transparent);
			imagetruecolortopalette($new_image, true, 256);
			imagecopyresized($new_image, $this->image, 0, 0, $targetLeft, $targetTop, $maxWidth , $maxHeight , $targetWidth , $targetHeight );

		} elseif( $this->image_type == IMAGETYPE_PNG ) {

			$transparent = imagecolorallocatealpha($new_image, 255, 255, 255, 127);
			imagealphablending($new_image , false);
			imagesavealpha($new_image,true);
			imagefilledrectangle($new_image, 0, 0, $maxWidth, $maxHeight, $transparent);
			imagecopyresampled($new_image , $this->image, 0, 0, $targetLeft, $targetTop, $maxWidth, $maxHeight, $targetWidth, $targetHeight);
		}

		$this->image = $new_image;
	}

	function cut($x, $y, $width, $height)
	{
		$new_image = imagecreatetruecolor($width, $height);
		imagecopy($new_image, $this->image, 0, 0, $x, $y, $width, $height);
		$this->image = $new_image;
	}

	function maxarea($width, $height = null)
	{
		$height = $height ? $height : $width;

		if($this->getWidth() > $width){
			$this->resizeToWidth($width);
		}
		if($this->getHeight() > $height){
			$this->resizeToheight($height);
		}
	}

	function cutFromCenter($width, $height)
	{
		if($width < $this->getWidth() && $width > $height){
			$this->resizeToWidth($width);
		}
		if($height < $this->getHeight() && $width < $height){
			$this->resizeToHeight($height);
		}

		$x = ($this->getWidth() / 2) - ($width / 2);
		$y = ($this->getHeight() / 2) - ($height / 2);

		return $this->cut($x, $y, $width, $height);
	}

	// Resize the canvas and fill the empty space with a color of your choice
	function maxareafill($width, $height, $red = 0, $green = 0, $blue = 0)
	{
		$this->maxarea($width, $height);
		$new_image = imagecreatetruecolor($width, $height);
		$color_fill = imagecolorallocate($new_image, $red, $green, $blue);
		imagefill($new_image, 0, 0, $color_fill);
		imagecopyresampled($new_image, $this->image, floor(($width - $this->getWidth())/2), floor(($height-$this->getHeight())/2), 0, 0, $this->getWidth(), $this->getHeight(), $this->getWidth(), $this->getHeight());
		$this->image = $new_image;
	}

	function getExtension()
	{
		$type	= '';

		switch( $this->image_type )
		{
			case IMAGETYPE_JPEG:
				$type	= '.jpg';
				break;
			case IMAGETYPE_GIF:
				$type	= '.gif';
				break;
			case IMAGETYPE_PNG:
				$type	= '.png';
				break;
		}
		return $type;
	}

	// Keep Original ratio. Keep Original dimensions if smaller than max settings by Kevin Lankhorst.
	function resizeOriginal($maxWidth, $maxHeight, $avatarWidth, $avatarHeight)
	{
		$sourceWidth	= $this->getWidth();
		$sourceHeight = $this->getHeight();
		$targetWidth	= $sourceWidth;
		$targetHeight = $sourceHeight;
		$targetLeft = 0;
		$targetTop = 0;

		if (!empty($maxWidth) && $targetWidth > $maxWidth) {
			$ratio = $maxWidth / $sourceWidth;

			$targetWidth	= $sourceWidth	* $ratio;
			$targetHeight = $sourceHeight * $ratio;
		}

		if (!empty($maxHeight) && $targetHeight > $maxHeight) {
			$ratio = $maxHeight / $sourceHeight;

			$targetWidth	= $sourceWidth	* $ratio;
			$targetHeight = $sourceHeight * $ratio;
		}

		//Upscale if one of the dimensions gets smaller than the avatar size.
		if ($targetWidth < $avatarWidth) {
			$ratio = $avatarWidth / $targetWidth;

			$targetWidth = $targetWidth * $ratio;
			$targetHeight = $targetHeight * $ratio;

			if ($targetHeight > $maxHeight) {
				$targetTop = ($sourceHeight - ($maxHeight*($sourceHeight / $targetHeight))) / 2;
				$targetHeight = $maxHeight;
				$sourceHeight = $sourceHeight / $ratio;
			}

		}

		if ($targetHeight < $avatarHeight) {
			$ratio = $avatarHeight / $targetHeight;

			$targetWidth = $targetWidth * $ratio;
			$targetHeight = $targetHeight * $ratio;

			if ($targetWidth > $maxWidth) {
				$targetLeft = ($sourceWidth - ($maxWidth*($sourceWidth / $targetWidth))) / 2;
				$targetWidth = $maxWidth;
				$sourceWidth = $sourceWidth / $ratio;
			}

		}

		//rebuilding new image
		$new_image = imagecreatetruecolor($targetWidth, $targetHeight);

		if( $this->image_type == IMAGETYPE_JPEG ) {

			imagecopyresampled($new_image, $this->image, 0, 0, abs($targetLeft), abs($targetTop), abs($targetWidth), abs($targetHeight), abs($sourceWidth), abs($sourceHeight));

		} elseif( $this->image_type == IMAGETYPE_GIF ) {

			$transparent = imagecolortransparent($this->image);
			imagepalettecopy($this->image, $new_image);
			imagefill($new_image, 0, 0, $transparent);
			imagecolortransparent($new_image, $transparent);
			imagetruecolortopalette($new_image, true, 256);
			imagecopyresized($new_image, $this->image, 0, 0, abs($targetLeft), abs($targetTop), abs($targetWidth), abs($targetHeight), abs($sourceWidth), abs($sourceHeight));

		} elseif( $this->image_type == IMAGETYPE_PNG ) {

			$transparent = imagecolorallocatealpha($new_image, 255, 255, 255, 127);
			imagealphablending($new_image , false);
			imagesavealpha($new_image,true);
			imagefilledrectangle($new_image, 0, 0, $targetWidth, $targetHeight, $transparent);
			imagecopyresampled($new_image , $this->image, 0, 0, abs($targetLeft), abs($targetTop), abs($targetWidth), abs($targetHeight), abs($sourceWidth), abs($sourceHeight));
		}

		$this->image = $new_image;
	}
}
