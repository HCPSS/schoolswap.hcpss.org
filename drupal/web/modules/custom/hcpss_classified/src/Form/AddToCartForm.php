<?php

namespace Drupal\hcpss_classified\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Markup;
use Drupal\Core\Url;
use Drupal\node\Entity\Node;
use Drupal\node\NodeInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Provides a HCPSS Classified form.
 */
class AddToCartForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'hcpss_classified_add_to_cart';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    /** @var \Drupal\node\NodeInterface $node */
    $node = $this->getRequest()->get('node');
    if (!($node instanceof NodeInterface) || $node->bundle() != 'listing') {
      throw new NotFoundHttpException();
    }

    $session = $this->getRequest()->getSession();
    $cart = $session->get('cart', []);

    $form['quantity'] = [
      '#type' => 'number',
      '#title' => $this->t('Quantity'),
      '#required' => TRUE,
      '#default_value' => empty($cart[$node->id()]) ? 1 : $cart[$node->id()],
      '#min' => 1,
      '#max' => $node->field_quantity->value,
    ];

    $form['available'] = [
      '#type' => 'item',
      '#markup' => "Available: ".$node->field_quantity->value,
    ];

    $form['nid'] = [
      '#type' => 'hidden',
      '#value' => $node->id(),
    ];

    $form['actions'] = [
      '#type' => 'actions',
    ];

    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Add and Keep Shopping'),
    ];

    $form['actions']['checkout'] = [
      '#type' => 'submit',
      '#value' => $this->t('Add and Checkout'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
//    if (mb_strlen($form_state->getValue('message')) < 10) {
//      $form_state->setErrorByName('quantity', $this->t('Message should be at least 10 characters.'));
//    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $session = $this->getRequest()->getSession();
    $cart = $session->get('cart', []);
    $nid = $form_state->getValue('nid');
    $cart[$nid] = $form_state->getValue('quantity');
    $session->set('cart', $cart);

    $node = Node::load($nid);
    $this->messenger()->addStatus(
      Markup::create("
        <em>{$node->getTitle()}<em> added to cart.
        <a href=\"/checkout\">Checkout</a> to complete your request.
      ")
    );

    switch ($form_state->getValue('op')) {
      case $form_state->getValue('submit'):
        $form_state->setRedirect('<front>');
        break;
      case $form_state->getValue('checkout'):
        $form_state->setRedirect('hcpss_classified.checkout');
        break;
      default:
        $form_state->setRedirect('<front>');
        break;
    }
  }
}
