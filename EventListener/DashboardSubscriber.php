<?php

namespace MauticPlugin\LeuchtfeuerCompanySegmentMembersWidgetBundle\EventListener;

use Mautic\DashboardBundle\Event\WidgetDetailEvent;
use Mautic\DashboardBundle\EventListener\DashboardSubscriber as OriginalDashboardSubscriber;
use Mautic\LeadBundle\Model\LeadModel;
use MauticPlugin\LeuchtfeuerCompanySegmentMembersWidgetBundle\Entity\CompanySegmentRepository;
use MauticPlugin\LeuchtfeuerCompanySegmentMembersWidgetBundle\Form\Type\DashboardCompanySegmentMembersType;
use MauticPlugin\LeuchtfeuerCompanySegmentMembersWidgetBundle\Integration\Config;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class DashboardSubscriber extends OriginalDashboardSubscriber
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
     * @var array<string, array<string, string>>
     */
    protected $types = [
        'company.segment.members' => [
            'formAlias' => DashboardCompanySegmentMembersType::class,
        ],
    ];

    /**
     * Define permissions to see those widgets.
     *
     * @var array<string>
     *
     * @TODO
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

        if (!$event->isCached()) {
            $arrayWithIDsOfSelectedSegments = $event->getWidget()->getParams()['companysegments'];
            $companySegments                = $this->companySegmentRepository->getSegmentObjectsViaListOfIDs($arrayWithIDsOfSelectedSegments);
            $companies                      = $this->companySegmentRepository->getCompanyArrayFromCompanySegments($companySegments);

            usort($companies, function ($a, $b) {
                return $b->getDateAdded() <=> $a->getDateAdded();
            });

            $limit            = intval(round((($event->getWidget()->getHeight() - 80) / 35) - 1));
            $companiesReduced = array_slice($companies, 0, $limit);
            $items            = [];

            foreach ($companiesReduced as $company) {
                $companyId        = $company->getId();
                $companyName      = $company->getName();
                $companyWebsite   = $company->getWebsite();
                $companyMauticUrl = $this->router->generate('mautic_company_action', ['objectAction' => 'view', 'objectId' => $companyId]);
                $nameType         = 'link';
                $websiteType      = (null !== $companyWebsite) ? 'link' : 'text';

                $row = [
                    ['value' => $companyId],
                    [
                        'value'    => $companyName,
                        'type'     => $nameType,
                        'link'     => $companyMauticUrl,
                        'external' => true,
                    ],
                    [
                        'value'    => $companyWebsite,
                        'type'     => $websiteType,
                        'link'     => $companyWebsite,
                        'external' => true,
                    ],
                ];

                $items[] = $row;
            }

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
        //$event->setTemplate('@LeuchtfeuerCompanySegmentMembersWidget/companysegmentmemberstable.html.twig');
        $event->stopPropagation();
    }
}
