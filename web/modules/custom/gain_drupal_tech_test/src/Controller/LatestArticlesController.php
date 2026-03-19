<?php

namespace Drupal\gain_drupal_tech_test\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\Core\Url;
use Drupal\gain_drupal_tech_test\Service\ArticleService;


class LatestArticlesController extends ControllerBase {

  protected $articleService;

  public function __construct(ArticleService $article_service) {
    $this->articleService = $article_service;
  }

    public static function create(ContainerInterface $container) {
        return new static(
          $container->get('gain_drupal_tech_test.article_service')
        );
    }

  
  public function getLatestArticles() {
    $articles = $this->articleService->getLatestArticles(10);
    return new JsonResponse($articles);
  }

}

