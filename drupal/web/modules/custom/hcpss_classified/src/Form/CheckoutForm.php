<?php

namespace Drupal\hcpss_classified\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\node\Entity\Node;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\paragraphs\ParagraphInterface;

/**
 * Provides a HCPSS Classified form.
 */
class CheckoutForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'hcpss_classified_checkout';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $cart = $this->getRequest()->getSession()->get('cart', []);
    if (empty($cart)) {
      return ['message' => [
        '#type' => 'item',
        '#markup' => '<p>Your cart is empty</p>',
      ]];
    }



    $form['name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Name of Person Making Request'),
      '#required' => TRUE,
    ];

    $form['school'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Name of School or Office'),
      '#required' => TRUE,
    ];

    $form['date'] = [
      '#type' => 'date',
      '#title' => $this->t('Date of Delivery'),
      '#required' => TRUE,
    ];

    $form['return_date'] = [
      '#type' => 'date',
      '#title' => $this->t('Date of Return'),
      '#required' => FALSE,
    ];

    $form['comment'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Comments'),
      '#required' => FALSE,
    ];

    $form['line_items'] = ['#type' => 'container', '#tree' => TRUE];
    foreach ($cart as $nid => $quantity) {
      $form['line_items'][$nid] = ['#type' => 'container', '#tree' => TRUE];
      $node = Node::load($nid);
      $form['line_items'][$nid]['quantity'] = [
        '#type' => 'number',
        '#title' => $this->t('Quantity'),
        '#default_value' => $quantity,
      ];

      $form['line_items'][$nid]['nid'] = [
        '#type' => 'hidden',
        '#value' => $nid,
      ];

      $form['line_items'][$nid]['title'] = [
        '#type' => 'item',
        '#markup' => $node->getTitle(),
      ];

      $form['line_items'][$nid]['remove'] = [
        '#type' => 'link',
        '#url' => Url::fromRoute('hcpss_classified.remove_listing', ['node' => $nid]),
        '#title' => $this->t('Remove'),
      ];
    }

    $form['actions'] = [
      '#type' => 'actions',
    ];

    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Send'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
//    if (mb_strlen($form_state->getValue('message')) < 10) {
//      $form_state->setErrorByName('message', $this->t('Message should be at least 10 characters.'));
//    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    dpm($form_state->getValues());

    $line_items = [];
    foreach ($form_state->getValue('line_items') as $item) {
      $listing = Node::load($item['nid']);
      $quantity = $item['quantity'];
      $paragraph = Paragraph::create([
        'type' => 'line_item',
        'field_listing' => $listing,
        'field_quantity' => $quantity,
      ]);
      $paragraph->save();
      $line_items[] = $paragraph;
    }

    $node = Node::create([
      'type' => 'order',
      'field_line_items' => $line_items,
      'field_name' => $form_state->getValue('name'),
      'field_school' => $form_state->getValue('school'),
      'field_date' => $form_state->getValue('date'),
      'field_return_date' => $form_state->getValue('return_date'),
      'field_comments' => $form_state->getValue('comments'),
    ]);
    $node->save();

    $this->getRequest()->getSession()->set('cart', []);
    $this->messenger()->addStatus('Tkanks!');


//    $this->messenger()->addStatus($this->t('The message has been sent.'));
//    $form_state->setRedirect('<front>');
  }

}
