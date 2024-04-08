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
                if ($col !== "dateAdded") {
                    $company->addUpdatedField($col, $val);
                }
                else {
                    $company->setDateAdded(new \DateTime($val));
                }
            }
            $this->companyModel->saveEntity($company);
            $this->setReference('company-' . $count, $company);
        }

        $companySegments = CsvHelper::csv_to_array(__DIR__.'/fakecompanysegmentdata.csv');
        foreach ($companySegments as $segmentData) {
            $companySegment = new CompanySegment();
            $segmentName = $segmentData["name"];
            $companySegment->setName($segmentName);
            $companySegment->setAlias($segmentData["alias"]);
            $companySegment->setIsPublished($segmentData["is_published"]);
            if ($segmentName == "CompanysegmentNr1"){
                $companies = $this->companyRepository->getCompaniesByUniqueFields($uniqueFieldsWithData = ["companyname" => "Company A"]);
                $companies[] = $this->companyRepository->getCompaniesByUniqueFields($uniqueFieldsWithData = ["companyname" => "Company B"])[0];
            } elseif ($segmentName == "CompanysegmentNr2"){
                $companies = [];
            } elseif ($segmentName == "CompanysegmentNr3"){
                $companies = $this->companyRepository->getCompaniesByUniqueFields($uniqueFieldsWithData = ["companyname" => "Company B"]);
                $companies[] = $this->companyRepository->getCompaniesByUniqueFields($uniqueFieldsWithData = ["companyname" => "Company D"])[0];
                $companies[] = $this->companyRepository->getCompaniesByUniqueFields($uniqueFieldsWithData = ["companyname" => "Company C"])[0];
            } elseif ($segmentName == "CompanysegmentNr4"){
                $companies = $this->companyRepository->getCompaniesByUniqueFields($uniqueFieldsWithData = ["companyname" => "Company D"]);
                $companies[] = $this->companyRepository->getCompaniesByUniqueFields($uniqueFieldsWithData = ["companyname" => "Company E"])[0];
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