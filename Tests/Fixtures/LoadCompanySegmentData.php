<?php

namespace MauticPlugin\LeuchtfeuerCompanySegmentMembersWidgetBundle\Tests\Fixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Mautic\CoreBundle\Helper\CsvHelper;
use Mautic\LeadBundle\Entity\Company;
use Mautic\LeadBundle\Entity\CompanyRepository;
use Mautic\LeadBundle\Model\CompanyModel;
use MauticPlugin\LeuchtfeuerCompanySegmentsBundle\Entity\CompanySegment;
use MauticPlugin\LeuchtfeuerCompanySegmentsBundle\Model\CompanySegmentModel;

class LoadCompanySegmentData extends AbstractFixture implements OrderedFixtureInterface
{
    public function __construct(
        private CompanySegmentModel $companySegmentModel,
        private CompanyModel $companyModel,
        private CompanyRepository $companyRepository,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $companies = CsvHelper::csv_to_array(__DIR__.'/fakecompanydata.csv');
        foreach ($companies as $count => $l) {
            $company = new Company();
            foreach ($l as $col => $val) {
                if ('dateAdded' !== $col) {
                    $company->addUpdatedField($col, $val);
                } else {
                    $company->setDateAdded(new \DateTime($val));
                }
            }
            $this->companyModel->saveEntity($company);
            $this->setReference('company-'.$count, $company);
        }

        $companySegments = CsvHelper::csv_to_array(__DIR__.'/fakecompanysegmentdata.csv');
        foreach ($companySegments as $segmentData) {
            $companySegment = new CompanySegment();
            $segmentName    = $segmentData['name'];
            $companySegment->setName($segmentName);
            $companySegment->setAlias($segmentData['alias']);
            $companySegment->setIsPublished($segmentData['is_published']);
            if ('CompanysegmentNr1' == $segmentName) {
                $companies   = $this->companyRepository->getCompaniesByUniqueFields($uniqueFieldsWithData = ['companyname' => 'Company A']);
                $companies[] = $this->companyRepository->getCompaniesByUniqueFields($uniqueFieldsWithData = ['companyname' => 'Company B'])[0];
            } elseif ('CompanysegmentNr2' == $segmentName) {
                $companies = [];
            } elseif ('CompanysegmentNr3' == $segmentName) {
                $companies   = $this->companyRepository->getCompaniesByUniqueFields($uniqueFieldsWithData = ['companyname' => 'Company B']);
                $companies[] = $this->companyRepository->getCompaniesByUniqueFields($uniqueFieldsWithData = ['companyname' => 'Company D'])[0];
                $companies[] = $this->companyRepository->getCompaniesByUniqueFields($uniqueFieldsWithData = ['companyname' => 'Company C'])[0];
            } elseif ('CompanysegmentNr4' == $segmentName) {
                $companies   = $this->companyRepository->getCompaniesByUniqueFields($uniqueFieldsWithData = ['companyname' => 'Company D']);
                $companies[] = $this->companyRepository->getCompaniesByUniqueFields($uniqueFieldsWithData = ['companyname' => 'Company E'])[0];
            }
            foreach ($companies as $company) {
                $companySegment->addCompanies($company);
            }

            $this->companySegmentModel->saveEntity($companySegment);
            $this->setReference('companysegment-'.$segmentName, $companySegment);
        }
    }

    public function getOrder()
    {
        return 8;
    }
}
