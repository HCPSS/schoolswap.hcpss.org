<?php

function _hcpss_classified_create_node_listing__variations__post_process($variations) {
  foreach ($variations as &$variation) {
    $variation += [
      'status' => TRUE,
    ];
  }

  return $variations;
}
