<?php

namespace MauticPlugin\LeuchtfeuerCompanySegmentMembersWidgetBundle\Entity;


use Doctrine\Persistence\ManagerRegistry;
use Mautic\CoreBundle\Entity\CommonRepository;
use Mautic\LeadBundle\Entity\Company;
use MauticPlugin\LeuchtfeuerCompanySegmentsBundle\Entity\CompanySegment;

use function PHPUnit\Framework\throwException;

/**
 * @template T of object
 *
 * @extends CommonRepository<T>
 */
class CompanySegmentRepository extends CommonRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CompanySegment::class);
    }

    /**
     * @param array<mixed> $ids
     *
     * @return array<CompanySegment>
     */
    public function getSegmentObjectsViaListOfIDs(array $ids): array
    {
        $q = $this->getEntityManager()->createQueryBuilder()
            ->from(CompanySegment::class, 'cs', 'cs.id');

        $q->select('cs')
            ->andWhere($q->expr()->eq('cs.isPublished', ':true'))
            ->setParameter('true', true, 'boolean');

        if (!empty($ids)) {
            $q->andWhere($q->expr()->in('cs.id', $ids));
        }

        return $q->getQuery()->getResult();
    }

    /**
     * @param array<CompanySegment> $companySegments
     *
     * @return array<Company>
     */
    public function getCompanyArrayFromCompanySegments(array $companySegments): array
    {
        if (empty($companySegments)) {
            throwException(new \Mautic\IntegrationsBundle\Exception\UnexpectedValueException('No CompanySegment was passed to method getCompanyArrayFromCompanySegments'));
        }
        $companies = [];
        foreach ($companySegments as $companySegment) {
            if ($companySegment instanceof CompanySegment) {
                foreach ($companySegment->getCompanies() as $company) {
                    $companies[] = $company;
                }
            }
        }
        //test

        return array_unique($companies, SORT_REGULAR);
    }
}
