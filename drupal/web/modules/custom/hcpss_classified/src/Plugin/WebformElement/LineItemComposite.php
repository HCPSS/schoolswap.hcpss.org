<?php

namespace Drupal\hcpss_classified\Plugin\WebformElement;

use Drupal\Core\Annotation\Translation;
use Drupal\webform\Annotation\WebformElement;
use Drupal\webform\Plugin\WebformElement\WebformCompositeBase;
use Drupal\webform\WebformSubmissionInterface;

/**
 * @WebformElement(
 *   id = "line_item_composite",
 *   label = @Translation("Line item composite"),
 *   description = @Translation("Provides a line item element."),
 *   category = @Translation("HCPSS"),
 *   multiline = TRUE,
 *   composite = TRUE,
 *   states_wrapper = FALSE,
 * )
 */
class LineItemComposite extends WebformCompositeBase {

  /**
   * {@inheritdoc}
   */
  protected function formatHtmlItemValue(array $element, WebformSubmissionInterface $webform_submission, array $options = []) {
    return $this->formatTextItemValue($element, $webform_submission, $options);
  }

  /**
   * {@inheritdoc}
   */
  protected function formatTextItemValue(array $element, WebformSubmissionInterface $webform_submission, array $options = []) {
    $value = $this->getValue($element, $webform_submission, $options);
    $lines = [];
    $lines[] = 'yis';
    return $lines;
  }
}
