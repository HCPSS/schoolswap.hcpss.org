<?php

namespace Drupal\hcpss_classified\Helpers;

use Drupal\generated_content\Helpers\GeneratedContentAssetGenerator;

class ListingAssetGenerator extends GeneratedContentAssetGenerator {

  const GENERATE_TYPE_SPECIFIC = 'specific';

  protected static function generatorMap() {
    $map = parent::generatorMap();

    $map[static::GENERATE_TYPE_SPECIFIC][static::ASSET_TYPE_JPG] = [
      static::class,
      'generateContentFileSpecificJpg'
    ];

    return $map;
  }

  public function generateContentFileSpecificJpg($type, array $options = []) {
    $options += [
      'index' => 1,
      'prefix' => 'cabinet',
    ];

    $files = $this->assets[$type] ?? NULL;

    if (empty($files)) {
      throw new \RuntimeException(sprintf('Unable to create a static asset: "%s" source asset type is not available.', $type));
    }

    foreach ($files as $file) {
      if (str_ends_with($file, "/{$options['prefix']}{$options['index']}.jpg")) {
        return $file;
      }
    }

    throw new \RuntimeException(sprintf('Unable to create a static asset: "%s" source asset type is not available.', $type));
  }

  /**
   * {@inheritdoc}
   */
  protected function getAssetsDirs() {
    $module_path = $this->moduleExtensionList->getPath('hcpss_classified');

    return [
      $module_path . DIRECTORY_SEPARATOR . rtrim(static::ASSETS_DIRECTORY, DIRECTORY_SEPARATOR),
    ];
  }
}
