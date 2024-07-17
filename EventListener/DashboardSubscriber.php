<?php

namespace MauticPlugin\LeuchtfeuerCompanyListWidgetBundle\EventListener;

use Mautic\DashboardBundle\Event\WidgetDetailEvent;
use Mautic\DashboardBundle\EventListener\DashboardSubscriber as OriginalDashboardSubscriber;
use Mautic\LeadBundle\Entity\Company;
use Mautic\LeadBundle\Model\LeadModel;
use MauticPlugin\LeuchtfeuerCompanyListWidgetBundle\Form\Type\DashboardCompanyListType;
use MauticPlugin\LeuchtfeuerCompanyListWidgetBundle\Integration\Config;
use MauticPlugin\LeuchtfeuerCompanySegmentsBundle\Entity\CompanySegment;
use MauticPlugin\LeuchtfeuerCompanySegmentsBundle\Entity\CompanySegmentRepository;
use MauticPlugin\LeuchtfeuerCompanyTagsBundle\Entity\CompanyTags;
use MauticPlugin\LeuchtfeuerCompanyTagsBundle\Entity\CompanyTagsRepository;

use function PHPUnit\Framework\throwException;

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

        $companies = $this->mergeCompanies($segmentCompanies, $tagCompanies);

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
                'mautic.widget.company.list.id',
                'mautic.widget.company.list.name',
                'mautic.widget.company.list.website',
            ],
            'bodyItems' => $items,
        ]);

        $this->finalizeWidget($event);
    }

    /**
     * @param array<CompanyTags> $companyTags
     *
     * @return array<Company>
     */
    public function getCompaniesByTag(array $companyTags): array
    {
        if (empty($companyTags)) {
            throwException(new \Mautic\IntegrationsBundle\Exception\UnexpectedValueException('No CompanyTag was passed to method getCompanyArrayFromCompanySegments'));
        }
        $companies = [];
        foreach ($companyTags as $companyTag) {
            if ($companyTag instanceof CompanyTags) {
                foreach ($companyTag->getCompanies() as $company) {
                    $companies[] = $company;
                }
            }
        }

        return array_unique($companies, SORT_REGULAR);
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

        return array_unique($companies, SORT_REGULAR);
    }

    /**
     * @param array<int|string> $selectedSegments
     *
     * @return array<Company>
     */
    private function getsCompaniesFromSelectedSegments(array $selectedSegments): array
    {
        if (!empty($selectedSegments)) {
            $companySegments                = $this->companySegmentRepository->getSegmentObjectsViaListOfIDs($selectedSegments);

            return $this->getCompanyArrayFromCompanySegments($companySegments);
        } else {
            return [];
        }
    }

    /**
     * @param array<int|string> $selectedTags
     *
     * @return array<Company>
     */
    private function getCompaniesFromSelectedTags(array $selectedTags): array
    {
        if (!empty($selectedTags)) {
            $companyTags = $this->companyTagsRepository->getTagObjectsByIds($selectedTags);

            return $this->getCompaniesByTag($companyTags);
        } else {
            return [];
        }
    }

    private function mergeCompanies($segmentCompanies, $tagCompanies)
    {
        if (!empty($segmentCompanies) && !empty($tagCompanies)) {
            return array_intersect($segmentCompanies, $tagCompanies);
        }
        else {
            $companies        = array_merge($tagCompanies, $segmentCompanies);
            return array_unique($companies);
        }
    }

    private function finalizeWidget(WidgetDetailEvent $event): void
    {
        $event->setTemplate('@MauticCore/Helper/table.html.twig');
        $event->stopPropagation();
    }
}

