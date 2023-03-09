<?php

namespace Drupal\hcpss_classified\Plugin\Block;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Url;

/**
 * Provides a login block.
 *
 * @Block(
 *   id = "hcpss_classified_login",
 *   admin_label = @Translation("Login"),
 *   category = @Translation("HCPSS")
 * )
 */
class LoginBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  protected function blockAccess(AccountInterface $account) {
    return AccessResult::allowedIf($account->isAnonymous());
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build['login_block'] = [
      '#type' => 'container',
      '#attributes' => ['class' => 'login-block']
    ];

    $build['login_block']['login_link'] = [
      '#title' => $this->t('Login'),
      '#type' => 'link',
      '#url' => Url::fromRoute('simplesamlphp_auth.saml_login'),
    ];

    return $build;
  }
}
