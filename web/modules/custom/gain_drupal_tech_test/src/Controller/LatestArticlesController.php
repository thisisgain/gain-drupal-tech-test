<?php

namespace Drupal\gain_drupal_tech_test\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\Core\Url;

/**
 * Returns latest articles JSON response.
 */
class LatestArticlesController extends ControllerBase {

  /**
   * Node storage.
   *
   * @var \Drupal\node\NodeStorageInterface
   */
  protected $nodeStorage;

  /**
   * Constructs controller object.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->nodeStorage = $entity_type_manager->getStorage('node');
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager')
    );
  }

  /**
   * Returns latest 10 published articles.
   */
  public function latestArticles() {

    $nids = $this->nodeStorage->getQuery()
      ->condition('status', 1)
      ->condition('type', 'article')
      ->sort('created', 'DESC')
      ->range(0, 10)
      ->accessCheck(TRUE)
      ->execute();

    $nodes = $this->nodeStorage->loadMultiple($nids);

    $data = [];

    foreach ($nodes as $node) {

      $data[] = [
        'nid' => $node->id(),
        'title' => $node->label(),
        'authored_date' => date('Y-m-d H:i:s', $node->getCreatedTime()),
        'url' => Url::fromRoute(
          'entity.node.canonical',
          ['node' => $node->id()],
          ['absolute' => TRUE]
        )->toString(),
      ];
    }

    return new JsonResponse($data);
  }

}