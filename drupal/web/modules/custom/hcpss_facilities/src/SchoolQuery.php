<?php

namespace Drupal\hcpss_facilities;

/**
 * A class for querying the HCPSS school api.
 */
class SchoolQuery {

  /**
   * @var string
   */
  private $apiEndpoint;

  public function __construct(string $endpoint) {
    $this->apiEndpoint = $endpoint;
  }

  /**
   * @var array
   */
  private $data = [];

  public function getFacilities(): array {
    if (empty($this->data['facilities'])) {
      $json = file_get_contents("{$this->apiEndpoint}/facilities.json");
      $this->data['facilities'] = json_decode($json, TRUE);
    }

    return $this->data['facilities'];
  }

  /**
   * Get the school data.
   *
   * @return array
   */
  public function getSchools(): array {
    if (empty($this->data['schools'])) {
      $json = file_get_contents("{$this->apiEndpoint}/schools.json");
      $schools = json_decode($json, true)['schools'];

      foreach ($schools as $level => $acronyms) {
        foreach ($acronyms as $acronym) {
          $json = file_get_contents("{$this->apiEndpoint}/schools/{$acronym}.json");
          $this->data['schools'][$acronym] = json_decode($json, TRUE);
        }
      }
    }

    return $this->data['schools'];
  }
}
