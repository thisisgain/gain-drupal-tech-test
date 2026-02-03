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
 *   category = @Translation("GAIN Drupal Tech Test"),
 * )
 */
class RecentArticlesBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs a new RecentArticlesBlock instance.
   *
   * @param array $configuration
   *   The plugin configuration.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entity_type_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   *
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   *   The container.
   * @param array $configuration
   *   Configuration.
   * @param string $plugin_id
   *   Plugin ID.
   * @param mixed $plugin_definition
   *   Plugin definition.
   *
   * @return static
   *   New instance.
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
   *
   * @return array
   *   Renderable array.
   */
  public function build() {
    $node_storage = $this->entityTypeManager->getStorage('node');

    // BUG 1: Query doesn't filter by published status
    // BUG 2: Query doesn't filter by content type 'article'
    // BUG 3: Wrong sort order (oldest first instead of newest)
    // BUG 4: Hardcoded limit instead of configurable.
    $query = $node_storage->getQuery()
      ->sort('created', 'ASC')
      ->range(0, 10)
      ->accessCheck(TRUE);

    $nids = $query->execute();
    $nodes = $node_storage->loadMultiple($nids);

    $items = [];
    foreach ($nodes as $node) {
      $items[] = [
        '#markup' => '<div class="recent-article">' . $node->getTitle() . '</div>',
      ];
    }

    return [
      '#theme' => 'item_list',
      '#items' => $items,
      '#title' => $this->t('Recent Articles'),
      '#cache' => [
        'tags' => ['node_list:article'],
        'max-age' => 3600,
      ],
    ];
  }

}
