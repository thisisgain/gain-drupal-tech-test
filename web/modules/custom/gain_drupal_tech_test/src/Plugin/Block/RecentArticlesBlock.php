<?php

namespace Drupal\gain_drupal_tech_test\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Form\FormStateInterface;

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

    public function defaultConfiguration() {
    return [
      'items_count' => 5,
    ];
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


  public function blockForm($form, FormStateInterface $form_state) {
    $form['items_count'] = [
      //'#type' => 'number',
      '#type' => 'textfield',
      '#title' => $this->t('Number of items'),
      '#default_value' => $this->configuration['items_count'],
      '#min' => 1,
      '#max' => 50,
    ];

  return $form;
}

public function blockSubmit($form, FormStateInterface $form_state) {
  $this->configuration['items_count'] = $form_state->getValue('items_count');
}
  /**
   * {@inheritdoc}
   *
   * @return array
   *   Renderable array.
   */
  public function build() {
    $node_storage = $this->entityTypeManager->getStorage('node');

    $node_count = $this->configuration['items_count'] ?? 5;

    // BUG 1: Query doesn't filter by published status
    // BUG 2: Query doesn't filter by content type 'article'
    // BUG 3: Wrong sort order (oldest first instead of newest)
    // BUG 4: Hardcoded limit instead of configurable.
    $query = $node_storage->getQuery()
      ->sort('created', 'DESC')
      ->condition('type', 'article')
      ->condition('status', 1)
      ->range(0, $node_count)
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
