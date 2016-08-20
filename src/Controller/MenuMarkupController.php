<?php

namespace Drupal\menu_markup\Controller;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Config\ConfigFactory;
use Drupal\Core\Entity\Query\QueryFactory;
use Drupal\Component\Render\FormattableMarkup;

/**
 * @file
 * Contains the menu_markup controller.
 */
class MenuMarkupController extends ControllerBase {

  protected $configFactory;
  protected $entityQuery;
  private   $markupOptions;
  private   $storedSetings;
  private   $links;

  /**
   * Constructs a new class instance.
   *
   * @param \Drupal\Core\Config\ConfigFactory $configFactory
   *    Drupal ConfigFactory object.
   * @param \Drupal\Core\Entity\Query\QueryFactory $entityQuery
   *    Drupal QueryFactory object.
   * @param array $links
   *    An array of links as seen in hook_menu_links_discovered_alter()
   */
  public function __construct(ConfigFactory $configFactory, QueryFactory $entityQuery, $links) {

    $this->configFactory = $configFactory;
    $this->entityQuery = $entityQuery;
    $this->links = $links;
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
   * Parse the configuration into something usable - one entry per line.
   *
   *  The configuration lines should be in the following format:
   *
   *  MENU_TITLE|REPLACEMENT_STRING|NODE_TYPE_MACHINE_NAME
   *
   *  Where:
   *
   *  MENU_TITLE = The name of the menu item (Home, News Archive, etc.)
   *  REPLACEMENT_STRING = string containing markup to replace MENU_TITLE with
   *  NODECOUNT = An optional content type machine name used to generate a badge
   */
  public function parseMenuConfig() {

    // Fetch the configuration for the menu_markup module.
    $this->storedSetings = $this->configFactory->get('menu_markup.settings')->get('config');

    $this->markupOptions = array();
    $lines = preg_split('/\r\n|[\r\n]/', $this->storedSetings);

    foreach ($lines as $line) {
      $tmp = explode('|', $line);

      if (count($tmp) >= 2) {
        $this->markupOptions[$tmp[0]]['menuTitle'] = $tmp[1];
        if (isset($tmp[2])) {
          $this->markupOptions[$tmp[0]]['nodeType'] = $tmp[2];
        }
      }
    }

  }

  /**
   * Rebuild the menu links!
   *
   * @return array
   *    Returns the modified array of links.
   */
  public function rebuildMenuLinks() {

    // Now, let's rebuild the menu links.
    foreach ($this->links as $index => $link) {
      if (@array_key_exists($link['title'], $this->markupOptions)) {
        $translatedMenuTitle = $this->t($link['title']);
        $menuTitleStr = (string) $translatedMenuTitle;

        // Do we have a badge count here?
        if (@$this->markupOptions[$link['title']]['nodeType']) {
          $query = $this->entityQuery->get('node')
                      ->condition('type', $this->markupOptions[$link['title']]['nodeType'])
                      ->condition('status', 1);

          $nids = $query->execute();
          $nodeType = count($nids);
        }
        else {
          $nodeType = '';
        }

        // Token replacement.
        $replacementString = $this->markupOptions[$link['title']]['menuTitle'];

        // This is where the magic happens - convert it.
        $this->links[$index]['title'] = new FormattableMarkup($replacementString, array('%title' => $menuTitleStr, '%nodeCount' => $nodeType));
      }
    }

    return $this->links;
  }

  /**
   * Check to see if we have any links.
   *
   * @return bool
   *    Returns true if there are customized links, false if not.
   */
  public function haveLinks() {
    if (isset($this->links) && count($this->links)) {
      return TRUE;
    }
    else {
      return FALSE;
    }
  }

}
