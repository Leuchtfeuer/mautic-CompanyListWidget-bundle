<?php

namespace MauticPlugin\LeuchtfeuerCompanySegmentMembersWidgetBundle\Form\Type;

use Mautic\LeadBundle\Model\ListModel;
use MauticPlugin\LeuchtfeuerCompanySegmentsBundle\Entity\CompanySegmentRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class DashboardCompanySegmentMembersType extends AbstractType
{
    public function __construct(
        //private ListModel $segmentModel,
        protected CompanySegmentRepository $companySegmentRepository,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $lists = $this->companySegmentRepository->getSegments();
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


        $builder->add('limit', IntegerType::class, [
                'label'             => 'mautic.widget.company.segment.members.limit',
                'label_attr'        => ['class' => 'control-label'],
                'attr'              => ['class' => 'form-control'],
                'required'          => false,
            ]
        );
    }
}
