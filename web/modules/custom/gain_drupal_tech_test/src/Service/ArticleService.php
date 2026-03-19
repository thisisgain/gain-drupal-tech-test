<?php

namespace Drupal\gain_drupal_tech_test\Service;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Url;

class ArticleService {

  protected $entityTypeManager;

  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
  }

  public function getLatestArticles($limit = 10) {
    $node_storage = $this->entityTypeManager->getStorage('node');

    $nids = $node_storage->getQuery()
      ->condition('type', 'article')
      ->condition('status', 1)
      ->sort('created', 'DESC')
      ->range(0, $limit)
      ->accessCheck(TRUE)
      ->execute();

    $nodes = $node_storage->loadMultiple($nids);

    $data = [];

    foreach ($nodes as $node) {
      $data[] = [
        'Node ID' => $node->id(),
        'title' => $node->label(),
        'Authored date' => date('d-m-Y', $node->getCreatedTime()),
        'url' => Url::fromRoute('entity.node.canonical', ['node' => $node->id()], ['absolute' => TRUE])->toString(),
      ];
    }

    return $data;
  }

}