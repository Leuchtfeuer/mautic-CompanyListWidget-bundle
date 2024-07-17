<?php

namespace MauticPlugin\LeuchtfeuerCompanyListWidgetBundle\Form\Type;

use MauticPlugin\LeuchtfeuerCompanySegmentsBundle\Entity\CompanySegmentRepository;
use MauticPlugin\LeuchtfeuerCompanyTagsBundle\Entity\CompanyTagsRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

class DashboardCompanyListType extends AbstractType
{
    public function __construct(
        protected CompanySegmentRepository $companySegmentRepository,
        protected CompanyTagsRepository $companyTagsRepository,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $lists           = $this->companySegmentRepository->getSegments();
        $companySegments = [];
        foreach ($lists as $list) {
            $companySegments[$list['name']] = $list['id'];
        }

        $builder->add('companysegments', ChoiceType::class, [
            'label'             => 'mautic.widget.company.segment.filter',
            'multiple'          => true,
            'choices'           => $companySegments,
            'label_attr'        => ['class' => 'control-label'],
            'attr'              => ['class' => 'form-control'],
            'required'          => false,
        ]
        );

        $tags        = $this->companyTagsRepository->getAllTagObjects();
        $companyTags = [];
        foreach ($tags as $tag) {
            $companyTags[$tag->getTag()] = $tag->getID();
        }

        $builder->add('companytags', ChoiceType::class, [
            'label'             => 'mautic.widget.company.tag.filter',
            'multiple'          => true,
            'choices'           => $companyTags,
            'label_attr'        => ['class' => 'control-label'],
            'attr'              => ['class' => 'form-control'],
            'required'          => false,
        ]
        );

        $orderoptions = ['mautic.widget.company.list.orderbydatecreated' => 1];
        $builder->add('order', ChoiceType::class, [
            'label'             => 'mautic.widget.company.list.ordertitle',
            'multiple'          => false,
            'choices'           => $orderoptions,
            'label_attr'        => ['class' => 'control-label'],
            'attr'              => ['class' => 'form-control'],
            'required'          => false,
        ]
        );
    }
}
