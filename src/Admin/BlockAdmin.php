<?php

declare(strict_types=1);

namespace Spyck\VisualizationSonataBundle\Admin;

use Sonata\AdminBundle\Form\FormMapper;
use Spyck\VisualizationBundle\Entity\Block;
use Spyck\VisualizationBundle\Entity\Widget;
use Spyck\VisualizationSonataBundle\Form\Type\ParameterType;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

#[AutoconfigureTag('sonata.admin', [
    'manager_type' => 'orm',
    'model_class' => Block::class,
    'show_in_dashboard' => false,
])]
final class BlockAdmin extends AbstractAdmin
{
    protected array $removeRoutes = [];

    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->add('widget', null, [
                'required' => true,
            ])
            ->add('name', null, [
                'required' => false,
            ])
            ->add('description', null, [
                'required' => false,
            ])
            ->add('size', ChoiceType::class, [
                'choices' => Block::getSizeData(true),
            ])
            ->add('variables', ParameterType::class)
            ->add('chart', ChoiceType::class, [
                'choices' => Widget::getChartData(true),
                'required' => false,
            ])
            ->add('filter')
            ->add('filterView')
            ->add('active')
            ->add('position');
    }
}
