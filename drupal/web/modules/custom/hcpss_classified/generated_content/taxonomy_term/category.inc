<?php

use Drupal\taxonomy\TermInterface;
use Drupal\taxonomy\Entity\Term;

/**
 * @implements hook_generated_content_create_ENTITY_TYPE_BUNDLE
 */
function hcpss_classified_generated_content_create_taxonomy_term_category() {
  $names = [
    ['name' => 'Electronics', 'children' => [['name' => 'Camera'], ['name' => 'Overhead']]],
    ['name' => 'Furniture', 'children' => [['name' => 'Classroom'], ['name' => 'Office'], ['name' => 'Table']]],
    ['name' => 'Special Education Equipment'],
    ['name' => 'Textbooks', 'children' => [['name' => 'Elementary'], ['name' => 'Middle'], ['name' => 'High']]],
  ];

  $terms = [];
  $create = function (array $config, TermInterface $parent = null) use (&$create, &$terms) {
    $term = Term::create([
      'vid' => 'category',
      'name' => $config['name'],
    ]);

    if ($parent) {
      $term->parent = $parent;
    }

    $term->save();
    $terms[] = $term;

    if (!empty($config['children'])) {
      foreach ($config['children'] as $child) {
        $create($child, $term);
      }
    }
  };

  foreach ($names as $config) {
    $create($config);
  }

  return $terms;
}


