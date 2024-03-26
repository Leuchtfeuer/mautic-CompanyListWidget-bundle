<?php

declare(strict_types=1);

namespace MauticPlugin\LeuchtfeuerCompanySegmentMembersWidgetBundlealt\Integration\Support;

use Mautic\IntegrationsBundle\Integration\DefaultConfigFormTrait;
use Mautic\IntegrationsBundle\Integration\Interfaces\ConfigFormInterface;
use MauticPlugin\LeuchtfeuerCompanySegmentMembersWidgetBundlealt\Form\Type\LeuchtfeuerCompanySegmentMembersWidgetConfigType;
use MauticPlugin\LeuchtfeuerCompanySegmentMembersWidgetBundlealt\Integration\LeuchtfeuerCompanySegmentMembersWidgetIntegration;

class ConfigSupport extends LeuchtfeuerCompanySegmentMembersWidgetIntegration implements ConfigFormInterface
{
    use DefaultConfigFormTrait;
    public function getConfigFormName(): ?string
    {
        return LeuchtfeuerCompanySegmentMembersWidgetConfigType::class;
    }
}