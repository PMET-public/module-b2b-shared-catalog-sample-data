<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace MagentoEse\B2BSharedCatalogSampleData\Model;

use Magento\Framework\Setup\SampleData\Context as SampleDataContext;


 class Related {

     /**
      * @var \Magento\TargetRule\Model\RuleFactory
      */
     protected $rule;

     /**
      * @var SampleDataContext
      */
     protected $sampleDataContext;

     /**
      * 
      * @var FixtureManager
      */
      protected $fixtureManager;
     
      /**
       * 
       * @var Csv
       */
      protected $csvReader;

     /**
      * Related constructor.
      * @param SampleDataContext $sampleDataContext
      * @param \Magento\TargetRule\Model\RuleFactory $rule
      */
     public function __construct(
         SampleDataContext $sampleDataContext,
         \Magento\TargetRule\Model\RuleFactory $rule
     ) {
         $this->fixtureManager = $sampleDataContext->getFixtureManager();
         $this->csvReader = $sampleDataContext->getCsvReader();
         $this->rule = $rule;
     }

     /**
      * @param array $fixtures
      */
     public function install(array $fixtures)
     {

         foreach ($fixtures as $fileName) {
             $fileName = $this->fixtureManager->getFixture($fileName);
             if (!file_exists($fileName)) {
                 throw new Exception('File not found: '.$fileName);
             }
             $rows = $this->csvReader->getData($fileName);
             $header = array_shift($rows);
             foreach ($rows as $row) {
                 $data = [];
                 foreach ($row as $key => $value) {
                     $data[$header[$key]] = $value;
                 }
                 $rule = $this->rule->create();
                 $rule->setName($data['name']);
                 $rule->setFromDate($data['from_date']);
                 $rule->setToDate($data['to_date']);
                 $rule->setIsActive($data['is_active']);
                 $rule->setSortOrder($data['sort_order']);
                 switch ($data['type']) {
                     case 'upsell':
                         $applyTo = 2;
                         break;
                     case 'crosssell':
                         $applyTo = 3;
                         break;
                     default:
                         //default to related
                         $applyTo = 1;
                 }
                 $rule->setApplyTo($applyTo);
                 //# of products to show
                 $rule->setPositionsLimit($data['product_limit']);
                 $rule->setConditionsSerialized($data['conditions']);
                 $rule->setActionsSerialized($data['actions']);
                 $rule->save();
             }

         }
     }
 }
