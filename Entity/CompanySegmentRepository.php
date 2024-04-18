<?php

namespace MauticPlugin\LeuchtfeuerCompanySegmentMembersWidgetBundle\Entity;

use Mautic\CoreBundle\Entity\CommonRepository;
use Mautic\UserBundle\Entity\User;
use MauticPlugin\LeuchtfeuerCompanySegmentsBundle\Entity\CompanySegment;
use Doctrine\Persistence\ManagerRegistry;


class CompanySegmentRepository extends CommonRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CompanySegment::class);
    }
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

    public function getCompanyArrayFromCompanySegments(array $companySegments){
        if (empty($companySegments)) {
            return;
        }
        $companies = [];
        foreach ($companySegments as $companySegment) {
            if ($companySegment instanceof CompanySegment) {
                foreach ($companySegment->getCompanies() as $company) {
                    $companies[] = $company;
                }
            }
        }
        return array_unique($companies, SORT_REGULAR);
    }

}