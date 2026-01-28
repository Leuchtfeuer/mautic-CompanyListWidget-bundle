<?php

declare(strict_types=1);

namespace MauticPlugin\LeuchtfeuerCompanyListWidgetBundle\Tests\Fixtures;

use Doctrine\ORM\EntityManagerInterface;
use Mautic\LeadBundle\Entity\Company;
use Mautic\PluginBundle\Entity\Integration;
use Mautic\PluginBundle\Entity\Plugin;
use MauticPlugin\LeuchtfeuerCompanySegmentsBundle\Entity\CompaniesSegments;
use MauticPlugin\LeuchtfeuerCompanySegmentsBundle\Entity\CompanySegment;
use MauticPlugin\LeuchtfeuerCompanyTagsBundle\Entity\CompanyTags;

final class FixtureHelper
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function createAndEnablePlugin(): void
    {
        $plugin = new Plugin();
        $plugin->setName('Company List Widget');
        $plugin->setBundle('LeuchtfeuerCompanyListWidgetBundle');
        $this->em->persist($plugin);

        $integration = new Integration();
        $integration->setPlugin($plugin);
        $integration->setIsPublished(true);
        $integration->setName('CompanyListWidget');
        $this->em->persist($integration);
        $this->em->flush();
    }

    /**
     * @param array<string, mixed> $data
     */
    public function createCompany(string $name, array $data = []): Company
    {
        $company = new Company();
        $company->setName($name);

        if (isset($data['website']) && is_string($data['website'])) {
            $company->setWebsite($data['website']);
        }

        if (isset($data['dateAdded']) && $data['dateAdded'] instanceof \DateTime) {
            $company->setDateAdded($data['dateAdded']);
        } else {
            $company->setDateAdded(new \DateTime());
        }

        $this->em->persist($company);
        $this->em->flush();

        return $company;
    }

    public function createCompanySegment(string $name): CompanySegment
    {
        $segment = new CompanySegment();
        $segment->setName($name);
        $segment->setAlias(strtolower(str_replace(' ', '-', $name)));

        $this->em->persist($segment);
        $this->em->flush();

        return $segment;
    }

    public function createCompanyTag(string $name): CompanyTags
    {
        $tag = new CompanyTags();
        $tag->setTag($name);

        $this->em->persist($tag);
        $this->em->flush();

        return $tag;
    }

    public function addCompanyToSegment(Company $company, CompanySegment $segment): void
    {
        $companiesSegments = new CompaniesSegments();
        $companiesSegments->setCompany($company);
        $companiesSegments->setCompanySegment($segment);
        $companiesSegments->setDateAdded(new \DateTime());
        $companiesSegments->setManuallyAdded(true);

        $this->em->persist($companiesSegments);
        $this->em->flush();
    }

    public function addTagToCompany(Company $company, CompanyTags $tag): void
    {
        $tag->addCompany($company);
        $this->em->persist($tag);
        $this->em->flush();
    }
}
