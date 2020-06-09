<?php
/**
 * @package   akeebabackup
 * @copyright Copyright (c)2006-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Backup\Admin\Helper;

defined('_JEXEC') or die;

use Exception;
use FOF30\Container\Container;
use Joomla\CMS\Document\HtmlDocument;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;

/**
 * JavaScript bundler service for the component's container
 *
 * @since 7.1.0
 */
final class JsBundler
{
	/**
	 * The application container
	 *
	 * @var   Container
	 * @since 7.1.0
	 */
	private $container;

	/**
	 * The JavaScript files to bundle
	 *
	 * @var   array<string>
	 * @since 7.1.0
	 */
	private $files = [];

	/**
	 * The JavaScript scripts to bundle AFTER the files
	 *
	 * @var   array<string>
	 * @since 7.1.0
	 */
	private $scripts = [];

	/**
	 * Absolute filesystem path to the cached media folder directory
	 *
	 * @var   string
	 * @since 7.1.0
	 */
	private $cacheDirectory;

	/**
	 * URL path to the cached media folder directory
	 *
	 * @var   string
	 * @since 7.1.0
	 */
	private $cacheUrlPath;

	private $optimizerEnabled = true;

	/**
	 * JsBundler constructor.
	 *
	 * @param   Container  $container  The component Container object
	 *
	 * @since   7.1.0
	 */
	public function __construct(Container $container)
	{
		// Store a reference to the container
		$this->container = $container;

		// Is the optimizer enabled?
		$this->optimizerEnabled = $this->container->params->get('optimizeJS', 1) == 1;

		if (!$this->optimizerEnabled)
		{
			return;
		}

		// Find the URL path to the site's root
		$siteRootPath = rtrim(Uri::base(true), '/');

		if ($this->container->platform->isBackend())
		{
			// Remove the "administrator" part from the backend relative URL
			$siteRootPath = rtrim(substr($siteRootPath, 0, -13), '/');
		}

		// Set up the cache directory and it corresponding URL
		$this->cacheDirectory = sprintf("%s/media/%s/cachedMedia", JPATH_ROOT, $this->container->componentName);
		$this->cacheUrlPath   = sprintf("%s/media/%s/cachedMedia", $siteRootPath, $this->container->componentName);

		// Make sure the cache directory exists
		if (!@is_dir($this->cacheDirectory))
		{
			\JFolder::create($this->cacheDirectory, 0755);
		}
	}

	/**
	 * Enqueues a JavaScript file for inclusion in the bundle.
	 *
	 * @param   string  $uri  The media URI to add, e.g. media://com_foobar/js/foobar.js
	 *
	 * @since   7.1.0
	 */
	public function addJS($uri)
	{
		if (!$this->optimizerEnabled)
		{
			$this->container->template->addJS($uri, false, false, $this->container->mediaVersion);

			return;
		}

		$file = $this->container->template->parsePath($uri, true);

		if (!is_file($file))
		{
			// TODO Log an error?

			return;
		}

		$this->files[] = $file;
	}

	public function addInlineJS($scriptContent)
	{
		if (!$this->optimizerEnabled)
		{
			$this->container->template->addJSInline($scriptContent);

			return;
		}

		$this->scripts[] = $scriptContent;
	}

	/**
	 * Creates the bundle (if it not exists) and adds it to the application document
	 *
	 * @since   7.1.0
	 */
	public function bundleAndInclude()
	{
		if (!$this->shouldIncludeJavascript())
		{
			return;
		}

		// Get the signature of the JS bundle
		$signature = md5(json_encode($this->files) . '::MEDIAVERSION::' . $this->container->mediaVersion);

		// If the file is cached insert the cached file into the document
		if ($this->isCached($signature))
		{
			$this->includeBundle($signature);

			return;
		}

		// Bundle and cache the files
		if (!$this->bundleFiles($signature))
		{
			$this->includeSeparateFiles();
		}

		// Insert the cached bundle file into the document
		$this->includeBundle($signature);
	}

	/**
	 * Should I even try to include JavaScript files to the output document?
	 *
	 * If we are running under CLI, we can't get the application document or the document is not an HtmlDocument we
	 * should not even bother including JS files.
	 *
	 * @return  bool
	 *
	 * @since   7.1.0
	 */
	private function shouldIncludeJavascript()
	{
		// Is the optimizer disabled?
		if (!$this->optimizerEnabled)
		{
			return false;
		}

		// I can't bundle JS files in a CLI application
		if ($this->container->platform->isCli())
		{
			return false;
		}

		// Make sure we have a JDocument to insert JavaScript into.
		$document = $this->container->platform->getDocument();

		if (is_null($document))
		{
			return false;
		}

		// Double check we have an HTML document in the application
		if (!($document instanceof HtmlDocument))
		{
			return false;
		}

		return true;
	}

	/**
	 * Get the absolute filesystem path for a given bundle
	 *
	 * @param   string  $signature  The bundle signature
	 *
	 * @return  string
	 *
	 * @since   7.1.0
	 */
	private function getBundleFilepath($signature)
	{
		return sprintf("%s/%s.js", $this->cacheDirectory, $signature);
	}

	/**
	 * Do we have a cached file for the given bundle signature?
	 *
	 * @param   string  $signature  The bundle signature to check
	 *
	 * @return  bool  True if the bundle exists and is cached.
	 *
	 * @since   7.1.0
	 */
	private function isCached($signature)
	{
		// Always rebundle when in site development mode
		if (defined('JDEBUG') && JDEBUG)
		{
			return false;
		}

		// If the cache directory doesn't exist we are, by definition, not bundled.
		if (!@is_dir($this->cacheDirectory))
		{
			return false;
		}

		$filePath = $this->getBundleFilepath($signature);

		return @file_exists($filePath) && is_file($filePath);
	}

	/**
	 * Include a JS bundle into the application output document
	 *
	 * @param   string  $signature  The bundle signature to include
	 *
	 * @since   7.1.0
	 */
	private function includeBundle($signature)
	{
		$url          = $this->cacheUrlPath . '/' . basename($this->getBundleFilepath($signature));
		$mediaVersion = $this->container->mediaVersion;

		try
		{
			$app = Factory::getApplication();
		}
		catch (Exception $e)
		{
			// Fallback when all else fails – this should never happen, really!
			echo "<script data-cfasync='false' src='$url?$mediaVersion' type='text/javascript' defer>";

			return;
		}

		$callback = function() use ($app, $url, $mediaVersion) {
			$replacement  = "<script data-cfasync='false' src='$url?$mediaVersion' type='text/javascript' defer></script></body";

			$buffer = $app->getBody();

			$app->setBody(preg_replace('#<\s*/body#', $replacement, $buffer));
		};

		$app->registerEvent('onAfterRender', $callback);
	}

	/**
	 * Include the individual JS files / scripts into the application output document
	 *
	 * @since   7.1.0
	 */
	private function includeSeparateFiles()
	{
		// Find the URL path to the site's root
		$siteRootPath = rtrim(Uri::base(true), '/');

		if ($this->container->platform->isBackend())
		{
			// Remove the "administrator" part from the backend relative URL
			$siteRootPath = rtrim(substr($siteRootPath, 0, -13), '/');
		}

		$siteRootFolder = JPATH_ROOT;
		$srfLen         = strlen($siteRootFolder);

		foreach ($this->files as $file)
		{
			$file = substr($file, $srfLen);
			$url  = $siteRootPath . '/' . ltrim($file, '/');
			$this->includeJSFile($url);
		}

		foreach ($this->scripts as $script)
		{
			$script = <<< JS

;// This comment is intentionally put here to prevent badly written plugins from causing a Javascript error
// due to missing trailing semicolon and/or newline in their code.
$script

JS;

			$this->container->template->addJSInline($script);
		}
	}

	/**
	 * Include a JS file into the application output document
	 *
	 * @param   string  $url      The relative or absolute URL to include
	 * @param   array   $options  Internal Joomla options ('version' is the only meaningful one a.t.m.)
	 * @param   array   $attribs  Attributes for the element
	 *
	 * @since   7.1.0
	 */
	private function includeJSFile($url, $options = [], $attribs = [])
	{
		$document = $this->container->platform->getDocument();

		$options = array_merge([
			'version' => $this->container->mediaVersion,
		], $options);

		$attribs = array_merge($attribs, [
			'defer'        => false,
			'async'        => false,
			'mime'         => 'text/javascript',
			// NB! This is a string on purpose. DO NOT TURN INTO A BOOLEAN.
			'data-cfasync' => 'false',
		]);

		$document->addScript($url, $options, $attribs);
	}

	/**
	 * Bundle all JS files
	 *
	 * @param   string  $signature  The signature we are bundling
	 *
	 * @return  bool  True on success
	 *
	 * @since   7.1.0
	 */
	private function bundleFiles($signature)
	{
		$bundled        = '';
		$siteRootFolder = JPATH_ROOT;
		$srfLen         = strlen($siteRootFolder);

		foreach ($this->files as $file)
		{
			$cleanFile   = substr($file, $srfLen);
			$fileContent = file_get_contents($file);

			if ($fileContent === false)
			{
				continue;
			}

			$bundled .= <<< TXT
// ============================================================================
// $cleanFile
// ============================================================================

$fileContent

TXT;
		}

		foreach ($this->scripts as $script)
		{
			$bundled .= <<< HTML
$script

HTML;

		}

		return file_put_contents($this->getBundleFilepath($signature), $bundled, LOCK_EX) !== false;
	}
}