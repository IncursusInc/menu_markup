<?php

/**
 * @file
 * Contains \Drupal\menu_markup\Controller\MenuMarkupController.
 */

namespace Drupal\menu_markup\Controller;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Config\ConfigFactory;
use Drupal\Component\Render\FormattableMarkup;

class MenuMarkupController extends ControllerBase {

	protected $configFactory;
	public		$menuConfig;
	public		$markupOptions;
	public		$links;

	public function __construct( ConfigFactory $configFactory, $links )
	{
		$this->configFactory = $configFactory;
		$this->links = $links;
	}

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory')
    );
  }

  // Parse the configuration into something usable - one entry per line, so let's split it up
	public function parseMenuConfig() {

  	// Fetch the configuration for the menu_markup module
  	$this->menuConfig = $this->configFactory->get('menu_markup.settings');
  	$storedSettings = $this->menuConfig->get('config');

  	$this->markupOptions = array();
  	$lines = preg_split('/\r\n|[\r\n]/', $storedSettings);

  	foreach ($lines as $line) {
  		$tmp = explode('|', $line);

  		if (count($tmp) > 0) {
  			$this->markupOptions[ $tmp[0] ]['menuTitle'] = $tmp[1];
				if (@isset($tmp[2])) {
  				$this->markupOptions[ $tmp[0] ]['nodeCount'] = $tmp[2];
				}
			}
  	}

	}

	// Rebuild the menu links!
	public function rebuildMenuLinks()
	{
		// Now, let's rebuild the menu links
  	foreach ($this->links as $index => $link) {
  		if (@array_key_exists($link['title'], $this->markupOptions)) {
				$translatedMenuTitle = t($link['title']);
				$menuTitleStr = (string) $translatedMenuTitle;

				// Do we have a badge count here?
				if( @$this->markupOptions[$link['title']]['nodeCount'] ) {
					$query = \Drupal::entityQuery('node')
										->condition('type', $this->markupOptions[$link['title']]['nodeCount'] )
										->condition('status', 1);

					$nids = $query->execute();
					$nodeCount = count($nids);
				} else {
					$nodeCount = '';
				}

				// Token replacement
				$replacementString = $this->markupOptions[$link['title']]['menuTitle'];
				$replacementString = preg_replace('/\{\{\s*title\s*\}\}/', $menuTitleStr, $replacementString);
				$replacementString = preg_replace('/\{\{\s*nodeCount\s*\}\}/', $nodeCount, $replacementString);

				// This is where the magic happens - convert it!
  			$this->links[$index]['title'] = new FormattableMarkup($replacementString, array());
			}
  	}
	
		return $this->links;
	}
}
