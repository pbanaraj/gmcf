<?php

namespace Drupal\s3fs;

use Drupal\Core\Asset\CssOptimizer;

/**
 * Optimizes a CSS asset.
 */
class S3fsCssOptimizer extends CssOptimizer {

  /**
   * Return absolute urls to access static files that they aren't in S3 bucket.
   *
   * @param array $matches
   *   An array of matches by a preg_replace_callback() call that scans for
   *   url() references in CSS files, except for external or absolute ones.
   *
   * @return string
   *   The file path.
   */
  public function rewriteFileURI($matches) {
    // Prefix with base and remove '../' segments where possible.
    $path = $this->rewriteFileURIBasePath . $matches[1];
    $last = '';
    while ($path != $last) {
      $last = $path;
      $path = preg_replace('`(^|/)(?!\.\./)([^/]+)/\.\./`', '$1', $path);
    }
    return 'url(' . file_create_url($path) . ')';
  }

}
