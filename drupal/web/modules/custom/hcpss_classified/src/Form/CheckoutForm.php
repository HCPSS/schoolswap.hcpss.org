<?php

namespace Drupal\hcpss_classified\Form;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Mail\MailManagerInterface;
use Drupal\Core\Url;
use Drupal\node\Entity\Node;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\paragraphs\ParagraphInterface;
use Drupal\user\Entity\User;
use Drupal\user\UserInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a HCPSS Classified form.
 */
class CheckoutForm extends FormBase {

  /**
   * @var \Drupal\Core\Mail\MailManagerInterface
   */
  protected $mailer;

  /**
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  public function __construct(MailManagerInterface $mailer, EntityTypeManagerInterface $entityTypeManager) {
    $this->mailer = $mailer;
    $this->entityTypeManager = $entityTypeManager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('plugin.manager.mail'),
      $container->get('entity_type.manager')
    );
  }

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

    $form['comments'] = [
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

  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
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

    $ids = $this->entityTypeManager->getStorage('user')->getQuery()
      ->condition('status', 1)
      ->condition('roles', 'recipient')
      ->execute();

    if (empty($ids)) {
      return;
    }

    $emails = array_reduce(User::loadMultiple($ids), function (array $carry, UserInterface $item) {
      $carry[] = $item->getEmail();
      return $carry;
    }, []);

    $result = $this->mailer->mail(
      'hcpss_classified',
      'hcpss_classified_checkout',
      implode(', ', $emails),
      'en',
      ['nid' => $node->id()]
    );

    $this->getRequest()->getSession()->set('cart', []);
    $this->messenger()->addStatus('Thanks!');
  }
}
