<?php
namespace Drupal\fgmc_homepage\Controller;


use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\Core\Template\TwigEnvironment;

class FGMCHomePageController  extends ControllerBase implements ContainerInjectionInterface  {

  protected $twig;

  public function __construct(TwigEnvironment $twig)
  {
    $this->twig = $twig;
  }
  /**
   *  to return the twig instance
   */
  public static function create(ContainerInterface $container)
  {
    return new static(
      $container->get('twig')
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
      $rates1  = array();
	  foreach($rates as $key => $value){            
            preg_match("/[^\\d*](\\d.*)/", $key, $matches);
            $rates1[$matches[1]]  = $value;
      }
      $template = $this->twig->loadTemplate(drupal_get_path('module', 'fgmc_homepage') . '/templates/FGMCHomePageRates.html.twig');
    }
    catch (\Exception $ex) {
      \Drupal::logger('getMIRates')->error('Server Exception: ' . $ex->getMessage());
    }
    return new JsonResponse(array('status'=>1,'template'=>$template->render(array('rates'=>$rates1))));
  }
  /**
   *Returns Terms and Disclosure content.
   *
   */
  public function getTerms_and_disclosure(Request $request) {
      $nodeid = $request->request->get('tdid');
      $node = \Drupal::entityTypeManager()->getStorage("node")->load($nodeid);
      if($node) {
        return new JsonResponse(array('status'=>1,'title'=>$node->getTitle(),'body'=>$node->get('body')->value,'node_js'=>$node->get('field_node_js')->value));
      }else{
        return new JsonResponse(array('status'=>0));
      }
  }
  /**
   *Returns the Rates date and time stored in config variables.
   *
   */
  public function getRatesDateAndTime() {
	   date_default_timezone_set('US/Eastern');
       $dttimestp = \Drupal::config('demo.settings')->get('homepagerates_date_time');
       $time = date("h:i A", $dttimestp);
       $date = date("m/d/Y", $dttimestp);
       return new JsonResponse(array('status'=>1,'Ratedate'=>$date, 'Ratetime'=>$time));
  }
  /**
   * Returns the testimonial content
   *
   */
   public function getTestimonials(Request $request) {
      $nodeid = $request->request->get('bnid');
      $node = \Drupal::entityTypeManager()->getStorage("node")->load($nodeid);
      if($node) {
          $viewmode = 'default';
          $entityType = 'node';
          $display = entity_get_display($entityType, $node->getType(), $viewmode);
          $viewBuilder = \Drupal::entityTypeManager()->getViewBuilder($entityType);
          $content = 'No Testimonials';
          if (isset($node->{'field_testimonials'})) {
            $fieldRenderable = $viewBuilder->viewField($node->{'field_testimonials'}, $display->getComponent('field_testimonials'));
            if (count($fieldRenderable) &&! empty($fieldRenderable)) {
              $content = drupal_render($fieldRenderable);
            }
          }
        return new JsonResponse(array('status'=>1,'template'=>$content));
      }else{
        return new JsonResponse(array('status'=>0));
      }
   }

}