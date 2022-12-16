<?php

use Drupal\generated_content\Helpers\GeneratedContentHelper;

/**
 * Example of general page properties variations.
 */
function _hcpss_classified_create_node_listing__variations__general() {
  $helper = GeneratedContentHelper::getInstance();

  /** @var \Drupal\generated_content\Helpers\GeneratedContentAssetGenerator $assetHelper */
  $assetHelper = \Drupal::service('generated_content.asset_generator');
  $assetHelper->getAssets();

  $variations = [
    [
      'title' => 'Rolling cabinet',
      'description' => $helper::randomPlainParagraph(),
      'categories' => 'Classroom',
      'location' => 'Logistics Center',
      'pictures' => ['prefix' => 'cabinet', 'indices' => [1,2]],
    ], [
      'title' => 'Book shelves',
      'description' => $helper::randomPlainParagraph(),
      'categories' => 'Office',
      'location' => 'Logistics Center',
      'pictures' => ['prefix' => 'shelves', 'indices' => [1]],
    ], [
      'title' => 'Science lab table',
      'description' => $helper::randomPlainParagraph(),
      'categories' => 'Classroom',
      'location' => 'Logistics Center',
      'pictures' => ['prefix' => 'table', 'indices' => [1,2,3,4]],
    ], [
      'title' => 'Power point clicker',
      'description' => $helper::randomPlainParagraph(),
      'categories' => 'Electronics',
      'location' => 'Logistics Center',
      'pictures' => ['prefix' => 'clicker', 'indices' => [1,2,3]],
    ], [
      'title' => 'Webcam',
      'description' => $helper::randomPlainParagraph(),
      'categories' => 'Camera',
      'location' => 'Logistics Center',
      'pictures' => ['prefix' => 'webcam', 'indices' => [1,2]],
    ]
  ];

  return $variations;
}
