<?php

namespace MauticPlugin\LeuchtfeuerCompanySegmentMembersWidgetBundle\Tests\Fixtures;

use Doctrine\Persistence\ObjectManager;
use Mautic\CoreBundle\Helper\CsvHelper;
use Mautic\LeadBundle\Entity\Company;
use Mautic\LeadBundle\Model\CompanyModel;
use MauticPlugin\LeuchtfeuerCompanySegmentsBundle\Entity\CompanySegment;
use MauticPlugin\LeuchtfeuerCompanySegmentsBundle\Model\CompanySegmentModel;

class LoadCompanysegmentData
{
    public function __construct(
        private CompanySegmentModel $companySegmentModel
    ) {
    }
    public function load(ObjectManager $manager): void
    {
        $companySegments = CsvHelper::csv_to_array(__DIR__.'/fakecompanydata.csv');

        foreach ($companySegments as $count => $l) {
            $companySegment = new CompanySegment();
            foreach ($l as $col => $val) {
                $companySegment->addUpdatedField($col, $val);
            }
            $this->companySegmentModel->saveEntity($companySegment);

            $this->setReference('company-'.$count, $companySegment);
        }
    }
    public function getOrder()
    {
        return 11;
    }
}