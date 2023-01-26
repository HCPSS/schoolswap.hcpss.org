<?php

namespace Drupal\hcpss_classified\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormBuilderInterface;
use Drupal\Core\Http\RequestStack;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\hcpss_classified\Form\AddToCartForm;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides an add to cart block.
 *
 * @Block(
 *   id = "hcpss_classified_add_to_cart",
 *   admin_label = @Translation("Add to Cart"),
 *   category = @Translation("HCPSS")
 * )
 */
class AddToCartBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The form builder.
   *
   * @var \Drupal\Core\Form\FormBuilderInterface
   */
  protected $formBuilder;

  /**
   * @var RequestStack
   */
  protected $requestStack;

  /**
   * Constructs a new AddToCartBlock instance.
   *
   * @param array $configuration
   *   The plugin configuration, i.e. an array with configuration values keyed
   *   by configuration option name. The special key 'context' may be used to
   *   initialize the defined contexts by setting it to an array of context
   *   values keyed by context names.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Form\FormBuilderInterface $form_builder
   *   The form builder.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, FormBuilderInterface $form_builder, RequestStack $request_stack) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->formBuilder = $form_builder;
    $this->requestStack = $request_stack;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('form_builder'),
      $container->get('request_stack')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    if (!$this->requestStack->getCurrentRequest()->get('node')) {
      return [];
    }

    return $this->formBuilder->getForm(AddToCartForm::class);
  }

  /**
   * @return int|void
   */
  public function getCacheMaxAge() {
    return 0;
  }
}
