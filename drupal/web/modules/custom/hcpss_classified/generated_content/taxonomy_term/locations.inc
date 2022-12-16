<?php

use Drupal\Component\Serialization\Json;
use Drupal\taxonomy\Entity\Term;

/**
 * @implements hook_generated_content_create_ENTITY_TYPE_BUNDLE
 */
function hcpss_classified_generated_content_create_taxonomy_term_locations() {
  $api = 'https://api.hocoschools.org/facilities.json';
  $facilities = Json::decode(file_get_contents($api));
  $locations = [];

  foreach ($facilities as $facility) {
    $location = Term::create([
      'vid' => 'locations',
      'name' => $facility['name'],
      'field_code' => $facility['code'],
      'field_address' => [
        'address_line1' => $facility['street'],
        'locality' => $facility['city'],
        'administrative_area' => $facility['state'],
        'postal_code' => $facility['postal_code'],
        'country_code' => 'US',
      ],
    ]);

    $location->save();
    $locations[] = $location;
  }

  return $locations;
}
