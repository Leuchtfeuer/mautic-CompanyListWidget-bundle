<?php

namespace MauticPlugin\LeuchtfeuerCompanyListWidgetBundle\EventListener;

use Mautic\DashboardBundle\Event\WidgetDetailEvent;
use Mautic\DashboardBundle\EventListener\DashboardSubscriber as OriginalDashboardSubscriber;
use Mautic\LeadBundle\Entity\Company;
use Mautic\LeadBundle\Entity\CompanyRepository;
use Mautic\LeadBundle\Model\LeadModel;
use MauticPlugin\LeuchtfeuerCompanyListWidgetBundle\Form\Type\DashboardCompanyListType;
use MauticPlugin\LeuchtfeuerCompanyListWidgetBundle\Integration\Config;
use MauticPlugin\LeuchtfeuerCompanySegmentsBundle\Entity\CompanySegment;
use MauticPlugin\LeuchtfeuerCompanySegmentsBundle\Entity\CompanySegmentRepository;
use MauticPlugin\LeuchtfeuerCompanyTagsBundle\Entity\CompanyTags;
use MauticPlugin\LeuchtfeuerCompanyTagsBundle\Entity\CompanyTagsRepository;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class DashboardSubscriber extends OriginalDashboardSubscriber
{
    /**
     * Define the name of the bundle/category of the widget(s).
     *
     * @var string
     */
    protected $bundle = 'companylistwidget';

    /**
     * Define the widget(s).
     *
     * @var array<string, array<string, string>>
     */
    protected $types = [
        'company.list' => [
            'formAlias' => DashboardCompanyListType::class,
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
        protected CompanyTagsRepository $companyTagsRepository,
        protected CompanyRepository $companyRepository,
    ) {
    }

    public function onWidgetDetailGenerate(WidgetDetailEvent $event): void
    {
        if ('company.list' != $event->getType()) {
            return;
        }

        if ($event->isCached()) {
            $this->finalizeWidget($event);

            return;
        }

        $selectedSegments = $event->getWidget()->getParams()['companysegments'];
        $selectedTags     = $event->getWidget()->getParams()['companytags'];

        $segmentCompanies = $this->getsCompaniesFromSelectedSegments($selectedSegments);
        $tagCompanies     = $this->getCompaniesFromSelectedTags($selectedTags);
        $companies        = array_intersect($segmentCompanies, $tagCompanies);

        usort($companies, function ($a, $b): int {
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
                'mautic.widget.company.list.id',
                'mautic.widget.company.list.name',
                'mautic.widget.company.list.website',
            ],
            'bodyItems' => $items,
        ]);

        $this->finalizeWidget($event);
    }

    /**
     * @param array<int>|array{} $selectedSegments
     *
     * @return array<Company>
     */
    private function getsCompaniesFromSelectedSegments(array $selectedSegments): array
    {
        if (empty($selectedSegments)) {
            return $this->companyRepository->findAll();
        }

        $companySegments = $this->companySegmentRepository->getSegmentObjectsViaListOfIDs($selectedSegments);
        $companies       = $this->getCompanyArrayFromCompanySegments($companySegments);

        return $this->intersectCompanies($companies);
    }

    /**
     * @param array<CompanySegment> $companySegments
     *
     * @return array<array<Company>>
     */
    public function getCompanyArrayFromCompanySegments(array $companySegments): array
    {
        $companies = [];
        foreach ($companySegments as $companySegment) {
            $companies[] = $companySegment->getCompanies()->toArray();
        }

        return $companies;
    }

    /**
     * @param array<int>|array{} $selectedTags
     *
     * @return array<Company>
     */
    private function getCompaniesFromSelectedTags(array $selectedTags): array
    {
        if (empty($selectedTags)) {
            return $this->companyRepository->findAll();
        }

        $companyTags = $this->companyTagsRepository->getTagObjectsByIds($selectedTags);
        $companies   = $this->getCompanyArrayFromCompanyTags($companyTags);

        return $this->intersectCompanies($companies);
    }

    /**
     * @param array<CompanyTags> $companyTags
     *
     * @return array<array<Company>>
     */
    public function getCompanyArrayFromCompanyTags(array $companyTags): array
    {
        $companies = [];
        foreach ($companyTags as $companyTag) {
            $companies[] = $companyTag->getCompanies()->toArray();
        }

        return $companies;
    }

    /**
     * @param array<array<Company>> $companies
     *
     * @return array<Company>
     */
    private function intersectCompanies(array $companies): array
    {
        if (empty($companies)) {
            return [];
        }

        $intersectedCompanies = array_shift($companies);
        foreach ($companies as $companyList) {
            $intersectedCompanies = array_intersect($intersectedCompanies, $companyList);
        }

        return $intersectedCompanies;
    }

    private function finalizeWidget(WidgetDetailEvent $event): void
    {
        $event->setTemplate('@MauticCore/Helper/table.html.twig');
        $event->stopPropagation();
    }
}
