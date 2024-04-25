<?php

namespace MauticPlugin\LeuchtfeuerCompanySegmentMembersWidgetBundle\Tests\IntegrationTests;

use Mautic\CoreBundle\Helper\CsvHelper;
use Mautic\CoreBundle\Translation\Translator;
use Mautic\LeadBundle\Entity\Company;
use Mautic\LeadBundle\Model\LeadModel;
use MauticPlugin\LeuchtfeuerCompanySegmentMembersWidgetBundle\Entity\CompanySegmentRepository;
use MauticPlugin\LeuchtfeuerCompanySegmentMembersWidgetBundle\Integration\Config;
use MauticPlugin\LeuchtfeuerCompanySegmentsBundle\Entity\CompanySegment;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class DashboardSubscriberTest extends KernelTestCase
{

    public CompanySegmentRepository $companySegmentRepository;
    public LeadModel $leadModel;
    public RouterInterface $router;
    public TranslatorInterface $translator;
    public Config $config;


    protected function setUp(): void{

        self::bootKernel(['environment' => 'test']);
        $entityManager = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();


        $this->translator = self::getContainer()->get(Translator::class);
        $this->leadModel = self::getContainer()->get(LeadModel::class);
        $this->router = self::getContainer()->get(Router::class);
        $this->config = self::getContainer()->get(Config::class);
        $this->companySegmentRepository = $this->createMock(CompanySegmentRepository::class);

        $this->companySegmentRepository->expects()->method('getSegmentObjectsViaListOfIDs')->willReturn('1');

        $companies = CsvHelper::csv_to_array(__DIR__.'/fakecompanydata.csv');
        $companyArray = [];
        foreach ($companies as $count => $l) {
            $company = new Company();
            foreach ($l as $col => $val) {
                if ('dateAdded' !== $col) {
                    $company->addUpdatedField($col, $val);
                } else {
                    $company->setDateAdded(new \DateTime($val));
                }
            }
            $companyArray[] = $company;
        }

        $this->companySegmentRepository->expects('1')->method('getCompanyArrayFromCompanySegments')->willReturn($companyArray);


        xdebug_break();
        //$this->companySegmentRepository = $entityManager->getRepository(CompanySegment::class, \MauticPlugin\LeuchtfeuerCompanySegmentMembersWidgetBundle\Entity\CompanySegmentRepository::class);
    }

    public function testOnWidgetDetailGenerate()
    {
        $this->translator = self::getContainer()->get(Translator::class);
    }
}

    
