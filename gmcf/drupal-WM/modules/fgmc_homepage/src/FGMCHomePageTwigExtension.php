<?php

/**
 * @file
 * Contains Drupal\fgmc_homepage\FGMCHomePageTwigExtension
 */

namespace Drupal\fgmc_homepage;

/**
 * Class FGMCHomePageTwigExtension.
 *
 * @package Drupal\fgmc_homepage
 */
class FGMCHomePageTwigExtension extends \Twig_Extension {

  /**
   * {@inheritdoc}
   * This function must return the name of the extension. It must be unique.
   */
  public function getName() {
    return 'fgmc_homepage_twig_extension';
  }

  /**
   * In this function we can declare the extension function.
   */
  public function getFunctions() {
    return array(
      new \Twig_SimpleFunction('get_mi_rates', array($this, 'getMIRates'), array('is_safe' => array('html'))),
      new \Twig_SimpleFunction('get_internal_path_from_uri', array($this, 'getInternalPathFromURI'), array('is_safe' => array('html'))),
      new \Twig_SimpleFunction('getPreview_Req', array($this, 'getPreview_Req'), array('is_safe' => array('html'))),
      new \Twig_SimpleFunction('getRatesDateAndTime', array($this, 'getRatesDateAndTime'), array('is_safe' => array('html'))),
      new \Twig_SimpleFunction('getInternalPathFromTargetID', array($this, 'getInternalPathFromTargetID'), array('is_safe' => array('html'))),
      new \Twig_SimpleFunction('attach_js_variables', array($this, 'attachJSvariables'), array('is_safe' => array('html'))),
    );
  }

  /**
   * Fetches Rates from API call
   * 
   * @return array
   */
  public function getMIRates() {
    $rates = array();
    try {
      $rates = \Drupal::config('demo.settings')->get('homepagerates');
	  foreach($rates as $key => $value){            
            preg_match("/[^\\d*](\\d.*)/", $key, $matches);
            $rates1[$matches[1]]  = $value;
      }
    }
    catch (\Exception $ex) {
      \Drupal::logger('getMIRates')->error('Server Exception: ' . $ex->getMessage());
    }
    return $rates1;
  }

  /**
   * Returns internal path from link
   * 
   * @param string $uri
   * @return string
   */
  public function getInternalPathFromURI($uri) {
    try {
      $url = \Drupal\Core\Url::fromUri($uri);
      return $url->toString();
    }
    catch (\InvalidArgumentException $ex) {
      return '';
    }
    catch (\Exception $ex) {
      return '';
    }
  }
  /**
   * Returns internal path from node target id
   * 
   * @param string $uri
   * @return string
   */
  public function getInternalPathFromTargetID($target_id) {
    try { 
        $page_path =   \Drupal\Core\Url::fromRoute('entity.node.canonical', ['node' =>$target_id ], ['absolute' => false])->toString();
        return $page_path;
    }
    catch (\InvalidArgumentException $ex) {
      return '';
    }
    catch (\Exception $ex) {
      return '';
    }
  }
  /**
   *Returns Preview Req True or False based on Request path.
   *
   */
  public function getPreview_Req() {
    $preview_req = false;
    $post_params = \Drupal::request()->server->get('REQUEST_URI');
    $path = explode('/', $post_params);
    if(in_array('preview', $path) || in_array('draft', $path)){
        $preview_req = true;
    }
    return $preview_req;
  }
  /*
   * This method create a twig function {{ attach_js_variables }}
   * @params $module, $key=> variable name, $value=>variable value.
   */
  public function attachJSvariables($module,$key,$value) {
      // Use Renderer::render() on a temporary render array to get additional
      // bubbleable metadata on the render stack.
      $template_attached = ['#attached' => ['drupalSettings' =>[$module =>[$key => [$value]]]]];
      return \Drupal::service('renderer')->render($template_attached);
  }

}
