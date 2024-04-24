<?php

return [
    'name'        => 'Company Segment Members Widget by Leuchtfeuer',
    'description' => 'Add Widget that displays members of a Company Segment in the Dashboard',
    'version'     => '5.0.0',
    'author'      => 'Leuchtfeuer Digital Marketing GmbH',

    'services'    => [
        'other' => [
            'leuchtfeuercompanysegmentmemberswidget.config' => [
                'class'     => MauticPlugin\LeuchtfeuerCompanySegmentMembersWidgetBundle\Integration\Config::class,
                'arguments' => [
                    'mautic.integrations.helper',
                ],
            ],
        ],
        'integrations' => [
            'mautic.integration.leuchtfeuercompanysegmentmemberswidget' => [
                'class' => MauticPlugin\LeuchtfeuerCompanySegmentMembersWidgetBundle\Integration\LeuchtfeuerCompanySegmentMembersWidgetIntegration::class,
                'tags'  => [
                    'mautic.integration',
                    'mautic.basic_integration',
                ],
            ],
            'leuchtfeuercompanysegmentmemberswidget.integration.configuration' => [
                'class' => MauticPlugin\LeuchtfeuerCompanySegmentMembersWidgetBundle\Integration\Support\ConfigSupport::class,
                'tags'  => [
                    'mautic.config_integration',
                ],
            ],
        ],
        'fixtures' => [
            'mautic.leuchtfeuercompanysegmentmemberswidget.fixture.companysegment' => [
                'class'     => MauticPlugin\LeuchtfeuerCompanySegmentMembersWidgetBundle\Tests\Fixtures\LoadCompanySegmentData::class,
                'tag'       => Doctrine\Bundle\FixturesBundle\DependencyInjection\CompilerPass\FixturesCompilerPass::FIXTURE_TAG,
                'arguments' => [
                    'mautic.leuchtfeuercompanysegments.model.companysegment',
                    'mautic.lead.model.company',
                    'mautic.lead.repository.company',
                ],
            ],
        ],
    ],
];
