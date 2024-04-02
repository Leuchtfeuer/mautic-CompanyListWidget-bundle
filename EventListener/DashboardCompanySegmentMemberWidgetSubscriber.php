<?php

namespace MauticPlugin\LeuchtfeuerCompanySegmentMembersWidgetBundle\EventListener;

use Mautic\DashboardBundle\Event\WidgetDetailEvent;
use Mautic\DashboardBundle\EventListener\DashboardSubscriber;
use Mautic\LeadBundle\Model\LeadModel;
use MauticPlugin\LeuchtfeuerCompanySegmentMembersWidgetBundle\Form\Type\DashboardCompanySegmentMembersType;
use MauticPlugin\LeuchtfeuerCompanySegmentMembersWidgetBundle\Integration\Config;
use MauticPlugin\LeuchtfeuerCompanySegmentsBundle\Entity\CompanySegment;
use MauticPlugin\LeuchtfeuerCompanySegmentsBundle\Entity\CompanySegmentRepository;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class DashboardCompanySegmentMemberWidgetSubscriber extends DashboardSubscriber
{
    /**
     * Define the name of the bundle/category of the widget(s).
     *
     * @var string
     */
    protected $bundle = 'company';

    /**
     * Define the widget(s).
     *
     * @var array
     */
    protected $types = [
        'company.segment.members' => [
            'formAlias' => DashboardCompanySegmentMembersType::class,
        ],
    ];

    /**
     * Define permissions to see those widgets.
     *
     * @var array
     */
    protected $permissions = [
        'page:pages:viewown',
        'page:pages:viewother',
    ];

    public function __construct(
        protected LeadModel $leadModel,
        protected RouterInterface $router,
        protected TranslatorInterface $translator,
        protected Config $config,
        protected CompanySegmentRepository $companySegmentRepository,
    ) {
    }

    public function onWidgetDetailGenerate(WidgetDetailEvent $event): void
    {

        if (!$this->config->isPublished()) {

            return;
        }

        if ('company.segment.members' != $event->getType()) {
            return;
        }

        $params = $event->getWidget()->getParams();




        $items    = [];
        $segmentArray = $params['companysegments'];
        $companySegments = $this->companySegmentRepository->getSegmentObjectsViaListOfIDs($segmentArray);
        $companies = $this->getCompanyArrayFromCompanySegments($companySegments);

        //companies sortieren
        usort($companies, function($a, $b) {
            return $a->getDateAdded() <=> $b->getDateAdded();
        });

        //array auf limit reduzieren
        if (!empty($params['limit'])) {
            $limit = $params['limit'];
        }
        else{
            $limit = round((($event->getWidget()->getHeight() - 80) / 35) - 1);
        }
        $limitedCompanies = array_slice($companies, 0, $limit);

                    foreach ($limitedCompanies as $company) {
                        $testDateAdded = $company->getDateAdded();
                        $companyId = $company->getId();
                        $companyName = $company->getName();
                        $companyWebsite = $company->getWebsite();
                        $companyDataAdded = $company->getDateAdded();
                        $companyUrl = ($companyId !== null) ? $this->router->generate('mautic_company_action', ['objectAction' => 'view', 'objectId' => $companyId]) : '';
                        $nameType    = ($companyId !== null) ? 'link' : 'text';
                        $websiteType    = ($companyWebsite !== null) ? 'link' : 'text';


                        $row     = [
                            ['value' => $companyId],
                            [
                                'value' => $companyName,
                                'type'  => $nameType,
                                'link'  => $companyUrl,
                            ],
                            [
                                'value' => $companyWebsite,
                                'type'  => $websiteType,
                                'link'  => $companyWebsite,
                            ],

                        ];

                        $items[] = $row;
                    }


/*
        if (empty($leads)) {
            $leads[] = [
                'name' => $this->translator->trans('mautic.report.report.noresults'),
            ];
        }

        // Build table rows with links
        foreach ($leads as &$lead) {
            $leadUrl = isset($lead['id']) ? $this->router->generate('mautic_contact_action', ['objectAction' => 'view', 'objectId' => $lead['id']]) : '';
            $type    = isset($lead['id']) ? 'link' : 'text';
            $row     = [
                [
                    'value' => $lead['name'],
                    'type'  => $type,
                    'link'  => $leadUrl,
                ],
                ['value' => 'abc'],
                ['value' => 'abcd'],
            ];

        $items[] = $row;
        }
*/
        if (!$event->isCached()) {
            $event->setTemplateData([
                'headItems' => [
                    'mautic.widget.company.segment.members.id',
                    'mautic.widget.company.segment.members.name',
                    'mautic.widget.company.segment.members.website',
                ],
                'bodyItems' => $items,
            ]);
        }

    $event->setTemplate('@MauticCore/Helper/table.html.twig');
    $event->stopPropagation();
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