<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

class RSFormProTar
{
	// Holds the path to the archive.
	protected $path;
	
	// File pointer.
	protected $fh;
	
	public function __construct($path) {
		$this->path = $path;
		
		if (!file_exists($this->path) && file_put_contents($this->path, '') === false) {
			throw new Exception(sprintf('Could not create TAR archive "%s"!', $this->path));
		}
		
		// Open the archive for appending - binary mode.
		$this->fh = @fopen($this->path, 'r+b');
		
		if (!$this->fh) {
			throw new Exception(sprintf('Could not open TAR archive "%s" for appending!', $this->path));
		}
		
		// Always appending...
		$this->seek(0, SEEK_END);
	}
	
	// Closes the archive.
	public function close() {
		return fclose($this->fh);
	}
	
	// Seek to a certain position.
	public function seek($offset, $whence = SEEK_SET) {
		return fseek($this->fh, $offset, $whence);
	}
	
	// Tell the current position.
	public function tell() {
		return ftell($this->fh);
	}
	
	// Write to the archive.
	protected function write($string) {
		return fwrite($this->fh, $string);
	}
	
	// Read from file.
	protected function read($size) {
		return fread($this->fh, $size);
	}
	
	// Add a string in the archive.
	public function add($string) {
		// Write the actual data.
		return $this->write($string);
	}
	
	// Adds padding if size doesn't split in an exact number of 512 blocks.
	public function addPadding($size) {
		$padding = $size % 512 ? (512 - $size % 512) : 0;
		$this->write(pack(sprintf('a%d', $padding), ''));
	}
	
	// Adds an empty header that can be populated later on.
	public function addEmptyHeader() {
		return $this->write(pack('a512', ''));
	}
	
	// Adds the file header
	public function addHeader($size, $name) {
		$size = decoct($size);
		
		$info	= stat($this->path);
		$uid 	= sprintf('%6s ', decoct($info[4]));
		$gid 	= sprintf('%6s ', decoct($info[5]));
		$mode   = sprintf('%6s ', decoct(fileperms($this->path)));
		$mtime  = sprintf('%11s', decoct(filemtime($this->path)));
		
		// Compute the "before" & "after" checksum chunks.
		$before	= pack('a100a8a8a8a12A12', $name, $mode, $uid, $gid, $size, $mtime);
		$after	= pack('a1a100a6a2a32a32a8a8a155a12', '', '', '', '', '', '', '', '', '', '');

		// Compute checksum
		$checksum = 0;
		for ($i = 0; $i < 148; $i++) {
			$checksum += ord(substr($before, $i, 1));
		}
		for ($i = 148; $i < 156; $i++) {
			$checksum += ord(' ');
		}
		for ($i = 156, $j = 0; $i < 512; $i++, $j++) {
			$checksum += ord(substr($after, $j, 1));
		}
		$checksum = sprintf('%6s ', decoct($checksum));
		$checksum = pack('a8', $checksum);
		
		$this->write($before.$checksum.$after);
	}
	
	// Adds the final footer to the TAR archive
	public function addFooter() {
		return $this->write(pack('a1024', ''));
	}
	
	// Compress the TAR using GZIP compression.
	public function compress($seek = 0) {
		if (!function_exists('gzopen') || !is_callable('gzopen')) {
			throw new Exception('The gzopen() function is missing from your PHP installation.');
		}
		
		if (!function_exists('gzwrite') || !is_callable('gzwrite')) {
			throw new Exception('The gzwrite() function is missing from your PHP installation.');
		}
		
		if (!function_exists('gzclose') || !is_callable('gzclose')) {
			throw new Exception('The gzclose() function is missing from your PHP installation.');
		}
		
		$gzip 	= substr($this->path, 0, -3).'tgz';
		$gz 	= @gzopen($gzip, 'a1');
		
		if (!$gz) {
			throw new Exception(sprintf('Could not create compressed archive %s', $gzip));
		}
		
		// Seek to requested position
		$this->seek($seek);
		
		// Read chunk
		$data = $this->read($this->getChunkSize());
		
		// Remember our position
		$position = $this->tell();
		
		// Write data
		if ($data !== false) {
			gzwrite($gz, $data);
		}
		
		if (feof($this->fh)) {
			$position = 0;
		}
		
		// Close
		gzclose($gz);
		
		return $position;
	}
	
	// Gets the chunk size (used for compression)
	public function getChunkSize() {
		return 1024*1024;
	}
	
	// Gets current archive size.
	public function getSize() {
		return filesize($this->path);
	}
}