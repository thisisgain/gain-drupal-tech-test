<?php

namespace Drupal\gain_drupal_tech_test\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'Recent Articles' Block.
 *
 * @Block(
 *   id = "recent_articles_block",
 *   admin_label = @Translation("Recent Articles"),
 *   category = @Translation("GAIN Drupal Tech Test")
 * )
 */
class RecentArticlesBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * Node storage.
   *
   * @var \Drupal\node\NodeStorageInterface
   */
  protected $nodeStorage;

  /**
   * Config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * Constructs a RecentArticlesBlock object.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    EntityTypeManagerInterface $entity_type_manager
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->nodeStorage = $entity_type_manager->getStorage('node');
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {

    $config = \Drupal::config('gain_drupal_tech_test.settings');

    // get limit from config form
    $limit = (int) $config->get('items_to_show');
    $limit = $limit > 0 ? $limit : 5;

    $nids = $this->nodeStorage->getQuery()
      ->condition('status', 1)
      ->condition('type', 'article')
      ->sort('created', 'DESC')
      ->range(0, $limit)
      ->accessCheck(TRUE)
      ->execute();
      

    $nodes = $this->nodeStorage->loadMultiple($nids);

    $items = [];
    foreach ($nodes as $node) {
      $items[] = [
        '#type' => 'link',
        '#title' => $node->label(),
        '#url' => $node->toUrl(),
      ];
    }

    return [
      '#theme' => 'item_list',
      '#title' => $this->t('Recent Articles'),
      '#items' => $items,
      '#cache' => [
        'tags' => ['node_list'],
        'contexts' => ['user.permissions'],
        'max-age' => 0,
      ],
    ];
  }

}