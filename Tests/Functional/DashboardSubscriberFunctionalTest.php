<?php

declare(strict_types=1);

namespace MauticPlugin\LeuchtfeuerCompanyListWidgetBundle\Tests\Functional;

use Mautic\CoreBundle\Test\MauticMysqlTestCase;
use Mautic\DashboardBundle\Entity\Widget;
use Mautic\DashboardBundle\Event\WidgetDetailEvent;
use MauticPlugin\LeuchtfeuerCompanyListWidgetBundle\EventListener\DashboardSubscriber;
use MauticPlugin\LeuchtfeuerCompanyListWidgetBundle\Tests\Fixtures\FixtureHelper;
use PHPUnit\Framework\Assert;

class DashboardSubscriberFunctionalTest extends MauticMysqlTestCase
{
    protected $useCleanupRollback = false;

    private FixtureHelper $fixtureHelper;
    private DashboardSubscriber $subscriber;

    protected function setUp(): void
    {
        parent::setUp();

        $this->fixtureHelper = new FixtureHelper($this->em);
        $this->fixtureHelper->createAndEnablePlugin();

        $subscriber = static::getContainer()->get(DashboardSubscriber::class);
        assert($subscriber instanceof DashboardSubscriber);
        $this->subscriber = $subscriber;
    }

    public function testWidgetShowsAllCompaniesWhenNoFiltersApplied(): void
    {
        $company1 = $this->fixtureHelper->createCompany('Company A');
        $company2 = $this->fixtureHelper->createCompany('Company B');
        $company3 = $this->fixtureHelper->createCompany('Company C');

        $event = $this->createWidgetEvent([]);
        $this->subscriber->onWidgetDetailGenerate($event);

        $templateData = $event->getTemplateData();
        Assert::assertIsArray($templateData);
        Assert::assertArrayHasKey('bodyItems', $templateData);
        $bodyItems = $templateData['bodyItems'];
        Assert::assertIsArray($bodyItems);
        Assert::assertCount(3, $bodyItems);

        $companyIds = $this->extractCompanyIds($bodyItems);
        Assert::assertContains($company1->getId(), $companyIds);
        Assert::assertContains($company2->getId(), $companyIds);
        Assert::assertContains($company3->getId(), $companyIds);
    }

    public function testWidgetShowsOnlyCompaniesFromSelectedSegment(): void
    {
        $segment1 = $this->fixtureHelper->createCompanySegment('VIP Customers');
        $segment2 = $this->fixtureHelper->createCompanySegment('Standard');

        $company1 = $this->fixtureHelper->createCompany('Company A');
        $company2 = $this->fixtureHelper->createCompany('Company B');
        $company3 = $this->fixtureHelper->createCompany('Company C');

        $this->fixtureHelper->addCompanyToSegment($company1, $segment1);
        $this->fixtureHelper->addCompanyToSegment($company2, $segment1);
        $this->fixtureHelper->addCompanyToSegment($company3, $segment2);

        $event = $this->createWidgetEvent(['companysegments' => [$segment1->getId()]]);
        $this->subscriber->onWidgetDetailGenerate($event);

        $templateData = $event->getTemplateData();
        Assert::assertIsArray($templateData);
        Assert::assertArrayHasKey('bodyItems', $templateData);
        $bodyItems = $templateData['bodyItems'];
        Assert::assertIsArray($bodyItems);
        Assert::assertCount(2, $bodyItems);

        $companyIds = $this->extractCompanyIds($bodyItems);
        Assert::assertContains($company1->getId(), $companyIds);
        Assert::assertContains($company2->getId(), $companyIds);
        Assert::assertNotContains($company3->getId(), $companyIds);
    }

    public function testWidgetShowsOnlyCompaniesWithSelectedTag(): void
    {
        $tag1 = $this->fixtureHelper->createCompanyTag('Partner');
        $tag2 = $this->fixtureHelper->createCompanyTag('Customer');

        $company1 = $this->fixtureHelper->createCompany('Company A');
        $company2 = $this->fixtureHelper->createCompany('Company B');

        $this->fixtureHelper->addTagToCompany($company1, $tag1);
        $this->fixtureHelper->addTagToCompany($company2, $tag2);

        $event = $this->createWidgetEvent(['companytags' => [$tag1->getId()]]);
        $this->subscriber->onWidgetDetailGenerate($event);

        $templateData = $event->getTemplateData();
        Assert::assertIsArray($templateData);
        Assert::assertArrayHasKey('bodyItems', $templateData);
        $bodyItems = $templateData['bodyItems'];
        Assert::assertIsArray($bodyItems);
        Assert::assertCount(1, $bodyItems);

        $companyIds = $this->extractCompanyIds($bodyItems);
        Assert::assertContains($company1->getId(), $companyIds);
        Assert::assertNotContains($company2->getId(), $companyIds);
    }

    public function testWidgetShowsOnlyCompaniesInAllSelectedSegments(): void
    {
        // Arrange
        $segment1 = $this->fixtureHelper->createCompanySegment('Segment 1');
        $segment2 = $this->fixtureHelper->createCompanySegment('Segment 2');

        $company1 = $this->fixtureHelper->createCompany('Company A'); // in both
        $company2 = $this->fixtureHelper->createCompany('Company B'); // only in segment1

        $this->fixtureHelper->addCompanyToSegment($company1, $segment1);
        $this->fixtureHelper->addCompanyToSegment($company1, $segment2);
        $this->fixtureHelper->addCompanyToSegment($company2, $segment1);

        $event = $this->createWidgetEvent([
            'companysegments' => [$segment1->getId(), $segment2->getId()],
        ]);
        $this->subscriber->onWidgetDetailGenerate($event);

        $templateData = $event->getTemplateData();
        Assert::assertIsArray($templateData);
        Assert::assertArrayHasKey('bodyItems', $templateData);
        $bodyItems = $templateData['bodyItems'];
        Assert::assertIsArray($bodyItems);
        Assert::assertCount(1, $bodyItems);

        $companyIds = $this->extractCompanyIds($bodyItems);
        Assert::assertContains($company1->getId(), $companyIds);
        Assert::assertNotContains($company2->getId(), $companyIds);
    }

    public function testWidgetShowsOnlyCompaniesWithAllSelectedTags(): void
    {
        $tag1 = $this->fixtureHelper->createCompanyTag('Tag 1');
        $tag2 = $this->fixtureHelper->createCompanyTag('Tag 2');

        $company1 = $this->fixtureHelper->createCompany('Company A'); // both tags
        $company2 = $this->fixtureHelper->createCompany('Company B'); // only tag1

        $this->fixtureHelper->addTagToCompany($company1, $tag1);
        $this->fixtureHelper->addTagToCompany($company1, $tag2);
        $this->fixtureHelper->addTagToCompany($company2, $tag1);

        $event = $this->createWidgetEvent([
            'companytags' => [$tag1->getId(), $tag2->getId()],
        ]);
        $this->subscriber->onWidgetDetailGenerate($event);

        $templateData = $event->getTemplateData();
        Assert::assertIsArray($templateData);
        Assert::assertArrayHasKey('bodyItems', $templateData);
        $bodyItems = $templateData['bodyItems'];
        Assert::assertIsArray($bodyItems);
        Assert::assertCount(1, $bodyItems);

        $companyIds = $this->extractCompanyIds($bodyItems);
        Assert::assertContains($company1->getId(), $companyIds);
        Assert::assertNotContains($company2->getId(), $companyIds);
    }

    public function testWidgetShowsCompaniesMatchingBothSegmentAndTag(): void
    {
        // Arrange
        $segment = $this->fixtureHelper->createCompanySegment('VIP');
        $tag     = $this->fixtureHelper->createCompanyTag('Partner');

        $company1 = $this->fixtureHelper->createCompany('Company A'); // VIP + Partner
        $company2 = $this->fixtureHelper->createCompany('Company B'); // only VIP
        $company3 = $this->fixtureHelper->createCompany('Company C'); // only Partner

        $this->fixtureHelper->addCompanyToSegment($company1, $segment);
        $this->fixtureHelper->addCompanyToSegment($company2, $segment);
        $this->fixtureHelper->addTagToCompany($company1, $tag);
        $this->fixtureHelper->addTagToCompany($company3, $tag);

        $event = $this->createWidgetEvent([
            'companysegments' => [$segment->getId()],
            'companytags'     => [$tag->getId()],
        ]);
        $this->subscriber->onWidgetDetailGenerate($event);

        $templateData = $event->getTemplateData();
        Assert::assertIsArray($templateData);
        Assert::assertArrayHasKey('bodyItems', $templateData);
        $bodyItems = $templateData['bodyItems'];
        Assert::assertIsArray($bodyItems);
        Assert::assertCount(1, $bodyItems);

        $companyIds = $this->extractCompanyIds($bodyItems);
        Assert::assertContains($company1->getId(), $companyIds);
        Assert::assertNotContains($company2->getId(), $companyIds);
        Assert::assertNotContains($company3->getId(), $companyIds);
    }

    /**
     * @param array<string, mixed> $params
     */
    private function createWidgetEvent(array $params = [], int $height = 300): WidgetDetailEvent
    {
        $widget = new Widget();
        $widget->setType('company.list');
        $widget->setParams($params);
        $widget->setHeight($height);

        $translator = static::getContainer()->get('translator');
        assert($translator instanceof \Symfony\Contracts\Translation\TranslatorInterface);

        $security = static::getContainer()->get('mautic.security');
        assert($security instanceof \Mautic\CoreBundle\Security\Permissions\CorePermissions);

        $cacheProvider = static::getContainer()->get('mautic.cache.provider');
        assert($cacheProvider instanceof \Mautic\CacheBundle\Cache\CacheProvider);

        return new WidgetDetailEvent($translator, $security, $widget, $cacheProvider);
    }

    /**
     * Extract company IDs from bodyItems array.
     *
     * @param array<mixed> $bodyItems
     *
     * @return list<int>
     */
    private function extractCompanyIds(array $bodyItems): array
    {
        $companyIds = [];
        foreach ($bodyItems as $item) {
            Assert::assertIsArray($item);
            Assert::assertArrayHasKey(0, $item);
            Assert::assertIsArray($item[0]);
            Assert::assertArrayHasKey('value', $item[0]);
            $value = $item[0]['value'];
            Assert::assertIsInt($value);
            $companyIds[] = $value;
        }

        return $companyIds;
    }
}
