<?php

namespace MauticPlugin\LeuchtfeuerCompanyListWidgetBundle\Integration;

use Mautic\IntegrationsBundle\Integration\BasicIntegration;
use Mautic\IntegrationsBundle\Integration\ConfigurationTrait;
use Mautic\IntegrationsBundle\Integration\Interfaces\BasicInterface;

class LeuchtfeuerCompanyListWidgetIntegration extends BasicIntegration implements BasicInterface
{
    use ConfigurationTrait;

    // there's probably a bug that does not allow this to be with the underscore like api_version
    public const NAME         = 'leuchtfeuercompanylistwidget';
    public const DISPLAY_NAME = 'Company List Widget by Leuchtfeuer';

    public function getName(): string
    {
        return self::NAME;
    }

    public function getDisplayName(): string
    {
        return self::DISPLAY_NAME;
    }

    public function getIcon(): string
    {
        return 'plugins/LeuchtfeuerCompanyListWidgetBundle/Assets/img/leuchtfeuercompanylistwidget.png';
    }
}
