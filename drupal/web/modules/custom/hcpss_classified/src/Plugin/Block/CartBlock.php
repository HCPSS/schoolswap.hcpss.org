<?php

namespace Drupal\hcpss_classified\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Routing\AccessAwareRouterInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Provides a cart block.
 *
 * @Block(
 *   id = "hcpss_classified_cart",
 *   admin_label = @Translation("Cart"),
 *   category = @Translation("HCPSS")
 * )
 */
class CartBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The session.
   *
   * @var \Symfony\Component\HttpFoundation\Session\SessionInterface
   */
  protected $session;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The access aware router.
   *
   * @var \Drupal\Core\Routing\AccessAwareRouterInterface
   */
  protected $accessAwareRouter;

  /**
   * Constructs a new CartBlock instance.
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
   * @param \Symfony\Component\HttpFoundation\Session\SessionInterface $session
   *   The session.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\Core\Routing\AccessAwareRouterInterface $access_aware_router
   *   The access aware router.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, SessionInterface $session, EntityTypeManagerInterface $entity_type_manager, AccessAwareRouterInterface $access_aware_router) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->session = $session;
    $this->entityTypeManager = $entity_type_manager;
    $this->accessAwareRouter = $access_aware_router;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('session'),
      $container->get('entity_type.manager'),
      $container->get('router')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $cart = $this->session->get('cart', []);
    $count = 0;

    if (!empty($cart)) {
      foreach ($cart as $quantity) {
        $count += $quantity;
      }
    }

    if ($count) {
      $build['content'] = [
        '#markup' => vsprintf('<p>%d items in cart. <a href="%s">Checkout &raquo;</a></p>', [
          $count,
          Url::fromRoute('hcpss_classified.checkout')->toString(),
        ]),
      ];
    } else {
      $build['content'] = [
        '#markup' => '<p>0 items in cart.</p>',
      ];
    }

    return $build;
  }

  /**
   * @return int|void
   */
  public function getCacheMaxAge() {
    return 0;
  }
}
