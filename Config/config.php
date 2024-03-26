<?php

return [
    'name'        => 'Company Segment Members Widget by Leuchtfeuer',
    'description' => 'Add Widget that displays members of a Company Segment in the Dashboard',
    'version'     => '5.0.0',
    'author'      => 'Leuchtfeuer Digital Marketing GmbH',

    'services'    => [
        'other' => [
        'leuchtfeuercompanysegmentmemberswidget.config' => [
            'class'     => \MauticPlugin\LeuchtfeuerCompanySegmentMembersWidgetBundlealt\Integration\Config::class,
            'arguments' => [
                'mautic.integrations.helper',
                ],
            ],
            ],
        'integrations' => [
            'mautic.integration.leuchtfeuercompanysegmentmemberswidget' => [
                'class' => \MauticPlugin\LeuchtfeuerCompanySegmentMembersWidgetBundlealt\Integration\LeuchtfeuerCompanySegmentMembersWidgetIntegration::class,
                'tags'  => [
                    'mautic.integration',
                    'mautic.basic_integration',
                    ],
                ],
            'leuchtfeuercompanysegmentmemberswidget.integration.configuration' => [
                'class' => \MauticPlugin\LeuchtfeuerCompanySegmentMembersWidgetBundlealt\Integration\Support\ConfigSupport::class,
                'tags'  => [
                    'mautic.config_integration',
                    ],
                ],
            ],
        ]
];
