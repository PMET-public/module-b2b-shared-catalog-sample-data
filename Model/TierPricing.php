<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace MagentoEse\B2BSharedCatalogSampleData\Model;

use Magento\Framework\Setup\SampleData\Context as SampleDataContext;

/**
 * Class Product
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class TierPricing
{
    /**
     * @var \Magento\Framework\Setup\SampleData\FixtureManager
     */
    protected $fixtureManager;

    /**
     * @var \Magento\Framework\File\Csv
     */
    protected $csvReader;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * 
     * @var MagentoEse\DataInstall\Model\Import\Importer\Importer
     */
    protected $importerModel;

    /**
     * Product constructor.
     * @param SampleDataContext $sampleDataContext
     * @param \Magento\Framework\ObjectManagerInterface  $objectManager
     */

    public function __construct(
        SampleDataContext $sampleDataContext,
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->fixtureManager = $sampleDataContext->getFixtureManager();
        $this->csvReader = $sampleDataContext->getCsvReader();
        $this->objectManager=$objectManager;
    }

    /**
     * @param array $productFixtures
     * @throws \Exception
     */
    public function install(array $productFixtures)
    {

        foreach ($productFixtures as $fileName) {
            $fileName = $this->fixtureManager->getFixture($fileName);
            if (!file_exists($fileName)) {
                throw new Exception('File not found: '.$fileName);
            }

            $rows = $this->csvReader->getData($fileName);
            $header = array_shift($rows);

            foreach ($rows as $row) {
                $_productsArray[] = array_combine($header, $row);
            }
            $this->importerModel = $this->objectManager->create('MagentoEse\DataInstall\Model\Import\Importer\Importer');
            $this->importerModel->setEntityCode('advanced_pricing');
            $this->importerModel->setValidationStrategy('validation-skip-errors');
            try {
                $this->importerModel->processImport($_productsArray);
            } catch (\Exception $e) {
                print_r($e->getMessage());
            }

            print_r($this->importerModel->getLogTrace());
            print_r($this->importerModel->getErrorMessages());
            unset ($_productsArray);
        }

    }

}
