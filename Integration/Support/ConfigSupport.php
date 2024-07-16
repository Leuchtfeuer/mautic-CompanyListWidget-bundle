<?php

declare(strict_types=1);

namespace MauticPlugin\LeuchtfeuerCompanyListWidgetBundle\Integration\Support;

use Mautic\IntegrationsBundle\Integration\DefaultConfigFormTrait;
use Mautic\IntegrationsBundle\Integration\Interfaces\ConfigFormInterface;
use MauticPlugin\LeuchtfeuerCompanyListWidgetBundle\Integration\LeuchtfeuerCompanyListWidgetIntegration;

class ConfigSupport extends LeuchtfeuerCompanyListWidgetIntegration implements ConfigFormInterface
{
    use DefaultConfigFormTrait;
}
