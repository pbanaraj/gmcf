<?php
namespace Drupal\customise\Controller;

use \Drupal\Core\Url;
use Drupal\customise\Form\ContributeForm;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Ajax;
use Drupal\Core\Ajax\CssCommand;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Drupal\Component\Serialization\Json;
use \Drupal\Component\Utility;

use Drupal\Core\Template\TwigEnvironment;

/**
 * key Content Type Controller.
 */
class CustomiseController extends ControllerBase implements ContainerInjectionInterface  {

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
  
  public function product_details($arg =  null){
	
	$pd_config 	 = \Drupal::config('demo.settings')->get('pd_config');
	$page_title  =  $pd_config['pd_title']; 
	$page_desc 	 =  $pd_config['pd_desc']; 
	$finalResult = array();
	$sid         = 'homepage_product';
	$entity_subqueue = \Drupal::entityManager()->getStorage('entity_subqueue')->load($sid);
	$items = $entity_subqueue->get('items')->getValue();
	foreach($items as $item) {
		$nodeids[] = $item['target_id'];
	}
	$nodes = \Drupal::entityTypeManager()->getStorage('node')->loadMultiple($nodeids);
	$i= 1;
	$center = 0;	
	foreach($nodes as $node) {
	  $nid = $node->get('nid')->value;
	  if($nid == $arg)
		  $center = $i;
	  $product_image = $node->get('field_image')->entity->uri->value;
	  
	  if($product_image && $product_image != '')
			$finalResult[$i]['product_image'] 		= file_create_url($node->get('field_image')->entity->uri->value);
	  else{
		  
		  $finalResult[$i]['product_image'] = '';
	  }		  
	  $finalResult[$i]['title'] 				= $node->get('title')->value; 	 	  
	  $finalResult[$i]['product_short_desc'] 	= $node->get('field_product_short_desc')->value;
	  $finalResult[$i]['product_long_desc'] 	= $node->get('field_product_long_desc')->value;	  
	  $finalResult[$i]['product_video_url'] 	= $node->get('field_product_video_url')->value;
	  $finalResult[$i]['product_dyk'] 			= $node->get('field_product_section_desc')->value;
	  $finalResult[$i]['product_vowel'] 		= $this->product_vowel_chk($finalResult[$i]['title']);
	  $i++;
	  
	}
	$count  = count($finalResult);
	$is_mobile = $this->isMobileDev();
	if($center){
		if($count <= 7)
			$finalResult = $this->product_swap($center, 4, $finalResult);
		else
			$finalResult = $this->product_swap($center, 1, $finalResult);
			
	}
	return array(      
	  '#theme' 	=> 'product_detail_page',
	  '#name' 	=> array('result'=>$finalResult,'page_title'=> $page_title,'page_desc'=>$page_desc,'count'=>$count,'center'=>$center,'is_mobile'=>$is_mobile),
	  
	  
    );  
  }
  public function all_product_details(){
	
	$pd_config 	= \Drupal::config('demo.settings')->get('pd_config');
	$page_title =  $pd_config['pd_title']; 
	$page_desc 	=  $pd_config['pd_desc']; 
	$finalResult = array();
	$sid = 'homepage_product';
	$entity_subqueue = \Drupal::entityManager()->getStorage('entity_subqueue')->load($sid);
	$items = $entity_subqueue->get('items')->getValue();
	foreach($items as $item) {
		$nodeids[] = $item['target_id'];
	}
	$nodes = \Drupal::entityTypeManager()->getStorage('node')->loadMultiple($nodeids);
	$i= 1;
	
	foreach($nodes as $node) {
	  $nid = $node->get('nid')->value;	
	  $product_image = $node->get('field_image')->entity->uri->value;
	  
	  if($product_image && $product_image != '')
			$finalResult[$i]['product_image'] 		= file_create_url($node->get('field_image')->entity->uri->value);
	  else{
		  
		  $finalResult[$i]['product_image'] = '';
	  }
	  $finalResult[$i]['title'] 				= $node->get('title')->value;
	  //$finalResult[$i]['product_image'] 		= file_create_url($node->get('field_image')->entity->uri->value);	
	  $finalResult[$i]['product_type'] 		= $node->get('field_product_type')->value; 
	  $finalResult[$i]['product_short_desc'] 	= $node->get('field_product_short_desc')->value;
	  $finalResult[$i]['product_long_desc'] 	= $node->get('field_product_long_desc')->value;	  
	  $finalResult[$i]['product_video_url'] 	= $node->get('field_product_video_url')->value;
	  $finalResult[$i]['product_dyk'] 			= $node->get('field_product_section_desc')->value;
	  $finalResult[$i]['product_vowel'] 		= $this->product_vowel_chk($finalResult[$i]['title']);
	  $i++;
	}
	$count  = count($finalResult);
	$is_mobile = $this->isMobileDev();
	
	return array(      
	  '#theme' 	=> 'product_detail_page',
	  '#name' 	=> array('result'=>$finalResult,'page_title'=> $page_title,'page_desc'=>$page_desc,'count'=>$count,'center'=>$center,'is_mobile'=>$is_mobile),
	  
	  
    );  
  }
  public function isMobileDev(){
    if(isset($_SERVER['HTTP_USER_AGENT']) and !empty($_SERVER['HTTP_USER_AGENT'])){
       $user_ag = $_SERVER['HTTP_USER_AGENT'];
       if(preg_match('/(Mobile|Android|Tablet|GoBrowser|[0-9]x[0-9]*|uZardWeb\/|Mini|Doris\/|Skyfire\/|iPhone|Fennec\/|Maemo|Iris\/|CLDC\-|Mobi\/)/uis',$user_ag)){
          return true;
       }else{
          return false;
       };
    }else{
       return false;    
    };
}
  public function product_vowel_chk($title){
	$first = $title[0];	 
	$vowelArr = array('A','E','I','O','U','a','e','i','o','u');
	if(in_array($first,$vowelArr))
		return 'an';
	else
		return 'a';
  }
	public function product_swap($key1, $key2, &$array) {
		$ary = array();
		$temp = array();
		
        $temp			= $array[$key1];
		$array[$key1]	= $array[$key2];
		$array[$key2]	= $temp;
        return $array;
    }
	public function whyus_testimonials() {
		$whyus_config 	= \Drupal::config('demo.settings')->get('whyus_detail_config');		 
		$finalResult 	= array();		
		$query 			= \Drupal::entityTypeManager()->getStorage("node")->getQuery();
		$query->condition('type' ,'why_us_testimonials','=')
			  ->condition('status' ,1);
		$result 		= $query->execute();
		$nodes 			= \Drupal::entityTypeManager()->getStorage("node")->loadMultiple($result);
		$i = 1;		
		$leftTopHtml 	= '<ul id="testimonial1">';
		$leftBottomHtml = '<ul id="testimonial2">';
		$rightTopHtml 	= '<ul id="testimonial3">';
		$rightBottomHtml = '<ul id="testimonial4">';
		foreach($nodes as $k => $node) {			
			$resultArr[$i]['nid']      = $node->get('nid')->value;
			$resultArr[$i]['imageUrl'] = file_create_url($node->get('field_author_image')->entity->uri->value);;
			$resultArr[$i]['body']     = $node->get("body")->value;
			$i++;
		}
		
		$leftTopArr = $this->shuffle_assoc($resultArr);$i = 1;
		
		foreach($leftTopArr as $k => $node) {
			$imageUrl 	= $node['imageUrl'];		  	  
			$body = $node['body'];
			if($i % 2 != 0){
				$leftTopHtml .= '<li><div class="col"><div class="overlay"><span class="fa fa-quote-left db-quotes"></span><p>'.wordwrap(html_entity_decode($body),25,"<br>\n",TRUE).'</p><span class="fa fa-quote-right text-right db-quotes"></span></div>'.'<img src="'.$imageUrl.'" alt="">'.'</div></li>';
			}else{
				$leftTopHtml .= '<li><div class="col">'.'<img src="'.$imageUrl.'" alt="">'.'</div></li>';
			}
			$i++;
		}		
		$leftBottomArr = $this->shuffle_assoc($resultArr);$i = 1;
		foreach($leftBottomArr as $k => $node) {					  
			$imageUrl 	= $node['imageUrl'];		  	  
			$body       = $node['body'];
			 if($i % 2 != 0){
				 $leftBottomHtml .= '<li><div class="col">'.'<img src="'.$imageUrl.'" alt="">'.'</div></li>';		 
			 }else{
				//$leftBottomHtml .= '<li><div class="col"><div class="overlay"></div><div class="testimonial-text"><span>"</span>'.wordwrap(html_entity_decode($body),25,"<br>\n",TRUE).'<span class="text-right">"</span></div>'.'<img src="'.$imageUrl.'" alt="" height="300" width="100%">'.'</div></li>';
				$leftBottomHtml .= '<li><div class="col"><div class="overlay"><span class="fa fa-quote-left db-quotes"></span><p>'.wordwrap(html_entity_decode($body),25,"<br>\n",TRUE).'</p><span class="fa fa-quote-right text-right db-quotes"></span></div>'.'<img src="'.$imageUrl.'" alt="">'.'</div></li>';				
			}
		$i++;
		}
		$rightTopArr = $this->shuffle_assoc($resultArr);$i = 1;
		foreach($rightTopArr as $k => $node) {
			$imageUrl 	= $node['imageUrl'];		  	  
			$body = $node['body'];
			 if($i % 2 != 0){
				 $rightTopHtml .= '<li><div class="col">'.'<img src="'.$imageUrl.'" alt="">'.'</div></li>';		 
			 }else{
				//$rightTopHtml .= '<li><div class="col"><div class="overlay"></div><span>"</span>'.wordwrap(html_entity_decode($body),25,"<br>\n",TRUE).'<span class="text-right">"</span>'.'<img src="'.$imageUrl.'" alt="" height="300" width="100%">'.'</div></li>';	
				$rightTopHtml .= '<li><div class="col"><div class="overlay"><span class="fa fa-quote-left db-quotes"></span><p>'.wordwrap(html_entity_decode($body),25,"<br>\n",TRUE).'</p><span class="fa fa-quote-right text-right db-quotes"></span></div>'.'<img src="'.$imageUrl.'" alt="" >'.'</div></li>';					
			}
		$i++;
		}
		$rightBottomArr = $this->shuffle_assoc($resultArr);$i = 1;
		foreach($rightBottomArr as $k => $node) {
			$imageUrl 	= $node['imageUrl'];		  	  
			$body = $node['body'];
			 if($i % 2 != 0){
				 $rightBottomHtml .= '<li><div class="col"><div class="overlay"><span class="fa fa-quote-left db-quotes"></span><p>'.wordwrap(html_entity_decode($body),25,"<br>\n",TRUE).'</p><span class="fa fa-quote-right text-right db-quotes"></span></div>'.'<img src="'.$imageUrl.'" alt="" >'.'</div></li>';		 
				 	 
			 }else{
				$rightBottomHtml .= '<li><div class="col">'.'<img src="'.$imageUrl.'" alt="">'.'</div></li>';	
			}
		$i++;
		}
		$leftTopHtml .= '</ul>';
		$leftBottomHtml .= '</ul>';
		$rightBottomHtml .= '</ul>';		
		$rightTopHtml .= '</ul>';
		$response = new AjaxResponse();    
		$response->addCommand(new HtmlCommand('#left_testimonial', $leftTopHtml.$leftBottomHtml));
		$response->addCommand(new HtmlCommand('#right_testimonial', $rightTopHtml.$rightBottomHtml));
		
		//our process section
		$ourProcessHtml = '';		
		$sid = 'homepage_why_us';
		$queue_status = \Drupal::config('entityqueue.entity_queue.'.$sid)->get('status');
		$nodeids = array();
		if($queue_status) {
			$entity_subqueue = \Drupal::entityManager()->getStorage('entity_subqueue')->load($sid);
			$items = $entity_subqueue->get('items')->getValue();
			foreach($items as $item) {
				$nodeids[] = $item['target_id'];
			}
		}
		$results = array();
		if(!empty($nodeids)) {
			$nodes = \Drupal::entityTypeManager()->getStorage('node')->loadMultiple($nodeids);	
			foreach($nodes as $node) {
				if($node->get('status')->value == 1) {
					$imageurl 		= file_create_url($node->get('field_why_us_type_icon')->entity->uri->value);  
					$why_us_title 	= $node->get('title')->value;      	  
					$why_us_data	= $node->get('field_why_us_data')->value;
					$ourProcessHtml.= '<div class="col-md-4 padd40 text-center"><div class="col"><p>'.$why_us_title.'</p>';
					$ourProcessHtml.= '<div class="whyus_data_image"><img src="'.$imageurl.'"></div> <h5>'.$why_us_data.'</h5></div></div>';
				}		  
			}
		}
		$response->addCommand(new HtmlCommand('#our_process_ajax', $ourProcessHtml));
		//certificate
		$certificateHtml = '';
		$query = \Drupal::entityTypeManager()->getStorage("node")->getQuery();
		$query->condition('type' ,'why_us_certificates','=')
			  ->condition('status' ,1);
		$result = $query->execute();
		$nodes = \Drupal::entityTypeManager()->getStorage("node")->loadMultiple($result);		
		$is_mobile = $this->isMobileDev();
		if(count($nodes)> 4 || $is_mobile){
			$certificateHtml.= '<ul class="bxslider">';
		}else{
			$certificateHtml.= '<ul class="awards">';
		}
		foreach($nodes as $node) {			
			$id = $node->get('nid')->value;
			$certificateIcon 	= file_create_url($node->get('field_why_us_type_icon')->entity->uri->value);
			$ext_link_val = $node->get('field_external_link')[0]->uri;	  
			if($ext_link_val && $ext_link_val != '') {
				$ext_link = Url::fromUri($node->get('field_external_link')[0]->uri)->toString();
				$certificateHtml.= '<li class="text-center" id="'.$id.'"><a class="ft_bot" href="'.$ext_link.'"><img src="'.$certificateIcon.'" alt=""></a></li>';
			} else {			
				$certificateHtml.= '<li class="text-center" id="'.$id.'"><img src="'.$certificateIcon.'" alt=""></li>';
			}			
		}
		$certificateHtml.= '</ul>';
		$response->addCommand(new HtmlCommand('#certificate_ajax', $certificateHtml));
		return $response;
	}
	public function shuffle_assoc($list) { 
		$keys = array_keys($list); 
		shuffle($keys); 
		$random = array(); 
		foreach ($keys as $key) { 
		$random[] = $list[$key]; 
	}
	return $random; 
	} 
	public function why_goodmortgage() {
		$alias 			= \Drupal::service('path.alias_manager')->getPathByAlias('/whyusdetail');
		$params 		= Url::fromUri("internal:" . $alias)->getRouteParameters();
		$entity_type 	= key($params);
		$node 			= \Drupal::entityTypeManager()->getStorage($entity_type)->load($params[$entity_type]);
		$body 			= $node->get('body')->value;
		$node_js 		= $node->get('field_node_js')->value;
		$node_css 		= $node->get('field_node_css')->value;		
		return array(      
		  '#theme' 	=> 'whyus_detail_page_new',
		  '#name' 	=> array('body'=>$body,'node_js'=>$node_js,'node_css'=>$node_css),
		 
		);
	}
	public function aboutus_leaders_ajax() {
				 
		$finalResult = array();		
		$query = \Drupal::entityTypeManager()->getStorage("node")->getQuery();
		$query->condition('type' ,'leaders','=')
			  ->condition('status' ,1);
		$result = $query->execute();
		$nodes = \Drupal::entityTypeManager()->getStorage("node")->loadMultiple($result);
		
		$Html = '';		
		$i = 0;
		$Html.= '<div class="row mrgT30">';
		foreach($nodes as $k => $node) {
			$nid 				= $node->get('nid')->value;		  
			$profileImageUrl 	= file_create_url($node->get('field_why_us_type_icon')->entity->uri->value);
			$HoverImageUrl 		= file_create_url($node->get('field_author_image')->entity->uri->value);
			$name 				= $node->get('field_customer_name')->value;
			$designation 		= $node->get('field_why_us_data')->value;
			$description        = $node->get('field_short_description')->value;
			/*if($i != 0 && $i % 3 == 0){			
				$Html.= '</div>';
			}
			if($i % 3 == 0){			
				$Html.= '<div class="row mrgT30">';
			}*/
            //$onclick = 	"showDetails('col-$i'); return false;";		
			$Html .= '<div class="col-lg-4 col-sm-6"><div class="col" id="col-{$i}">';
			$Html .= '<div class="profile"><p class="names">'.$name.'</p><p class="deg">'.$designation.'</p></div>';
			$Html .= '<div class="overlay"><p class="names">'.$name.'</p><p class="deg">'.$designation.'</p><p class="desc">'.$description.'</p></div>'.'<img class="profile-pic1 img-rounded" src="'.$profileImageUrl.'" alt=""><img class="profile-pic2 img-rounded" src="'.$HoverImageUrl.'" alt="">';
			$Html .= '</div></div>';
			
		   $i++;
		}
		$Html.= '</div>';		
		$response = new AjaxResponse();    
		$response->addCommand(new HtmlCommand('ajax_load_leaders', $Html));		
		return $response;
	}
	public function our_terms_ajax() {
				 
		$finalResult = array();		
		$query = \Drupal::entityTypeManager()->getStorage("node")->getQuery();
		$query->condition('type' ,'terms_of_use','=')
			  ->condition('status' ,1);
		$result = $query->execute();
		$nodes = \Drupal::entityTypeManager()->getStorage("node")->loadMultiple($result);
		
		$Html = '';
		foreach($nodes as $k => $node) {
			$nid 				= $node->get('nid')->value;	
			$title        		= $node->get('title')->value;
			$description        = $node->get('field_product_section_desc')->value;
			$Html .= '<div class="accordion-container" id="terms_ct_'.$nid.'"><a href="#" class="accordion-toggle">'.$title.'<span class="toggle-icon"><i class="fa fa-plus-circle"></i></span></a>';
			$Html .= '<div class="accordion-content" id="terms_ct_'.$nid.'"><div class="inner-accordion"><p class="text-center">'.$description.'</p></div></div></div>';
			
		}		
		$response = new AjaxResponse();    
		$response->addCommand(new HtmlCommand('terms_ajax', $Html));		
		return $response;
	}
	public function privacy_policy_ajax() {
				 
		$finalResult = array();		
		$query = \Drupal::entityTypeManager()->getStorage("node")->getQuery();
		$query->condition('type' ,'privacy_policy','=')
			  ->condition('status' ,1);
		$result = $query->execute();
		$nodes = \Drupal::entityTypeManager()->getStorage("node")->loadMultiple($result);
		
		$Html = '';
		foreach($nodes as $k => $node) {
			$nid 				= $node->get('nid')->value;	
			$title        		= $node->get('title')->value;
			$description        = $node->get('field_product_section_desc')->value;
			
            //$onclick = 	"showDetails('col-$i'); return false;";		
			$Html .= '<div class="accordion-container" id="privacy_ct_'.$nid.'"><a href="#" class="accordion-toggle">'.$title.'<span class="toggle-icon"><i class="fa fa-plus-circle"></i></span></a>';
			$Html .= '<div class="accordion-content" id="privacy_con_'.$nid.'"><div class="inner-accordion"><p class="text-center">'.$description.'</p></div></div></div>';
			
		}		
		$response = new AjaxResponse();    
		$response->addCommand(new HtmlCommand('privacy_policy_ajax', $Html));		
		return $response;
	}
	public function disclosure_ajax() {
				 
		$finalResult = array();		
		$query = \Drupal::entityTypeManager()->getStorage("node")->getQuery();
		$query->condition('type' ,'disclosures','=')->condition('field_disclosure' ,'content','=')
			  ->condition('status' ,1);
		$result = $query->execute();
		$nodes = \Drupal::entityTypeManager()->getStorage("node")->loadMultiple($result);		
		$HtmlContent = '<div id="disclosure_content">';
		
		foreach($nodes as $k => $node) {
			$nid 				= $node->get('nid')->value;	
			$title        		= $node->get('title')->value;
			$description        = $node->get('field_product_section_desc')->value;
			$HtmlContent .= '<div class="accordion-container" id="disc_ct_'.$nid.'"><a href="#" class="accordion-toggle">'.$title.'<span class="toggle-icon"><i class="fa fa-plus-circle"></i></span></a>';
			$HtmlContent .= '<div class="accordion-content" id="disc_con_'.$nid.'"><div class="inner-accordion"><p class="text-center">'.$description.'</p></div></div></div>';
			
		}
		$HtmlContent .= '</div>';
		
		//get pdf urls
		$queryPdf = \Drupal::entityTypeManager()->getStorage("node")->getQuery();
		$queryPdf->condition('type' ,'disclosures','=')->condition('field_disclosure' ,'pdf','=')
			  ->condition('status' ,1);
		$resultPdf = $queryPdf->execute();
		$nodesPdf = \Drupal::entityTypeManager()->getStorage("node")->loadMultiple($resultPdf);		
		$HtmlContent .= '<div id="disclosure_pdf">';
		
		foreach($nodesPdf as $k => $nodePdf) {
			$nid_pdf 				= $nodePdf->get('nid')->value;	
			$title_pdf        		= $nodePdf->get('title')->value;
			$url_pdf        		= file_create_url($nodePdf->get('field_download_doc')->entity->uri->value);
				
			$HtmlContent .= '<div class="pdf-container">'.$title_pdf.'<a href="'.$url_pdf.'" download><img src="/themes/basis/images/pdf_download.png" /></a></div>';
			$HtmlContent .= '</div>';
		}	
		$response = new AjaxResponse();		
		$response->addCommand(new HtmlCommand('disclosure_ajax', $HtmlContent));
		return $response;
	}
	public function about_us() {
		
		$alias			= \Drupal::service('path.alias_manager')->getPathByAlias('/aboutus');
		$params   		= Url::fromUri("internal:" . $alias)->getRouteParameters();
		$entity_type 	= key($params);
		$node     		= \Drupal::entityTypeManager()->getStorage($entity_type)->load($params[$entity_type]);
		
		$body     		= $node->get('body')->value;
		$node_js  		= $node->get('field_node_js')->value;
		$node_css 		= $node->get('field_node_css')->value;		
		return array(      
		  '#theme' 	=> 'about_detail_page',
		  '#name' 	=> array('body'=>$body,'node_js'=>$node_js,'node_css'=>$node_css),		 
		);
	}

	public function mortgage_school_detail() {
	
		$alias = \Drupal::service('path.alias_manager')->getPathByAlias('/mortgage_school');
		$params = Url::fromUri("internal:" . $alias)->getRouteParameters();
		$entity_type = key($params);
		$node = \Drupal::entityTypeManager()->getStorage($entity_type)->load($params[$entity_type]);
		$body = $node->get('body')->value;
		$node_js = $node->get('field_node_js')->value;
		$node_css = $node->get('field_node_css')->value;
		return array(      
		  '#theme' 	=> 'mortgage_school_detail',
		  '#name' 	=> array('body'=>$body,'node_js'=>$node_js,'node_css'=>$node_css),
		 
		);
	}
	public function mortgage_school_tiles() {
		$sid = 'education_tiles';
        $queue_status = \Drupal::config('entityqueue.entity_queue.'.$sid)->get('status');
        $nodeids = array();
        if($queue_status) {
            $entity_subqueue = \Drupal::entityManager()->getStorage('entity_subqueue')->load($sid);
            $items = $entity_subqueue->get('items')->getValue();
            foreach($items as $item) {
                $nodeids[] = $item['target_id'];
            }
        }
        $finalResult = array();
		if(!empty($nodeids)) {
			$nodes = \Drupal::entityTypeManager()->getStorage("node")->loadMultiple($nodeids);
			
			foreach($nodes as $node) {
				$nid = $node->get('nid')->value;	 
				$ids = array_column($node->field_related_sections->getValue(), 'target_id');
				/*$query = \Drupal::database()->select('node__field_rel_tile', 'nrtile');
				$query->fields('nrtile', ['field_rel_tile_target_id']);
				$query->leftjoin('node_field_data', 'nfd', 'nfd.nid = nrtile.entity_id');
				$query->condition('nfd.status', 1);
				$query->condition('nrtile.field_rel_tile_target_id', $nid);
				$result = $query->execute()->fetchAll();*/
				$num_of_results = count($ids);
				$finalResult[$nid]['tile_title'] = $node->get('title')->value;
				$finalResult[$nid]['tile_spec'] = $node->get('body')->value;
				$finalResult[$nid]['tile_short_desc'] = $node->get('field_product_section_desc')->value;
				$finalResult[$nid]['tile_icon'] = file_create_url($node->get('field_why_us_type_icon')->entity->uri->value);
				$finalResult[$nid]['sec_count'] = $num_of_results;
				$tile_node = \Drupal::entityTypeManager()->getStorage('node')->load($nid);
				$ids = array_column($tile_node->field_related_sections->getValue(), 'target_id');
				$finalResult[$nid]['section_id'] = $ids[0];
			}
		}
		$tile_content_html = "<ul id='tiles-wrap'>";		
		
		foreach($finalResult as $key_tile => $val_tile) {
			if($val_tile['sec_count'] > 0) {
				$view_more_link = "/mortgage_school_sections_detail/collapse".$val_tile['section_id'];
			} else {
				$view_more_link = "#";
			}
			$tile_content_html .= '<li><div class="tiles-item"><div class="row"><div class="col-sm-4 col-xs-12"><img src="'.$val_tile['tile_icon'].'"></img></div><div class="col-sm-8 col-xs-12">'.
								  '<h3 class="text-left">'.$val_tile['tile_title'].'</h3></div></div><div class="row"><div class="col-sm-9 col-xs-9"><h4 class="text-left"> '.$val_tile['sec_count'].' sections</h4></div><div class="col-sm-3">'.
								  '<a href="'.$view_more_link.'"><i class="fa fa-angle-right" aria-hidden="true"></i></a></div></div></div><div class="tiles-content">'.
							      '<h2>'.$val_tile['tile_title'].'</h2><p>'.$val_tile['tile_short_desc'].'</p>'.$val_tile['tile_spec'].'<a href="'.$view_more_link.'" class="pull-right">click here to see more<i class="fa fa-angle-right" aria-hidden="true"></i></a></div></li>';
		}
		$tile_content_html .= "</ul>";
		$response = new AjaxResponse();    
		$response->addCommand(new HtmlCommand('#mortgage_school_tiles_content', $tile_content_html));
		return $response;
	}
	public function mortgage_school_search() {
		$search_term = $_REQUEST['query'];
		$query = \Drupal::entityTypeManager()->getStorage("node")->getQuery();
		$group = $query->orConditionGroup()
				->condition('title', '%' . db_like($search_term) . '%', 'LIKE')
				->condition('body', '%' . db_like($search_term) . '%', 'LIKE');
		$query->condition('type' ,'education_sections','=')
		  ->condition($group)
          ->condition('status' ,1);
		$result = $query->execute();
		$nodes = \Drupal::entityTypeManager()->getStorage("node")->loadMultiple($result);
		foreach($nodes as $node) {
			$nid = $node->get('nid')->value;
			$long_desc = $node->get('body')->value;		
			$sec_title = $node->get('title')->value;		
			$query_parent = \Drupal::database()->select('node__field_related_sections', 'nt_relsec');
			$query_parent->fields('nfd', ['title']);
			$query_parent->leftjoin('node_field_data', 'nfd', 'nfd.nid = nt_relsec.entity_id');
			$query_parent->condition('nt_relsec.field_related_sections_target_id', $nid);
			$result_parent = $query_parent->execute()->fetchAll();
			if(!empty($result_parent[0]->title)) {
				$finalResult[$nid]['id'] = $nid;										
				$finalResult[$nid]['label'] = $sec_title." - ".$result_parent[0]->title;
			}
		}
		echo json_encode($finalResult);exit;
	}
	public function mortgage_school_sections_detail($collapse) {		
		$alias = \Drupal::service('path.alias_manager')->getPathByAlias('/mortgage_school_sections_detail');
		$params = Url::fromUri("internal:" . $alias)->getRouteParameters();
		$entity_type = key($params);
		$node = \Drupal::entityTypeManager()->getStorage($entity_type)->load($params[$entity_type]);
		$body = $node->get('body')->value;
		$node_js = $node->get('field_node_js')->value;
		$node_css = $node->get('field_node_css')->value;
		return array(      
		  '#theme' 	=> 'mortgage_school_sections_detail',
		  '#name' 	=> array('body'=>$body,'node_js'=>$node_js,'node_css'=>$node_css),
		 
		);
	}
	/*
	public function mortgage_school_sections() {
		$parent_id = 127;
		$tile_node = \Drupal::entityTypeManager()->getStorage('node')->load($parent_id);
		$tiles_array['tile_title'] = $tile_node->get('title')->value;
		$tiles_array['tile_subtitle'] = $tile_node->get('field_product_section_desc')->value;
		$query = \Drupal::entityTypeManager()->getStorage("node")->getQuery();
		$query->condition('type' ,'education_sections','=')
          ->condition('status' ,1)
		  ->condition('field_rel_tile.target_id',$parent_id);
		$result = $query->execute();
		$nodes = \Drupal::entityTypeManager()->getStorage("node")->loadMultiple($result);
		
		foreach($nodes as $node) {
			$nid = $node->get('nid')->value;  
			$finalResult[$nid]['nid'] = $nid;
			$finalResult[$nid]['section_title'] = $node->get('title')->value;
			$finalResult[$nid]['section_long_desc'] = $node->get('body')->value;
			$finalResult[$nid]['section_short_desc'] = $node->get('field_product_section_desc')->value;			
		}
		$sections_info = '<h4>'.$tiles_array['tile_title'].'</h4><span class="font-md">'.$tiles_array['tile_subtitle'].'</span><div><div class="panel-group"  id="accordion">';
		$i = 1;
		foreach($finalResult as $key_tile => $val_tile) {		
			$sections_info .= '<div class="panel panel-info active"><div class="panel-heading"><h4 class="panel-title"><div class="row"><div class="col-lg-6">'.
								$val_tile['section_title'].'</div><div class="col-lg-6 text-right"><span>'.$val_tile['section_short_desc'].'</span><a class="panel-arrow collapsed" data-toggle="collapse"  data-parent="#accordion" href="#collapse'.$i.'"></a>'.
								'</div></div></h4></div><div id="collapse'.$i.'" class="panel-collapse collapse"><div class="panel-body">'.$val_tile['section_long_desc'].'<div class="footer"><div class="row"><div class="col-lg-8">'.
								'<span>Related Content</span><button type="button" class="btn btn-info"><span class="pdf"></span> Article on Credit Scores</button><button type="button" class="btn btn-info"><span class="calculator"></span> lorem ipsum</button>'.
								'</div><div class="col-lg-4"><ul class="social"><li>Share it on</li><li><a href="javascript:void(0)" class="fb"> </a></li>'.
								'<li><a href="javascript:void(0)" class="tw"> </a></li><li><a href="javascript:void(0)" class="gp"> </a></li>'.
								'<li><a href="javascript:void(0)" class="in"> </a></li></ul></div></div></div></div></div></div>';
			$i++;
		}
		$sections_info .= "</div></div>";
		$response = new AjaxResponse();    
		$response->addCommand(new HtmlCommand('#mortgage_school_sections_detail', $sections_info));
		return $response;
	}*/
	public function mortgage_school_sections(Request $request) {            
        global $base_url;
		$request_para =  $request->request->all();
                
		if(!empty($request_para['hashString']) && $request_para['hashString'] != "default") {
			$section_id = (int)str_replace("collapse","",$request_para['hashString']);
		} else {
			$query_firstsection = \Drupal::database()->select('node_field_data', 'nfd');
			$query_firstsection->fields('nfd', ['nid']);	
			$query_firstsection->condition('nfd.type', 'education_sections');
			$query_firstsection->range(0,1);
			$result_firstsection = $query_firstsection->execute()->fetchAll();
			$section_id = $result_firstsection[0]->nid;
		}
		
		//get parent id based on section id
		$query = \Drupal::database()->select('node__field_related_sections', 'nfrs');
		$query->fields('nfrs', ['entity_id']);			
		$query->condition('nfrs.field_related_sections_target_id', $section_id);
		$result = $query->execute()->fetchAll();
		$parent_id = $result[0]->entity_id;
		
		$tile_node = \Drupal::entityTypeManager()->getStorage('node')->load($parent_id);
		$ids = array_column($tile_node->field_related_sections->getValue(), 'target_id');
		$tiles_array['tile_title'] = $tile_node->get('title')->value;
		$tiles_array['tile_subtitle'] = $tile_node->get('field_product_section_desc')->value;
		/*$query = \Drupal::entityTypeManager()->getStorage("node")->getQuery();
		$query->condition('type' ,'education_sections','=')
                ->condition('status' ,1)
		->condition('field_rel_tile.target_id',$parent_id);
		$result = $query->execute();*/
		$nodes = \Drupal::entityTypeManager()->getStorage("node")->loadMultiple($ids); 
		foreach($nodes as $node) {
                    $nid = $node->get('nid')->value;  
                    $finalResult[$nid]['nid'] = $nid;
                    $finalResult[$nid]['section_title'] = $node->get('title')->value;
                    $finalResult[$nid]['section_long_desc'] = $node->get('body')->value;
                    $finalResult[$nid]['section_short_desc'] = $node->get('field_product_section_desc')->value;
					$edu_image = $node->get('field_image')->entity->uri->value;	  
					if($edu_image && $edu_image != '') {
						$finalResult[$nid]['edu_image'] 		= file_create_url($node->get('field_image')->entity->uri->value);
					} else{
						$finalResult[$nid]['edu_image'] = '';
					}
					$finalResult[$nid]['video'] = $node->get('field_video')->value;
					$multimedia_type = $node->get('field_multimedia_type')->value;
					$finalResult[$nid]['multimedia_type'] = $multimedia_type;
                    $url_to_share = $base_url."/mortgage_school_sections_detail/collapse".$nid;
                    $share_html = '';
                    $share_html .=  '<li><a href="#" onclick="share_fb(\''.$url_to_share.'\');return false;" rel="nofollow"  target="_blank">';
                    $share_html .= '<img src="/themes/basis/images/fb.png"></img></a></li>';
					$share_html .='<li><a target="_blank"  class="twitter-share-button"
                                        href="https://twitter.com/share"
                                        data-size="large"
                                        data-text="custom share text"
                                        data-url="'.$url_to_share.'"
                                        data-hashtags=""
                                        data-via="Good Mortgage"
                                        data-related="Good Mortgage">
                                      <img src="/themes/basis/images/tw.png"></img>
                                      </a></li>';
                    $share_html .= '<li><a href="https://plus.google.com/share?url='.$url_to_share.'" onclick="javascript:window.open(this.href,  \'\', \'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600\');return false;">';
                    $share_html .= '<img src="/themes/basis/images/gp.png"></img></a></li>';
                    $share_html .='<li><a target="_blank" href="https://www.linkedin.com/shareArticle?mini=true&url='.$url_to_share.'&source=FMCG"><img src="/themes/basis/images/in.png"></img></a></li>';

                    
                     $finalResult[$nid]['section_share_on_social'] = $share_html; 
					 
		}
		
		$sections_info = '<h1 class="text-center">'.$tiles_array['tile_title'].'</h1><span class="font-md text-center">'.$tiles_array['tile_subtitle'].'</span><div><div class="panel-group"  id="accordion">';
		
		foreach($finalResult as $key_tile => $val_tile) {	
			$nid = $val_tile['nid'];
			// get related content
			$query_rel_content = \Drupal::entityTypeManager()->getStorage("node")->getQuery();
			$query_rel_content->condition('type' ,'related_section_content','=')
								->condition('status' ,1)
								->condition('field_rel_section.target_id',$nid);
			$result_rel_content = $query_rel_content->execute();
			$nodes_rel_content = \Drupal::entityTypeManager()->getStorage("node")->loadMultiple($result_rel_content); 
			
			
			if(count($nodes_rel_content) > 0) {
				$buttons .= "<span>Related Content</span>";
				$buttons .= "<ul>";
				foreach($nodes_rel_content as $node_rel_content) {
					$button_text = $node_rel_content->get('title')->value;
					if($node_rel_content->get('field_type_rel')->value == 'DL') {
						$pdf_link = file_create_url($node_rel_content->get('field_download_doc')->entity->uri->value);
						$button_link = "window.open('".$pdf_link."')";
					} else if($node_rel_content->get('field_type_rel')->value == 'IL') {							
							$int_link_val = $node_rel_content->get('field_internal_link')[0]->uri;	  
							if($int_link_val && $int_link_val != '') {
								$int_link = Url::fromUri($node_rel_content->get('field_internal_link')[0]->uri)->toString();
								$button_link = "location.href='".$int_link."'";
							} else {
								$button_link = "";
							}
					} else if($node_rel_content->get('field_type_rel')->value == 'EL') {
							$ext_link_val = $node_rel_content->get('field_external_link')[0]->uri;	  
							if($ext_link_val && $ext_link_val != '') {
								$ext_link = Url::fromUri($node_rel_content->get('field_external_link')[0]->uri)->toString();
								$button_link = "window.open('".$ext_link."')";
							}else {
								$button_link = "";
							}
					}					
					$relContentImg_data = $node_rel_content->get('field_image')->entity->uri->value;	


				
					if($relContentImg_data && $relContentImg_data != '') {
						$relContentImg = file_create_url($node_rel_content->get('field_image')->entity->uri->value);						
						$buttons .= '<li><a href="javascript:void(0);" onclick="'.$button_link.'">'.$button_text.'</a></li>';
					} else{						
						$buttons .= '<li><a href="javascript:void(0);" onclick="'.$button_link.'">'.$button_text.'</a></li>';
					}					
				}
			}
			$buttons .= "</ul>";
			if($val_tile['multimedia_type'] == "IMG") {
				if(!empty($finalResult[$nid]['edu_image'])) {
					$image_or_video = '<img src="'.$finalResult[$nid]['edu_image'].'" alt="">';
				} else {
					$image_or_video = "";
				}
			} else {
				if(!empty($finalResult[$nid]['video'])){	
					if (!(strpos($finalResult[$nid]['video'], 'youtube.com') === false)) {
	                    if (!(strpos($finalResult[$nid]['video'], 'm.youtube.com/watch?v=') === false)) {							
	                           $youtube_path = str_replace('m.youtube.com/watch?v=', 'youtube.com/embed/', $finalResult[$nid]['video']);
	                    } else if (!(strpos($finalResult[$nid]['video'], 'youtube.com/watch?v=') === false)) {							
	                     		$youtube_path = str_replace('youtube.com/watch?v=', 'youtube.com/embed/', $finalResult[$nid]['video']);
	                    }						
						$image_or_video =  '<iframe width="420" height="315" src="'.$youtube_path.'" allowfullscreen></iframe>';
					}
				} else {
					$image_or_video = "";
				}
			}
			
			if($section_id == $nid) {
				$activeClass = "active";
			} else {
				$activeClass = "";
			}
			if($image_or_video != "") $cart_class = "col-lg-6"; else $cart_class = "col-lg-12";
			
			$sections_info .= '<div class="panel panel-default '.$activeClass.'">'
                                . '        <div class="panel-heading" data-toggle="collapse"  data-parent="#accordion" data-target="#collapse'.$val_tile['nid'].'"><h4 class="panel-title">'
                                . '         <div class="row"><div class="col-lg-6 col-sm-12 col-xs-12 collapse-title" id="collapse'.$val_tile['nid'].'_title">'.
                                             $val_tile['section_title'].'</div><div class="col-lg-6 col-sm-12 col-xs-12 text-right">'
                                . '          <span>'.$val_tile['section_short_desc'].'</span>'
                                . '           <a class="panel-arrow collapsed" data-toggle="collapse"  data-parent="#accordion" href="#collapse'.$val_tile['nid'].'"></a>'.
						'</div></div></h4></div><div id="collapse'.$val_tile['nid'].'" class="panel-collapse collapse"><div class="panel-body"><div class="row"><div class="col-lg-12 text-right"><div id="printMailDiv">'.
									'<a data-toggle="modal" href="#sendMail" class="skyTx pull-right mailcontent" data-id="collapse'.$nid.'"><img src="/themes/basis/images/email.png" alt=""> </a>'.
									'<a href="javascript:void(0)" onclick="print_content(\'#collapse'.$nid.'\')"><img src="/themes/basis/images/print.png" alt=""> </a>'.
									'</div></div><div class="'.$cart_class.' content">'.$val_tile['section_long_desc'].
                                    '</div><div class="col-lg-6"><div class="cart">'.$image_or_video.'</div></div>'.
									'<div class="col-lg-12 text-center paddTB15"><button type="button" class="btn btn-success wmreadytostart">Ready to start?</button></div></div>'.
									'<div class="footer"><div class="row">'
                                    . '<div class="col-lg-8"><div id="relatedContentDiv">'.$buttons.'</div></div>'						
                                    . '<div class="col-lg-4">'
                                    . '<ul class="social"><li>Share it on</li>'.$val_tile['section_share_on_social'].'</div>'
                                    . '</div>'
                                    . '</div>'
                                    . '</div>'
                                    . '</div>'
                                    . '</div>';
			
		}
		$sectionId = "#collapse".$section_id;
		$sections_info .= "</div></div><input type='hidden' id='sectionId' value='".$sectionId."'>";
                
		$response = new AjaxResponse();    
		$response->addCommand(new HtmlCommand('#mortgage_school_sections_detail', $sections_info));
		return $response;
	}

    public function third_party_showleadinfo() { 
		$alias = \Drupal::service('path.alias_manager')->getPathByAlias('/tp_lendig_tree_page');
		$params = Url::fromUri("internal:" . $alias)->getRouteParameters();
		$entity_type = key($params);
		$node = \Drupal::entityTypeManager()->getStorage($entity_type)->load($params[$entity_type]);
		$body = $node->get('body')->value;
		$node_js = $node->get('field_node_js')->value;
		$node_css = $node->get('field_node_css')->value;
		return array(      
		  '#theme' 	=> 'showleadinfo',
		  '#name' 	=> array('body'=>$body,'node_js'=>$node_js,'node_css'=>$node_css),
		 
		);
	}
        
    public function mortgage_school_sections_send_mail(Request $request){  
        
            $request_para =  $request->request->all();     
            $to_email_ids = $request_para['to_email'];
            $subject =   $request_para['subject']? $request_para['subject']:"Test subject";
            $body_html = "<p>Hi ".$request_para['to_name'].",</p>".
						 "<p>Your friend ".$request_para['from_name']." would like you to see this link on goodmortgage.com.</p>".
						 "<p><a href='".$request_para['link']."'>".$request_para['link']."</p>".
						 "<p>Your friend also says: ".$request_para['comments']."</p>".
						 "<p>This is a onetime email - you have not been added to any email distribution lists.</p>".
						 "<p>goodmortgage.com is an internet mortgage lender licensed in <#> States across the United States.  <a href='www.goodmortgage.com'>www.goodmortgage.com</a>  for more information or call us Toll Free at 877.523.3886.</p>";
            $response_status = [];

            
            $mailManager = \Drupal::service('plugin.manager.mail');
            $module = 'customise';
            $key = 'mail_content'; // Replace with Your key
            $params['subject'] = t($subject);
            $params['body'] = $body_html;
            $langcode = \Drupal::currentUser()->getPreferredLangcode();
            $send = true;

            $result = $mailManager->mail($module, $key, $to_email_ids, $langcode, $params);

            if ($result['result'] != true) {
                $message = t('There was a problem sending an email to @email.', array('@email' => $to_email_ids));                
                \Drupal::logger('mail-log')->error($message);
                $response_status =  ["status"=>"Failed","message"=>$message];
            }
            else{                
                $message = t('An email has been sent to @email ', array('@email' => $to_email_ids));               
                \Drupal::logger('mail-log')->notice($message);
                $response_status =  ["status"=>"Success","message"=>$message];                
            }
            print json_encode(\Drupal\Component\Utility\Xss::filter($response_status));
            exit;
     }
	public function calculate_payment_taxes_insurance() {
	
		$alias = \Drupal::service('path.alias_manager')->getPathByAlias('/calculate-payment-with-tax-insurance');
		$params = Url::fromUri("internal:" . $alias)->getRouteParameters();
		$entity_type = key($params);
		$node = \Drupal::entityTypeManager()->getStorage($entity_type)->load($params[$entity_type]);
		$body = $node->get('body')->value;
		$node_js = $node->get('field_node_js')->value;
		$node_css = $node->get('field_node_css')->value;
		$calcconfig_values = \Drupal::configFactory()->getEditable('demo.settings')->get("calcconfig");
		$loan_term = array("10"=>"10 Years","15"=>"15 Years","20"=>"20 Years","30"=>"30 Years");
		$pmiconfig_values = \Drupal::configFactory()->getEditable('demo.settings')->get("pmiconfig");
		$pmi_array = array_chunk($pmiconfig_values,3);
		$pmi_final_array = array_chunk($pmi_array, ceil(count($pmi_array) / 2));
		
		return array(      
		  '#theme' 	=> 'calculate_payment_taxes_insurance',
		  '#name' 	=> array('body'=>$body,'node_js'=>$node_js,'node_css'=>$node_css,'calcconfig_values'=>$calcconfig_values,'loan_term'=>$loan_term,'factorA' =>$pmi_final_array[1],'factorB' =>$pmi_final_array[0]),
		 
		);
	}
	public function calculate_refinance() {
	
		$alias = \Drupal::service('path.alias_manager')->getPathByAlias('/calculate-refinance');
		$params = Url::fromUri("internal:" . $alias)->getRouteParameters();
		$entity_type = key($params);
		$node = \Drupal::entityTypeManager()->getStorage($entity_type)->load($params[$entity_type]);
		$body = $node->get('body')->value;
		$node_js = $node->get('field_node_js')->value;
		$node_css = $node->get('field_node_css')->value;
		$calcconfig_values = \Drupal::configFactory()->getEditable('demo.settings')->get("calcconfig");
		for($i=1;$i<=30;$i++) {
			$remaining_term[$i] = $i;
		}
		$loan_term = array("10"=>"10 Years","15"=>"15 Years","20"=>"20 Years","30"=>"30 Years");
		return array(      
		  '#theme' 	=> 'calculate_refinance',
		  '#name' 	=> array('body'=>$body,'node_js'=>$node_js,'node_css'=>$node_css,'calcconfig_values'=>$calcconfig_values,'remaining_term'=>$remaining_term,'loan_term'=>$loan_term),
		 
		);
	}	 
	public function calculate_debt_consolidate() {
		/*$data_rates = \Drupal::config('demo.settings')->get('homepagerates');
		foreach($data_rates as $k => $v){
			$loan[$k] => $v['rates']['key_data']['Rate'];
		}*/
		$calcconfig_values = \Drupal::configFactory()->getEditable('demo.settings')->get("calcconfig");
		$loan_term = array("10"=>"10 Years","15"=>"15 Years","20"=>"20 Years","30"=>"30 Years");
		
		$alias = \Drupal::service('path.alias_manager')->getPathByAlias('/calculate-debt-consolidate');
		$params = Url::fromUri("internal:" . $alias)->getRouteParameters();
		$entity_type = key($params);
		$node = \Drupal::entityTypeManager()->getStorage($entity_type)->load($params[$entity_type]);
		$body = $node->get('body')->value;
		$node_js = $node->get('field_node_js')->value;
		$node_css = $node->get('field_node_css')->value;
		return array(      
		  '#theme' 	=> 'calculate_debt_conso',
		  '#name' 	=> array('body'=>$body,'node_js'=>$node_js,'node_css'=>$node_css,'calcconfig_values'=>$calcconfig_values,'loan_term'=>$loan_term),
		 
		);
	}

        public function third_party_pre_qual() { 
		$alias = \Drupal::service('path.alias_manager')->getPathByAlias('/third-party-landing');
		$params = Url::fromUri("internal:" . $alias)->getRouteParameters();
		$entity_type = key($params);
		$node = \Drupal::entityTypeManager()->getStorage($entity_type)->load($params[$entity_type]);
		$body = $node->get('body')->value;
		$node_js = $node->get('field_node_js')->value;
		$node_css = $node->get('field_node_css')->value;
		return array(      
		  '#theme' 	=> 'tp_landing',
		  '#name' 	=> array('body'=>$body,'node_js'=>$node_js,'node_css'=>$node_css),
		 
		);
	}
        
        public function third_party_pre_qual_thank() { 
		$alias = \Drupal::service('path.alias_manager')->getPathByAlias('/third-party-landing-thank-you');
		$params = Url::fromUri("internal:" . $alias)->getRouteParameters();
		$entity_type = key($params);
		$node = \Drupal::entityTypeManager()->getStorage($entity_type)->load($params[$entity_type]);
		$body = $node->get('body')->value;
		$node_js = $node->get('field_node_js')->value;
		$node_css = $node->get('field_node_css')->value;
		return array(      
		  '#theme' 	=> 'tp_landing_thank',
		  '#name' 	=> array('body'=>$body,'node_js'=>$node_js,'node_css'=>$node_css),
		 
		);
	}
        
        public function third_party_showleadinfo_ajax() {
            
        }
        
        public function third_party_pre_qual_ajax() {
            $finalResult = '';		
            $zillowAPIConfig = \Drupal::config('customise.settings');
            $zillow_endpoint = $zillowAPIConfig->get('zillow_review_endpoint');

            $client = \Drupal::httpClient();
            try {
              $response = $client->get($zillow_endpoint);
              $data = (string) $response->getBody();
              $data_array = json_decode($data,true);
              $finalResult .= '<section class="row ratings"><div class="container"><div class="col-lg-6"><ul class="bxslider-rating">';
              foreach($data_array['reviews'] as $key => $review) {
                       
                       $key_border = $key+1;
                  	$odd =  ($key_border%2)? 'borderR': '';
                        $title = Utility\Unicode::truncate($review['title'], 30, TRUE, TRUE);
                        $ratings_row =   '<li><div class=rating-col '.$odd.'><div class=rating-count><input type="hidden" class="rating" data-readonly value="'.$review['rating'].'" /></div><p title="'.$review['title'].'"><b> '.$title.'</b></p><p>'.date("F, Y", strtotime($review['updated'])).' - '.$review['reviewerName']['screenName'].'</p></div></li>';	
                          
                if($review['rating'] >= 4) {
                    $finalResult .= $ratings_row;
                }
              }
             $finalResult .= '</ul></div><div class="col-lg-1 col-sm-4 paddTB15"><p class="rating-col"><span class="font-lg"> '.$data_array['totalReviews'].'</span><br> Customer Reviews </p></div><div class="col-lg-5 col-sm-8 text-right"><img src="images/bankrate.png" alt=""><a class="ft_bot" href="https://www.bbb.org/charlotte/business-reviews/mortgage-lender/first-guaranty-mortgage-corporation-dba-goodmortgagecom-in-charlotte-nc-542287"><img src="/themes/basis/images/rating.png" alt=""></a></div></div></section>';
            }
            catch (RequestException $e) {
              watchdog_exception('customise', $e->getMessage());
            }
            
	echo $finalResult; exit;
        }
        
        public function third_party_pre_qual_thank_ajax() {
            
        }
	public function what_is_my_balance() {
		$alias = \Drupal::service('path.alias_manager')->getPathByAlias('/calculate-balance-estimator');
		$params = Url::fromUri("internal:" . $alias)->getRouteParameters();
		$entity_type = key($params);
		$node = \Drupal::entityTypeManager()->getStorage($entity_type)->load($params[$entity_type]);
                $calcconfig_values = \Drupal::configFactory()->getEditable('demo.settings')->get("calcconfig");
                
                $pmiconfig_values = \Drupal::configFactory()->getEditable('demo.settings')->get("pmiconfig");
		$pmi_array = array_chunk($pmiconfig_values,3);
		$pmi_final_array = array_chunk($pmi_array, ceil(count($pmi_array) / 2));
                $loan_term = array("10"=>"10 Years","15"=>"15 Years","20"=>"20 Years","30"=>"30 Years");
		$body = $node->get('body')->value;
		$node_js = $node->get('field_node_js')->value;
		$node_css = $node->get('field_node_css')->value;
		return array(      
		  '#theme' 	=> 'what_is_my_balance',
		  '#name' 	=> array('body'=>$body,'node_js'=>$node_js,'node_css'=>$node_css,'calcconfig_values'=>$calcconfig_values,'loan_term'=>$loan_term,'factorA' =>$pmi_final_array[1],'factorB' =>$pmi_final_array[0]),
		 
		);
	}
        
    public function loan_payment_calc() {
		$alias = \Drupal::service('path.alias_manager')->getPathByAlias('/calculate-loan-payment');
		$params = Url::fromUri("internal:" . $alias)->getRouteParameters();
		$entity_type = key($params);
		$node = \Drupal::entityTypeManager()->getStorage($entity_type)->load($params[$entity_type]);
                $calcconfig_values = \Drupal::configFactory()->getEditable('demo.settings')->get("calcconfig");
		
                $pmiconfig_values = \Drupal::configFactory()->getEditable('demo.settings')->get("pmiconfig");
		$pmi_array = array_chunk($pmiconfig_values,3);
		$pmi_final_array = array_chunk($pmi_array, ceil(count($pmi_array) / 2));
                $loan_term = array("10"=>"10 Years","15"=>"15 Years","20"=>"20 Years","30"=>"30 Years");
                $body = $node->get('body')->value;
		$node_js = $node->get('field_node_js')->value;
		$node_css = $node->get('field_node_css')->value;
		return array(      
		  '#theme' 	=> 'loan_payment_calc',
		  '#name' 	=> array('body'=>$body,'node_js'=>$node_js,'node_css'=>$node_css, 'calcconfig_values'=>$calcconfig_values,'loan_term'=>$loan_term, 'factorA' =>$pmi_final_array[1],'factorB' =>$pmi_final_array[0]),
		);
	}
	public function job_openings() {
		$query = \Drupal::entityTypeManager()->getStorage("node")->getQuery();
		$query->condition('type' ,'job_openings','=')
          ->condition('status' ,1);
		$result = $query->execute();
		$nodes = \Drupal::entityTypeManager()->getStorage("node")->loadMultiple($result);
		//echo "<pre>";print_r($nodes);die;
		$finalResult = array();
		foreach($nodes as $node) {
			$nid = $node->get('nid')->value;
			$finalResult[$nid]['job_title'] = $node->get('title')->value;
			$finalResult[$nid]['job_short_desc'] = $node->get('field_product_short_desc')->value;
			$finalResult[$nid]['job_benefits'] = $node->get('field_product_long_desc')->value;
			$finalResult[$nid]['job_location'] = $node->get('field_job_location')->value;
			$finalResult[$nid]['job_profile_doc'] = file_create_url($node->get('field_download_doc')->entity->uri->value);
			$finalResult[$nid]['job_benefits_doc'] = file_create_url($node->get('field_attach_benefit_flyer')->entity->uri->value);
		}
		$job_details_html = '<div class="panel-group" id="accordion">';
		foreach($finalResult as $key_career => $val_career) {
			$benefits_link = "window.open('".$val_career['job_benefits_doc']."')";
			$benefits_html = '<h5>Benefits: <span class="benefit_link" onclick="'.$benefits_link.'"> (Download Benefit Flyer)</span> </h5>'.$val_career['job_benefits'];
			$button_link = "window.open('".$val_career['job_profile_doc']."')";
			$job_details_html .= '<div class="panel panel-default" id="panel_job_'.$key_career.'"><div class="panel-heading" data-toggle="collapse" data-parent="#accordion" data-target="#collapse'.$key_career.'"><h4 class="panel-title"><div class="row"><div class="col-sm-6">'.
								  $val_career['job_title'].'<p>'.$val_career['job_location'].'</p></div><div class="col-sm-6 text-right"><span> See Job Description </span><a class="panel-arrow collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapse'.$key_career.'"> </a>
									</div></div></h4></div>'.
								  '<div id="collapse'.$key_career.'" class="panel-collapse collapse"><div class="panel-body content">'.$val_career['job_short_desc'].$benefits_html.'</div></div>'.
								  '<hr><div class="footer"><div class="row"><div class="col-sm-8"><span onclick="'.$button_link.'">Download '.$val_career['job_title'].' Profile</span>'.
								  '</div><div class="col-sm-4 text-right"><button id="apply_job_button" type="button" class="btn btn-success" data-toggle="modal" data-target="#myModal" data-position-apply="'.$val_career['job_title'].'">Apply Now</button><input type="hidden" id="position-apply" value=""></input>'.
								  '</div></div></div></div>';
		}
		$job_details_html .= '</div>';
		
		// get tour album details
		$query_album = \Drupal::entityTypeManager()->getStorage("node")->getQuery();
		$query_album->condition('type' ,'tour_album','=')
          ->condition('status' ,1);
		$result_album = $query_album->execute();
		$nodes_album = \Drupal::entityTypeManager()->getStorage("node")->loadMultiple($result_album);
		$finalResult_album = array();
		foreach($nodes_album as $node_album) {
			$nid = $node_album->get('nid')->value;
			$finalResult_album[$nid]['title'] = $node_album->get('title')->value;
			$finalResult_album[$nid]['image'] = file_create_url($node_album->get('field_image')->entity->uri->value);
		}
		$tour_album_html = '';
		foreach($finalResult_album as $key_tour => $val_tour) {
			$tour_album_html .= '<div style="z-index: 1; top: 0px; left: 0px; width: 800px; height: 356px; position: absolute; overflow: hidden;"><img data-u="image" src="'.$val_tour['image'].'" />'.
								'<div class="img-title">'.$val_tour['title'].'</div>'.
								'<img data-u="thumb" src="'.$val_tour['image'].'" /></div>';
		}
		$job_email_config = \Drupal::configFactory()->getEditable('demo.settings')->get("job_email_config");
		$recipient_email = '<a href="mailto:'.$job_email_config['job_email'].'">'.$job_email_config['job_email'].'</a>';
		$response = new AjaxResponse();    
		$response->addCommand(new HtmlCommand('#tour_album_content', $tour_album_html));
		$response->addCommand(new HtmlCommand('#job_openings_content', $job_details_html));
		$response->addCommand(new HtmlCommand('#job_recipient_email', $recipient_email));
		return $response;
	}
	public function job_application(Request $request) {	
			$job_email_config = \Drupal::configFactory()->getEditable('demo.settings')->get("job_email_config");
			$request_para =  $request->request->all();
			$to_email_ids = $job_email_config['job_email'];
			$name = $request_para['first_name']." ".$request_para['last_name'];
			$position = $request_para['position'];
			$email = $request_para['to_email'];
			$phone = $request_para['phone'];
            $subject =   "Application for ".$position;
            $body_html = "<p>Hi,</p>".	
						 "<p>You’ve received an application from ".$name." for ".$position.". The uploaded resume is attached to this email.</p>".
						 "<p>Email address of ".$name." is ".$email."</p>".
						 "<p>Contact number of ".$name." is ".$phone."</p>".
						 "<p></p>".
						 "<p>** Please note: Do not reply to this email. This email is sent from an unattended mailbox. Replies will not be read.</p>";

			$response_status = [];

            $file_tmp_name    = $_FILES['file']['tmp_name'];
			$file_name        = $_FILES['file']['name'];
			$file_size        = $_FILES['file']['size'];
			$file_type        = $_FILES['file']['type'];
			$file_error       = $_FILES['file']['error'];
			
			if($file_error > 0)
			{
				die('Upload error or No files uploaded');
			}
			//read from the uploaded file & base64_encode content for the mail
			$handle = fopen($file_tmp_name, "r");
			$content = fread($handle, $file_size);
			fclose($handle);
			//$encoded_content = chunk_split(base64_encode($content));
			$attachment = array(
			  'filecontent' => $content,
			  'filename' => $file_name,
			  'filemime' => $file_type,  
			);
            $mailManager = \Drupal::service('plugin.manager.mail');
            $module = 'customise';
            $key = 'mail_content'; // Replace with Your key
            $params['subject'] = $subject;
            $params['body'] = $body_html;
			$params['attachments'][] = $attachment;
            $langcode = \Drupal::currentUser()->getPreferredLangcode();
            $send = true;

            $result = $mailManager->mail($module, $key, $to_email_ids, $langcode, $params);

            if ($result['result'] != true) {
                $message = t('There was a problem sending an email to @email.', array('@email' => $to_email_ids));      
                \Drupal::logger('mail-log')->error($message);
                $response_status =  ["status"=>"Failed","message"=>$message];
            }
            else{                
                $message = t('Thank you for applying for @position position  ', array('@position' => $position));               
                \Drupal::logger('mail-log')->notice($message);
                $response_status =  ["status"=>"Success","message"=>$message];                
            }
            print json_encode(\Drupal\Component\Utility\Xss::filter($response_status));
            exit;
	}
        
    public function state_mortgage_license($tid =  0) {
        $sid = 'mortgage_license';
        $entity_subqueue = \Drupal::entityManager()->getStorage('entity_subqueue')->load($sid);
        $items = $entity_subqueue->get('items')->getValue();
        foreach($items as $item) {
                $nodeids[] = $item['target_id'];
        }
	
        $nodes =  \Drupal\node\Entity\Node::loadMultiple($nodeids);
        $license = array(); $state = array();
        foreach ($nodes as $key => $node) {
            $term_id = $node->get('field_state')->getValue()[0]['target_id'];
            
            if(!in_array($term_id, $state)){
                $term = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->load($term_id);
                $state[$term_id] = $term->name->value;
            }
            
            $id = $node->get('nid')->getValue()[0]['value'];
            
            //if(($tid == 0) && (strtolower($term->name->value) == 'others')){
            if($tid == 0){
                $license[$id]['nid'] = $node->get('nid')->getValue()[0]['value'];
                $license[$id]['title'] = $node->get('field_product_short_desc')->getValue()[0]['value'];
                $license[$id]['body'] = $node->get('body')->getValue()[0]['value'];
                $license[$id]['state'] = $state[$term_id];
                continue;
            }
            //if((($tid != 0) && ($term_id == $tid)) && (strtolower($term->name->value) != 'others')){
            if(($tid != 0) && ($term_id == $tid)){
                $license[$id]['nid'] = $node->get('nid')->getValue()[0]['value'];
                $license[$id]['title'] = $node->get('field_product_short_desc')->getValue()[0]['value'];
                $license[$id]['body'] = $node->get('body')->getValue()[0]['value'];
                $license[$id]['state'] = $state[$term_id];
            }
            
        }
        
        $finalResult = '';
       foreach($license as $key => $val){
           if(($val['state'] == $state[$tid]) && ($tid > 0)){
                $finalResult .= '<tr>
                             <td data-title="License Held">'.$val['title'].'</td>
                             <td data-title="State Regulatory Authority">'.$val['body'].'</td>
                        </tr>';
           }else if($tid == 0){
               $finalResult .= '<tr>
                             <td data-title="License Held">'.$val['title'].'</td>
                             <td data-title="State Regulatory Authority">'.$val['body'].'</td>
                        </tr>';
           }
       }
       if($finalResult == ''){$finalResult = '<tr><td colspan="2" style="text-align: center;">No result found.</td></tr>';}
       echo $finalResult;exit;
    }
    
    public function state_list() {
	
        $query = \Drupal::entityQuery('taxonomy_term');
        $query->condition('vid', "states");
        $tids = $query->execute();
        $terms = \Drupal\taxonomy\Entity\Term::loadMultiple($tids);
        
        $i = 0; $select = '';
        //$finalResult .= '<option value="-1">Select State</sption>';
        $finalResult .= '<option value="0" selected>All</sption>';
       foreach($terms as $key => $val){
           //$select = ($i == 0)?' selected':'';
           if(strtolower($val->get('field_state_code')->getValue()[0]['value']) == 'others'){
               continue;
           }
           $finalResult .= '<option value="'.$val->get('tid')->getValue()[0]['value'].'" '.$select.'>'.$val->get('field_state_code')->getValue()[0]['value'].'</option>';
           $i++;
       }
       
       echo $finalResult;exit;
    }

    /**
     * To generate calculator categories in calculator landing page.
     *
     */
    public function calculator_types(Request $request) {
        $vid = $request->get('vid');
        $terms = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree($vid,0,1);  // 0==> mean to fetch all terms with depth 1
        if(!empty($terms)) {
            $output = array();
            foreach($terms as $key=>$term){

                $term_name =  $term->name;
                $tid = $term->tid;
                $current_vid = $term->vid;
                $sub_terms = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree($current_vid,$tid);  // Not to display main category if it doesn't contain sub categories
                if(!empty($sub_terms)) {
                    $page_exists = 0;
                    foreach($sub_terms as $sub_term){
                        $query = \Drupal::entityTypeManager()->getStorage("node")->getQuery();
                            $query->condition('type' ,'calculators','=')
                            ->condition('status' ,1)
                            ->condition('field_sub_calculator',$sub_term->tid,'=');
                        $result = $query->execute();
                        if(!empty($result)){
                            $page_exists = 1;
                            continue;
                        }
                    }
                    $term_obj = \Drupal\taxonomy\Entity\Term::load($tid);
                    $isCommon = $term_obj->get('field_is_common_calc_category')->value;
                    if($page_exists && $isCommon==0)
                        $output[] = array('tid'=>$tid,'term_name'=>$term_name);
                }
            }
            return new JsonResponse(array('status'=>1,'data'=>$output));
        } else {
            return new JsonResponse(array('status'=>0,'data'=>'No Categories Found.'));
        }
    }

    /**
     * Based on Calculator category selection, populating the sub-category and their calculator titles with description
     *
     */
    public function calculator_sub_types(Request $request) {
        $tid = $request->get('tid');
        $vid = $request->get('vid');
        $template = $this->twig->loadTemplate(drupal_get_path('module', 'customise') . '/templates/calculator_sub_types_content.html.twig');
        if($tid != 'default') {
            $query = \Drupal::entityQuery('taxonomy_term');
            $query->condition('vid', $vid);
            $query->condition('field_is_common_calc_category', 1);
            $tids = $query->execute();  // fetching tid for the common category
            $terms = array();
            foreach($tids as $isCommonTid){  // fetching sub category tids for common category
                $termsIteration = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree($vid,$isCommonTid);
                $terms = array_merge($terms,$termsIteration);
            }
            $termsbyParent = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree($vid,$tid); // fetching sub category tids for the targeted parent category
            $terms = array_merge($termsbyParent,$terms); // merging both for display.

        } else {
            $terms = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree($vid); // get all terms irrespective of category(main & sub);
        }
        $output = '';
        $new_terms = array();
        foreach($terms as $key=>$term){
            if(!in_array($term->tid, $new_terms)) { // distincts the sub categories
                array_push($new_terms,$term->tid);
                if($tid!= 'default') {
                        $output .= $this->getCalculatorForTerms($term,$tid,$template);
                }else {
                    if($term->depth != 0) { // Avoiding the main categories while mapping calculator content type.
                        $output .= $this->getCalculatorForTerms($term,$tid,$template);
                    }
                }
            }
        }
        return new Response($output);
    }
    public function getCalculatorForTerms($term,$tid,$template) {
        $term_name =  $term->name;
        $tid = $term->tid;
        /* load the calculator page based on calculator sub categories */
        $query = \Drupal::entityTypeManager()->getStorage("node")->getQuery();
        $query->condition('type' ,'calculators','=')
            ->condition('status' ,1)
            ->condition('field_sub_calculator',$tid,'=');
        $result = $query->execute();
        $nodes = \Drupal::entityTypeManager()->getStorage("node")->loadMultiple($result);
        $calculator_array = array();
        foreach($nodes as $node) {
            $cal_page_id =  $node->get('field_linked_to_calculator_refer')->target_id;
            if(isset($cal_page_id)) {
                $page_path = \Drupal\Core\Url::fromRoute('entity.node.canonical', ['node' =>$cal_page_id ], ['absolute' => TRUE])->toString(); //$node->get('field_linked_to_calculator_refer')->target_id; //Url::fromUri($node->get('field_linked_calculator_refer')->get('target_id')->value)->toString();
				$page_path = preg_replace("/^http:/i", "https:", $page_path);
			}else{
                $page_path = '#';
            }			
            $calculator_array[] = array('title'=>$node->get('title')->value,'description'=>$node->get('field_calculator_description')->value,'page_path'=>$page_path);
        }
        return $template->render(array('term_name'=>$term_name,'calculator_array'=>$calculator_array));
    }
	public function good_mortage_tv_videos_ajax() {

        $query = \Drupal::entityQuery('entity_subqueue')
                ->condition('queue', 'gm_tv_videos');

        $queueids = $query->execute();
        $queues = entity_load_multiple('entity_subqueue', $queueids);

        $Html = '<div class=" tab-container"><button type="button" class="hidden-lg hidden-md vertical-menu-button collapsed" data-toggle="collapse" data-target="#goodMortgageTv" aria-expanded="false" ><span class="text">Featured</span>
    <span class="glyphicon glyphicon-menu-down" ></span></button>';
        $t = 1;
        $terms = entity_load_multiple('entity_subqueue', $queueids);
                 $tabid=1;
                 foreach ($terms as $term) {
                 $tabarrays[$tabid] = $term->get('name')->value;
                 $tabid++;
                }  
        $Html .= '<ul id="goodMortgageTv" class="tabs text-center collapse">';
        $Html .= '<li class="tab-link current" data-tab="tab-1">Featured</li>';
        foreach ($terms as $term) {
            $sid = $term->get('name')->value;
            $entity_subqueue = \Drupal::entityManager()->getStorage('entity_subqueue')->load($sid);
            $items = $entity_subqueue->get('items')->getValue();

            if (!empty($items)) {
                $Html .= '<li class="tab-link ' . $tabarrays[$t] . ' tab-' . ($t + 1) . '" mytab="tab-' . ($t + 1) . '" data-tab="tab-' . ($t + 1) . '">' . $term->get('title')->value . '</li>';
            }
            $t++;
        }
        $Html .= '</ul>';

        $Html .= '<div id="tab-1" class="tab-content current">';
        $t = 1;
        foreach ($terms as $term) {

            $sid = $term->get('name')->value;
            $entity_subqueue = \Drupal::entityManager()->getStorage('entity_subqueue')->load($sid);
            $items = $entity_subqueue->get('items')->getValue();
            $nodeids = array();
            foreach ($items as $item) {
                $nodeids[] = $item['target_id'];
            }
            $nodes = \Drupal::entityTypeManager()->getStorage('node')->loadMultiple($nodeids);
            if (!empty($items)) {
                $Html .= '<div class="tab-title text-center">
                            ' . $term->get('title')->value . '
                            <ul class="tabs">
                                    <li class="expand-btn pull-right tab-link viewall" id="'. $term->get('title')->value . '"  data-tab="tab-' . ($t + 1) . '">View All</li>
                            </ul>
                     </div>';
                $Html .= '<ul class="mortgage-tv">';


                $step = 1;
                foreach ($nodes as $node) {
                    $video_title = $node->get('field_video_title')->value;
//                    $thumbnailImageUrl = file_create_url($node->get('field_video_thumbnail')->entity->uri->value);
                    $videoUrl = $node->get('field_good_mortgage_video_url')->value;
                    $youtubethumbnailpath = $this->gm_get_youtube_thumbnail_path($videoUrl);

                    $Html .= '<li>';
                    $Html .= '<!--<img src="http://img.youtube.com/vi/<?php echo get_post_meta($post->ID,"youtubeId"");?>/0.jpg"/>-->';
                    $Html .= '<div class="video" >';
                    $Html .= '<img class="img-rounded" src="' . $youtubethumbnailpath . '" alt="video">';
                    $Html .= '<div class="overlay"></div>';
                    $Html .= '<div class="playBtn" tid="' . $sid . '" nid="' . $node->get('nid')->value . '" data-toggle="modal" data-target="#imagesSlider" data-backdrop="static"></div>';
                    $Html .= '<div class="playtext">Play</div>';
                    $Html .= '</div>';
                    $Html .= '<p class="img-title">' . $video_title . '</p>';
                    $Html .= '</li>';
                    if ($step == 3) {
                        break;
                    }
                    $step++;
                }
                $Html .= '</ul>';
            }


            $t++;
        }
        $Html .= '</div>';


       $t1 = 2;
        $t = 1;
        foreach ($terms as $term) {
            $sid = $term->get('name')->value;
            $entity_subqueue = \Drupal::entityManager()->getStorage('entity_subqueue')->load($sid);
            $items = $entity_subqueue->get('items')->getValue();
            $nodeids = array();
            foreach ($items as $item) {
                $nodeids[] = $item['target_id'];
            }
            $nodes = \Drupal::entityTypeManager()->getStorage('node')->loadMultiple($nodeids);

            $Html .= '<div id="tab-' . $t1 . '" class="tab-content ' . ($t1 == 1 ? $tabarrays[$t1] : '') . '">';
            $Html .= '<ul class="mortgage-tv">';
            $node = '';
            foreach ($nodes as $node) {
                $video_title = $node->get('field_video_title')->value;
//                $thumbnailImageUrl = file_create_url($node->get('field_video_thumbnail')->entity->uri->value);
                $videoUrl = $node->get('field_good_mortgage_video_url')->value;
                $youtubethumbnailpath = $this->gm_get_youtube_thumbnail_path($videoUrl);
                $Html .= '<li>';
                $Html .= '<div class="video" >';
                $Html .= '<img class="img-rounded" src="' . $youtubethumbnailpath . '" alt="video">';
//                $Html .= $youtubethumbnailpath;
//                                            $Html .= '<iframe src="'.$videoUrl.'?autoplay=0&start=0&rel=0"   width="100%" height="100%" frameborder="0" allowfullscreen></iframe>';
                $Html .= '<div class="overlay"></div>';
                $Html .= '<div class="playBtn" tid="' . $sid . '" nid="' . $node->get('nid')->value . '" data-toggle="modal" data-target="#imagesSlider" data-backdrop="static"></div>';
                $Html .= '<div class="playtext">Play</div>';
                $Html .= '</div>';
                $Html .= '<p class="img-title">' . $video_title . '</p>';
                $Html .= '</li>';
            }
            $Html .= '</ul>';
            $Html .= '</div>';
            $t1++;
            $t++;
        }
        $Html .= '<!-- Video Slider Modal -->
                <div class="modal fade" id="imagesSlider" role="dialog">
                  <div class="modal-dialog modal-lg">

                    <!-- Modal content-->
                    <div class="modal-content">
                      <div class="modal-header">
                        <button type="button" class="close stopme" data-dismiss="modal">&times;</button>
                      </div>
                      <div class="modal-body" id="good_mortgage_category_videos">


                      </div>
                    </div>

                  </div>
                </div>';
        //main container close
        $Html .= '</div>';

        $response = new AjaxResponse();
        $response->addCommand(new HtmlCommand('goodmortgate_tv', $Html));
        return $response;
    }

    public function good_mortgage_category_videos_ajax($tid = NULL, $nid = NULL) {
        $entity_subqueue = \Drupal::entityManager()->getStorage('entity_subqueue')->load($tid);
        $items = $entity_subqueue->get('items')->getValue();
        $nodeids = array();
        foreach ($items as $item) {
            $nodeids[] = $item['target_id'];
        }
        $nodes = \Drupal::entityTypeManager()->getStorage('node')->loadMultiple($nodeids);
        $Html = '';
        $Html .= '<div id="amazingslider-wrapper-1" style="display:block;position:relative;max-width:100%;margin:0 auto;">
		   <div id="amazingslider-1" style="display:block;position:relative;margin:0 auto;">
		     <ul class="amazingslider-slides" style="display:none;">';


        foreach ($nodes as $node) {
            if (($node->get('nid')->value ) == $nid) {
                $video_title = $node->get('field_video_title')->value;
                //$thumbnailImageUrl = file_create_url($node->get('field_video_thumbnail')->entity->uri->value);
                $youtubethumbnailpath='';
                $videoUrl = $node->get('field_good_mortgage_video_url')->value;
                $youtubevideopath = $this->gm_get_youtube_video_path($videoUrl);
                $youtubethumbnailpath = $this->gm_get_youtube_thumbnail_path($videoUrl);

                $Html .= '<li>
                            <img src="' . $youtubethumbnailpath . '" alt="'.$video_title.'"  title="'.$video_title.'" />
                            ' . $youtubevideopath . '
                         </li>';
                
                
            }
        }


        foreach ($nodes as $node) {
            if (($node->get('nid')->value ) != $nid) {
                $video_title = $node->get('field_video_title')->value;
                //$thumbnailImageUrl = file_create_url($node->get('field_video_thumbnail')->entity->uri->value);
                $youtubethumbnailpath='';
                $videoUrl = $node->get('field_good_mortgage_video_url')->value;
                $youtubevideopath = $this->gm_get_youtube_video_path($videoUrl);
                $youtubethumbnailpath = $this->gm_get_youtube_thumbnail_path($videoUrl);

                $Html .= '<li>
                            <img src="' . $youtubethumbnailpath . '" alt="'.$video_title.'"  title="'.$video_title.'" />
                             ' . $youtubevideopath . '                            
                         </li>';
            }
        }
        $Html .= '</ul>';
        $Html .= '<ul class="amazingslider-thumbnails" style="display:none;">';
        
        foreach ($nodes as $node) {
           if (($node->get('nid')->value ) == $nid) {
                $videoUrl = $node->get('field_good_mortgage_video_url')->value;            
                $video_title = $node->get('field_video_title')->value;
                $youtubethumbnailpath='';
                $youtubethumbnailpath = $this->gm_get_youtube_thumbnail_path($videoUrl);               
                $Html .= '<li><img src="' . $youtubethumbnailpath . '" alt="'.$video_title.'"  title="'.$video_title.'" /></li>';
            }
        }
        
        foreach ($nodes as $node) {
           if (($node->get('nid')->value ) != $nid) {
                $videoUrl = $node->get('field_good_mortgage_video_url')->value;            
                $video_title = $node->get('field_video_title')->value;
                $youtubethumbnailpath='';
                $youtubethumbnailpath = $this->gm_get_youtube_thumbnail_path($videoUrl);               
                $Html .= '<li><img src="' . $youtubethumbnailpath . '" alt="'.$video_title.'"  title="'.$video_title.'" /></li>';
            }
        }        
        $Html .= '</ul>';
        
        $Html .= '</div></div>';

        $response = new AjaxResponse();
        $response->addCommand(new HtmlCommand('good_mortgage_category_videos', $Html));
        return $response;
    }    
    

    public function gm_get_youtube_video_path($videopath) {
        if (!empty($videopath)) {
            if (!(strpos($videopath, 'youtube.com') === false)) {
                if (!(strpos($videopath, 'm.youtube.com/watch?v=') === false)) {
                    $youtube_path = str_replace('m.youtube.com/watch?v=', 'youtube.com/embed/', $videopath);
                } else if (!(strpos($videopath, 'youtube.com/watch?v=') === false)) {
                    $youtube_path = str_replace('youtube.com/watch?v=', 'youtube.com/embed/', $videopath);
                }
                $videoid = explode('=', $videopath);
//                $image_or_video = '<iframe id="' . $videoid[1] . '" class="playerid" width="802" height="360" src="' . $youtube_path . '?autoplay=0&amp;start=0&amp;enablejsapi=1&amp;rel=0&amp;showinfo=1&amp;controls=1" allowfullscreen="allowfullscreen"></iframe>';
                 $image_or_video = '<video  id="' . $videoid[1] . '" class="playerid" preload="none" src="'.$youtube_path.'?v='.$videoid[1].'&t=5592s&showinfo=0" ></video>';                 
            }
        } else {
            $image_or_video = '<img data-u="image" src="' . $imagepath . '" />';
        }
        return $image_or_video;
    }

    public function gm_get_youtube_thumbnail_path($videopath) {
        $videoid = explode('=', $videopath);
        //$image_video_thumbnail = '<img class="img-rounded"  src="https://img.youtube.com/vi/'.$videoid[1].'/0.jpg' .'"/>';
        $image_video_thumbnail = "https://img.youtube.com/vi/$videoid[1]/0.jpg";

        return $image_video_thumbnail;
    }
	
	public function global_search() {
		
		$search_term = $_REQUEST['query'];
		$query = \Drupal\search_api\Entity\Index::load('search_test')->query();
		$query->keys($search_term);
		$query->range(0, 25);
		$data = $query->execute();
		$list = [];
		foreach ($data AS $item) {		
			$data = explode(':', $item->getId());
			$data = explode('/', $data[1]);
			$list[] = $data[1];
	    }
		
		$nodes = \Drupal::entityTypeManager()->getStorage("node")->loadMultiple($list);
		
		foreach($nodes as $node) {
			$nid = $node->get('nid')->value;				
			$sec_title = $node->get('title')->value;
			$c_type = $node->getType();
			$basicP_type = "other";
			$tpl_page_arr = array('tpl','tpl_thank','tpl_showlead'); // exclude basic pages
			$in_link = "";
			switch($c_type) {
				case "basic_page":
					if($node->get('field_ctype')->value) $basicP_type = $node->get('field_ctype')->value; else $basicP_type = "other";
					$in_link = "/node/".$nid;
					break;
				case "calculators":
					$in_link = "/calculators";
					break;
				case "products":
					$in_link = "/products#".$nid;
					break;
				case "education_tiles":
					$in_link = "/mortgage_school_detail";
					break;
				case "education_sections":
					$in_link = "/mortgage_school_sections_detail/collapse".$nid;
					break;
				case "why_us":
					$in_link = "/whyusdetail";
					break;
				case "testimonials":
					$in_link = "/";
					break;
				case "why_us_certificates":
					$in_link = "/whyusdetail";
					break;
				case "why_us_testimonials":
					$in_link = "/whyusdetail";
					break;
				case "good_mortgate_tv":
					$query = \Drupal::database()->select('entity_subqueue__items', 'eqi');
					$query->fields('eqi', ['entity_id']);
					$query->condition('eqi.items_target_id', $nid);					
					$result = $query->execute()->fetchAll();
					$tab_id = $result[0]->entity_id;
					$in_link = "/good-mortgage-tv#".$tab_id;
					break;
				case "job_openings":
					$in_link = "/careers#job_".$nid;
					break;
				case "leaders":
					$in_link = "/about-us";
					break;
				case "mortgage_license":
					$in_link = "/state-mortgage-licenses";
					break;
				case "tour_album":
					$in_link = "/careers";
					break;
				case "terms_and_disclosure":
					$in_link = "/";
					break;
				case "terms_of_use":
					$in_link = "/terms-of-use#terms_".$nid;
					break;
				case "disclosures":
					$in_link = "/disclosures#disc_".$nid;
					break;
				case "privacy_policy":
					$in_link = "/privacy-policy#privacy_".$nid;
					break;
			}
			if(!in_array($basicP_type,$tpl_page_arr)) {
				$finalResult[$nid]['id'] = $nid;
				$finalResult[$nid]['label'] = $sec_title;
				$finalResult[$nid]['in_link'] = $in_link;	
			}
		}
		echo json_encode($finalResult);exit;
	}
        
     public function get_footer_links_json_data(){
        global $base_secure_url;
        $items = \Drupal::menuTree()->load('footer-right-menu', new \Drupal\Core\Menu\MenuTreeParameters());
        foreach ($items as $key => $item) {
            $wt = $item->link->getWeight();
             if($item->link->isEnabled()){
                $url = $item->link->getUrlObject();
                
                if(filter_var($url->toString(), FILTER_VALIDATE_URL)){
                    $url_final = $url->toString();
                }
                else{
                     $url_final = $base_secure_url.$url->toString();
                }
                
                if($item->link->getOptions()['attributes']['class'] == 'termsofuse'){
                    
                    $termsofuse_url = $url_final;
                    
                }
                $footer_menu["FooterLinks"][$wt] = array("title"=>$item->link->getTitle(), "url"=> $url_final);
             }
        }
        ksort($footer_menu["FooterLinks"]);
        $footer_menu["FooterLinks"] = array_values($footer_menu["FooterLinks"]);
        $query = \Drupal::database()->select('block_content', 'bc');
		$query->fields('bcfcn', ['field_contact_number_value', 'entity_id']);
		$query->join('block_content__field_contact_number', 'bcfcn', 'bc.id = bcfcn.entity_id');
		$query->condition('bcfcn.deleted', 0);
		$query->condition('bc.id', 12);

		$header_phone = ($query->execute()->fetchField());
        
        $footer_menu["PhoneNumber"] = $header_phone ;
        $footer_menu["GoodMortgageUrl"] = $base_secure_url ;
        $footer_menu["TermsAndConditions"] = $termsofuse_url;
        header("Access-Control-Allow-Origin: *");
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 86400');    
        
        echo json_encode($footer_menu);
        exit;

     }
     

  public function all_product_details_ajax(){
	
    $block_manager = \Drupal::service('plugin.manager.block');
    $block_config = [];
    $block_plugin = $block_manager->createInstance('products_block', $block_config);
    $block_build = $block_plugin->build();
    $block_content = render($block_build);

    echo $block_content;exit;
    //return array('#markup' => $block_content);
  }
  
}