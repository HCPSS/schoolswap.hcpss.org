<?php

namespace Drupal\hcpss_classified\Element;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\webform\Element\WebformCompositeBase;

/**
 * Provides a line_item_composite.
 *
 * @FormElement("line_item_composite")
 */
class LineItemComposite extends WebformCompositeBase {

  /**
   * {@inheritdoc}
   */
  public function getInfo() {
    return parent::getInfo() + ['#theme' => 'line_item_composite'];
  }

  /**
   * {@inheritdoc}
   */
  public static function getCompositeElements(array $element) {
    $elements = [];

    $elements['item'] = [
      '#type' => 'entity_autocomplete',
      '#title' => t('Item'),
      '#target_type' => 'node',
      '#selection_handler' => 'default',
      '#selection_settings' => ['target_bundles' => ['listing']],
    ];

    $elements['quantity'] = [
      '#type' => 'number',
      '#title' => t('Quantity'),
      '#min' => 0,
    ];

    return $elements;
  }
}
