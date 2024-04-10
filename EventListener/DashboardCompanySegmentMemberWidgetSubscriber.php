<?php

namespace MauticPlugin\LeuchtfeuerCompanySegmentMembersWidgetBundle\EventListener;

use Mautic\DashboardBundle\Event\WidgetDetailEvent;
use Mautic\DashboardBundle\EventListener\DashboardSubscriber;
use Mautic\LeadBundle\Model\LeadModel;
use MauticPlugin\LeuchtfeuerCompanySegmentMembersWidgetBundle\Form\Type\DashboardCompanySegmentMembersType;
use MauticPlugin\LeuchtfeuerCompanySegmentMembersWidgetBundle\Integration\Config;
use MauticPlugin\LeuchtfeuerCompanySegmentMembersWidgetBundle\Entity\CompanySegmentRepository;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class DashboardCompanySegmentMemberWidgetSubscriber extends DashboardSubscriber
{
    /**
     * Define the name of the bundle/category of the widget(s).
     *
     * @var string
     */
    protected $bundle = 'companysegmentwidget';

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

        if ('company.segment.members' != $event->getType()) {
            return;
        }

        $params = $event->getWidget()->getParams();

        $items    = [];
        $segmentArray = $params['companysegments'];
        $companySegments = $this->companySegmentRepository->getSegmentObjectsViaListOfIDs($segmentArray);
        $companies = $this->companySegmentRepository->getCompanyArrayFromCompanySegments($companySegments);

        usort($companies, function($a, $b) {
            return $b->getDateAdded() <=> $a->getDateAdded();
        });

        /*
        if (!empty($params['limit'])) {
            $limit = $params['limit'];
        }
        else{
            $limit = round((($event->getWidget()->getHeight() - 80) / 35) - 1);
        }
        */
        $limit = round((($event->getWidget()->getHeight() - 80) / 35) - 1);
        $limitedCompanies = array_slice($companies, 0, $limit);

                    foreach ($limitedCompanies as $company) {
                        $companyId = $company->getId();
                        $companyName = $company->getName();
                        $companyWebsite = $company->getWebsite();
                        $companyUrl = ($companyId !== null) ? $this->router->generate('mautic_company_action', ['objectAction' => 'view', 'objectId' => $companyId]) : '';
                        $nameType    = ($companyId !== null) ? 'link' : 'text';
                        $websiteType    = ($companyWebsite !== null) ? 'link' : 'text';


                        $row     = [
                            ['value' => $companyId],
                            [
                                'value' => $companyName,
                                'type'  => $nameType,
                                'link'  => $companyUrl,
                                'external' => true,

                            ],
                            [
                                'value' => $companyWebsite,
                                'type'  => $websiteType,
                                'link'  => $companyWebsite,
                                'external' => true,
                            ],

                        ];

                        $items[] = $row;
                    }


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

}
