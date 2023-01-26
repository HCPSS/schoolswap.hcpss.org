<?php

namespace Drupal\hcpss_classified\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use Drupal\node\NodeInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Returns responses for HCPSS Classified routes.
 */
class HcpssClassifiedController extends ControllerBase {

  /**
   * Builds the response.
   */
  public function build(Request $request, NodeInterface $node) {
    $cart = $request->getSession()->get('cart', []);
    if (!empty($cart[$node->id()])) {
      unset($cart[$node->id()]);
    }
    $request->getSession()->set('cart', $cart);

    $destination = Url::fromRoute('hcpss_classified.checkout');
    return new RedirectResponse($destination->toString());
  }
}
