<?php

namespace MauticPlugin\LeuchtfeuerCompanySegmentMembersWidgetBundle\Form\Type;

use Mautic\IntegrationsBundle\Form\Type\IntegrationConfigType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

class LeuchtfeuerCompanySegmentMembersWidgetConfigType extends AbstractType
{
    /**
     * @param array<mixed> $options
     */
    public function finishView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['form']->children['isPublished']->vars['help'] = 'mautic.plugin.companysegmentmemberswidget.config.enable.help';
    }

    public function getParent(): string
    {
        return IntegrationConfigType::class;
    }
}