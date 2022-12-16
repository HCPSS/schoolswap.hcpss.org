<?php

namespace Drupal\hcpss_classified;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DependencyInjection\ServiceProviderBase;

class HcpssClassifiedServiceProvider extends ServiceProviderBase {

  /**
   * {@inheritdoc}
   */
  public function alter(ContainerBuilder $container) {
    $definition = $container->getDefinition('generated_content.asset_generator');
    $definition->setClass('Drupal\hcpss_classified\Helpers\ListingAssetGenerator');
  }
}
