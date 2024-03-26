<?php

namespace MauticPlugin\LeuchtfeuerCompanySegmentMembersWidgetBundlealt\Integration;

use Mautic\IntegrationsBundle\Integration\BasicIntegration;
use Mautic\IntegrationsBundle\Integration\ConfigurationTrait;
use Mautic\IntegrationsBundle\Integration\Interfaces\BasicInterface;

class LeuchtfeuerCompanySegmentMembersWidgetIntegration extends BasicIntegration implements BasicInterface
{
    use ConfigurationTrait;

    // there's probably a bug that does not allow this to be with the underscore like api_version
    public const NAME         = 'leuchtfeuercompanysegmentmemberswidget';
    public const DISPLAY_NAME = 'W:Comp.Segment Members by Leuchtfeuer';

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
        return 'plugins/LeuchtfeuerCompanySegmentMembersWidgetBundle/Assets/img/leuchtfeuercompanysegmentmemberswidget.png';
    }
}