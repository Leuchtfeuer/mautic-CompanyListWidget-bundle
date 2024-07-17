<?php

return [
    'name'        => 'Company List Widget by Leuchtfeuer',
    'description' => 'Add Widget that lists Companies (filtered by segment and/or tag) in the Dashboard',
    'version'     => '5.1.0',
    'author'      => 'Leuchtfeuer Digital Marketing GmbH',

    'services'    => [
        'other' => [
            'leuchtfeuercompanylistwidget.config' => [
                'class'     => MauticPlugin\LeuchtfeuerCompanyListWidgetBundle\Integration\Config::class,
                'arguments' => [
                    'mautic.integrations.helper',
                ],
            ],
        ],
        'integrations' => [
            'mautic.integration.leuchtfeuercompanylistwidget' => [
                'class' => MauticPlugin\LeuchtfeuerCompanyListWidgetBundle\Integration\LeuchtfeuerCompanyListWidgetIntegration::class,
                'tags'  => [
                    'mautic.integration',
                    'mautic.basic_integration',
                ],
            ],
            'leuchtfeuercompanysegmentmemberswidget.integration.configuration' => [
                'class' => MauticPlugin\LeuchtfeuerCompanyListWidgetBundle\Integration\Support\ConfigSupport::class,
                'tags'  => [
                    'mautic.config_integration',
                ],
            ],
        ],
    ],
];
