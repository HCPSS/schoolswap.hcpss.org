<?php

use Drupal\generated_content\Helpers\GeneratedContentAssetGenerator;
use Drupal\generated_content\Helpers\GeneratedContentHelper;
use Drupal\node\Entity\Node;
use Drupal\hcpss_classified\Helpers\ListingAssetGenerator;

/**
 * Implements hook_generated_content_create_ENTITY_TYPE_BUNDLE_weight().
 */
function hcpss_classified_generated_content_create_node_listing_weight() {
  return 1;
}

/**
 * Implements hook_generated_content_create_ENTITY_TYPE_BUNDLE().
 */
function hcpss_classified_generated_content_create_node_listing() {
  $nodes = [];

  /** @var GeneratedContentHelper $helper */
  $helper = GeneratedContentHelper::getInstance();

  $variations  = $helper::variationFetchAll(
    '_hcpss_classified_create_node_listing__variations__',
    __DIR__ . DIRECTORY_SEPARATOR . 'listing_variations'
  );

  foreach ($variations as $i => $variation) {
    $node = $helper::variationCreateNode('listing', $variation, $i, '_hcpss_classified_create_node_listing__variation_to_fields');
    $node->save();
    $nodes[] = $node;
  }

  return $nodes;
}

/**
 * Processor to convert variation values to 'page' node fields.
 */
function _hcpss_classified_create_node_listing__variation_to_fields($node, $variation) {
  $helper = GeneratedContentHelper::getInstance();

  if (!empty($variation['title'])) {
    $node->setTitle($variation['title']);
  }

  if (!empty($variation['description']) && $node->hasField('body')) {
    $node->body = ['format' => 'basic_html', 'value' => $variation['description']];
  }

  $get_term = function (string $vid, string $name) {
    $terms = \Drupal::entityTypeManager()
      ->getStorage('taxonomy_term')
      ->loadByProperties(['vid' => $vid, 'name' => $name]);

    return reset($terms);
  };

  $get_pictures = function (string $prefix, array $indices) use ($helper) {
    $pictures = [];
    foreach ($indices as $index) {
      $pictures[] = $helper::createFile(
        GeneratedContentAssetGenerator::ASSET_TYPE_JPG,
        ['prefix' => $prefix, 'index' => $index],
        ListingAssetGenerator::GENERATE_TYPE_SPECIFIC
      );
    }
    return $pictures;
  };

  $node->field_categories = $get_term('category', $variation['categories']);
  $node->field_location = $get_term('locations', $variation['location']);
  $node->field_pictures = $get_pictures($variation['pictures']['prefix'], $variation['pictures']['indices']);
}
