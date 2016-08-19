<?php

namespace Drupal\menu_markup\Controller;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Config\ConfigFactory;
use Drupal\Core\Entity\Query\QueryFactory;
use Drupal\Component\Render\FormattableMarkup;

/**
 *
 */
class MenuMarkupController extends ControllerBase {

  protected $configFactory;
  protected $entityQuery;
  private   $_markupOptions;
  private   $_storedSettings;
  private   $_links;

  /**
   *
   */
  public function __construct(ConfigFactory $configFactory, QueryFactory $entityQuery, $links) {

    $this->configFactory = $configFactory;
    $this->entityQuery = $entityQuery;
    $this->_links = $links;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {

    return new static(
        $container->get('config.factory'),
        $container->get('entity.query')
    );
  }

  /**
   * Parse the configuration into something usable - one entry per line, so let's split it up.
   *
   *  The configuration lines should be in the following format:
   *
   *  MENU_TITLE|REPLACEMENT_STRING|NODE_TYPE_MACHINE_NAME
   *
   *  Where:
   *
   *  MENU_TITLE = The name of the menu item (Home, News Archive, etc.)
   *  REPLACEMENT_STRING = The string containing markup to replace the MENU_TITLE with
   *  NODECOUNT = An optional content type machine name which can be used to generate a badge, etc.
   *
   * @return none
   */
  public function parseMenuConfig() {

    // Fetch the configuration for the menu_markup module.
    $this->_storedSettings = $this->configFactory->get('menu_markup.settings')->get('config');

    $this->_markupOptions = array();
    $lines = preg_split('/\r\n|[\r\n]/', $this->_storedSettings);

    foreach ($lines as $line) {
      $tmp = explode('|', $line);

      if (count($tmp) > 0) {
        $this->_markupOptions[$tmp[0]]['menuTitle'] = $tmp[1];
        if (isset($tmp[2])) {
          $this->_markupOptions[$tmp[0]]['nodeType'] = $tmp[2];
        }
      }
    }

  }

  /**
   * Rebuild the menu links!
   *
   * @return array
   */
  public function rebuildMenuLinks() {

    // Now, let's rebuild the menu links.
    foreach ($this->_links as $index => $link) {
      if (@array_key_exists($link['title'], $this->_markupOptions)) {
        $translatedMenuTitle = $this->t($link['title']);
        $menuTitleStr = (string) $translatedMenuTitle;

        // Do we have a badge count here?
        if (@$this->_markupOptions[$link['title']]['nodeType']) {
          $query = $this->entityQuery->get('node')
                      ->condition('type', $this->_markupOptions[$link['title']]['nodeType'])
                      ->condition('status', 1);

          $nids = $query->execute();
          $nodeType = count($nids);
        }
        else {
          $nodeType = '';
        }

        // Token replacement.
        $replacementString = $this->_markupOptions[$link['title']]['menuTitle'];
        $replacementString = preg_replace('/\{\{\s*title\s*\}\}/', $menuTitleStr, $replacementString);
        $replacementString = preg_replace('/\{\{\s*nodeCount\s*\}\}/', $nodeType, $replacementString);

        // This is where the magic happens - convert it!
        $this->_links[$index]['title'] = new FormattableMarkup($replacementString, array());
      }
    }

    return $this->_links;
  }

}
