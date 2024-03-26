<?php

namespace MauticPlugin\LeuchtfeuerCompanySegmentMembersWidgetBundlealt\Form\Type;

use Mautic\LeadBundle\Model\ListModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class DashboardCompanySegmentMembersType extends AbstractType
{
    public function __construct(
        private ListModel $segmentModel
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $lists    = $this->segmentModel->getUserLists();
        $segments = [];
        foreach ($lists as $list) {
            $segments[$list['name']] = $list['id'];
        }

        $builder->add('segments', ChoiceType::class, [
                'label'             => 'mautic.lead.list.filter',
                'multiple'          => true,
                'choices'           => $segments,
                'label_attr'        => ['class' => 'control-label'],
                'attr'              => ['class' => 'form-control'],
                'required'          => false,
            ]
        );

        $orderoptions = ['Date Created' => 1];
        $builder->add('order', ChoiceType::class, [
                'label'             => 'mautic.widget.company.segment.members.ordertitle',
                'multiple'          => false,
                'choices'           => $orderoptions,
                'label_attr'        => ['class' => 'control-label'],
                'attr'              => ['class' => 'form-control'],
                'required'          => false,
            ]
        );


        $builder->add('numberofentries', TextType::class, [
                'label'             => 'mautic.widget.company.segment.members.numberofentries',
                'label_attr'        => ['class' => 'control-label'],
                'attr'              => ['class' => 'form-control'],
                'required'          => false,
            ]
        );
    }
}
