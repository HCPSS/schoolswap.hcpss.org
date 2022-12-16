<?php

namespace Drupal\hcpss_saml_login\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\RedirectResponse;

class RedirectController extends ControllerBase {
  public function samlLogin() {
    return new RedirectResponse(Url::fromRoute('simplesamlphp_auth.saml_login')->toString());
  }
}
