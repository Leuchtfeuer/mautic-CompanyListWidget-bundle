<?php

namespace MauticPlugin\LeuchtfeuerCompanySegmentMembersWidgetBundle\Tests\Fixtures;

use DateTime;

use Doctrine\Persistence\ObjectManager;
use Mautic\CoreBundle\Helper\CsvHelper;
use Mautic\LeadBundle\Entity\Company;
use Mautic\LeadBundle\Model\CompanyModel;

class LoadCompanyData extends \Mautic\LeadBundle\DataFixtures\ORM\LoadCompanyData
{

    public function __construct(
        private CompanyModel $companyModel
    ) {
    }
    public function load(ObjectManager $manager): void
    {
        $companies = CsvHelper::csv_to_array(__DIR__.'/fakecompanydata.csv');

        foreach ($companies as $count => $l) {
            $company = new Company();
            $startDate = new DateTime("2022-01-01");
            $endDate = new DateTime("2022-12-31");
            $randomDateTime = (new DateTime())->setTimestamp(mt_rand($startDate->getTimestamp(), $endDate->getTimestamp()));
            $company->setDateAdded($randomDateTime);
            foreach ($l as $col => $val) {
                $company->addUpdatedField($col, $val);
            }
            $this->companyModel->saveEntity($company);

            $this->setReference('company-'.$count, $company);
        }
    }
    public function getOrder()
    {
        return 11;
    }

}