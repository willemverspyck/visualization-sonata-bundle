<?php

declare(strict_types=1);

namespace Spyck\VisualizationSonataBundle\Admin;

use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelAutocompleteType;
use Spyck\VisualizationBundle\Entity\Menu;
use Spyck\VisualizationSonataBundle\Form\Type\ParameterType;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('sonata.admin', [
    'group' => 'Visualization',
    'manager_type' => 'orm',
    'model_class' => Menu::class,
    'label' => 'Menu',
])]
final class MenuAdmin extends AbstractAdmin
{
    protected array $removeRoutes = ['show'];

    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->with('Fields')
                ->add('parent', ModelAutocompleteType::class, [
                    'callback' => [$this, 'getAutocompleteSearch'],
                    'property' => [
                        'name',
                    ],
                    'required' => false,
                ])
                ->add('name')
                ->add('dashboard', ModelAutocompleteType::class, [
                    'callback' => [$this, 'getAutocompleteSearch'],
                    'property' => [
                        'name',
                    ],
                    'required' => false,
                ])
                ->add('variables', ParameterType::class)
                ->add('position')
                ->add('active')
            ->end();
    }

    protected function configureDatagridFilters(DatagridMapper $datagrid): void
    {
        $datagrid
            ->add('name')
            ->add('parent');
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->add('name')
            ->add('parent')
            ->add('dashboard')
            ->add('position')
            ->add('active')
            ->add(ListMapper::NAME_ACTIONS, null, [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'delete' => [],
                ],
            ]);
    }
}
