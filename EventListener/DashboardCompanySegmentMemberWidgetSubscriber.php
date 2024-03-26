<?php

namespace MauticPlugin\LeuchtfeuerCompanySegmentMembersWidgetBundlealt\EventListener;

use Mautic\DashboardBundle\Event\WidgetDetailEvent;
use Mautic\DashboardBundle\EventListener\DashboardSubscriber;
use Mautic\LeadBundle\Model\LeadModel;
use MauticPlugin\LeuchtfeuerCompanySegmentMembersWidgetBundlealt\Form\Type\DashboardCompanySegmentMembersType;
use MauticPlugin\LeuchtfeuerCompanySegmentMembersWidgetBundlealt\Integration\Config;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class DashboardCompanySegmentMemberWidgetSubscriber extends DashboardSubscriber
{
    /**
     * Define the name of the bundle/category of the widget(s).
     *
     * @var string
     */
    protected $bundle = 'lead';

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
        $limit  = $params['limit'];

        if (empty($params['limit'])) {
            $limit = round((($event->getWidget()->getHeight() - 80) / 35) - 1);
        }

        $filters = [
            'leadlist_id' => [
                'list_column_name' => 't.id',
                'value' => 1,
                ]
        ];

        $leads = $this->leadModel->getLeadList($limit, $params['dateFrom'], $params['dateTo'], $filters);
        $items    = [];

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

/*
        $row = [
            [
                'value' => 'Jonas',
                'type'  => 'link',
                'link'  => 'https://leuchtfeuer.com/digital-marketing/',
                ],
            ['value' => 'abc'],
            ['value' => 'abcd'],
            ];
*/

        $items[] = $row;
        }

        if (!$event->isCached()) {
            $event->setTemplateData([
                'headItems' => [
                    'mautic.widget.company.segment.members.title',
                    'mautic.widget.company.segment.members.spaltezwei',
                    'mautic.widget.company.segment.members.spaltedrei',
                ],
                'bodyItems' => $items,
            ]);
        }

    $event->setTemplate('@MauticCore/Helper/table.html.twig');
    $event->stopPropagation();
    }
}