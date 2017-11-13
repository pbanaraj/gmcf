<?php

/**
 * @file
 * Contains \Drupal\customise\Plugin\Block\quoteBlock.
 */

namespace Drupal\customise\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'quoteBlock' block.
 *
 * @Block(
 *  id = "quote_block",
 *  admin_label = @Translation("Quote block"),
 * )
 */
class quoteBlock extends BlockBase {


  /**
   * {@inheritdoc}
   */
  public function build() {
    $block_data ="";

      
        
        	
        $loan_products = array(
                               "1"=>"30 year fixed",
                               "5"=>"25 year fixed",
                               "2"=>"20 year fixed",
                               "3"=>"15 year fixed",
                               "4"=>"10 year fixed",
                               "11"=>"10/1 ARM",
                               "12"=>"7/1 ARM",
                               "13"=>"5/1 ARM",
                               "14"=>"3/1 ARM",
                              // "15"=>"2/1 ARM"
                             );
	
        $credit_scores = array(
                               "740"=>"Excellent ( >=740 )",
                               "729"=>"Very Good: 720 – 739",
                               "709"=>"Good: 700 – 719",
                               "689"=>"Above Average: 680 – 699",
                               "659"=>"Average: 640 – 679",
                               "629"=>"Fair: 620 – 639",
                               "559"=>"Needs Some Work: 580 – 619",
                               "579"=>"Needs Much Work ( <=579 )",
                             );
        
        $property_types = array("SingleFamily"=>"Single Family Home",
                                "TownHome"=>"TownHome",
                                "Condo"=>"Condominium",
                                "HiRiseCondo"=>"Hi-Rise Condo",
                                "DetachedCondo"=>"Detached Condo",
                                "PuDs"=>"PUD",
                                "Manufactured"=>"Manufactured Home",
                                "Leasehold"=>"Leasehold",
                                "MultiUnit"=>"Multi Family Home"
                                );
        
	$occupency_types = array(
                                 "PrimaryResidence"=>"Primary Home",
                                 "SecondHome"=>"Second Home",
                                 "Investment"=>"Investment Property"
                                );
        
        
    return array(
        '#theme' => 'quick_rate_quote_block', 
        '#block_data' => array('loan_products'=>$loan_products,'credit_scores'=>$credit_scores,'property_types'=>$property_types,'occupency_types'=>$occupency_types), 
     );
  }

}
