<?php

declare(strict_types=1);

namespace Spyck\VisualizationSonataBundle\Admin;

use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelAutocompleteType;
use Spyck\SonataExtension\Form\Type\ParameterType;
use Spyck\SonataExtension\Utility\AutocompleteUtility;
use Spyck\VisualizationBundle\Entity\Block;
use Spyck\VisualizationBundle\Entity\Widget;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

#[AutoconfigureTag('sonata.admin', [
    'manager_type' => 'orm',
    'model_class' => Block::class,
    'show_in_dashboard' => false,
])]
final class BlockAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->add('widget', ModelAutocompleteType::class, [
                'callback' => [AutocompleteUtility::class, 'callbackForm'],
                'placeholder' => 'Choose widget',
                'property' => [
                    'name',
                    'adapter',
                ],
                'required' => true,
            ])
            ->add('name', null, [
                'required' => false,
            ])
            ->add('description', null, [
                'required' => false,
            ])
            ->add('size', ChoiceType::class, [
                'choices' => Block::getSizes(),
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

    protected function getRemoveRoutes(): iterable
    {
        return [];
    }
}
