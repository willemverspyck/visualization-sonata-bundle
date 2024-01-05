<?php

declare(strict_types=1);

namespace Spyck\VisualizationSonataBundle\Admin;

use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelAutocompleteType;
use Sonata\Form\Type\CollectionType;
use Spyck\SonataExtension\Form\Type\ParameterType;
use Spyck\SonataExtension\Utility\AutocompleteUtility;
use Spyck\VisualizationBundle\Entity\Dashboard;
use Spyck\VisualizationBundle\Entity\UserInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('sonata.admin', [
    'group' => 'Visualization',
    'manager_type' => 'orm',
    'model_class' => Dashboard::class,
    'label' => 'Dashboard',
])]
final class DashboardAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->with('Fields')
                ->add('name')
                ->add('description')
                ->add('category')
                ->add('blocks', CollectionType::class, [], [
                    'edit' => 'inline',
                    'inline' => 'table',
                    'sortable' => 'position',
                ])
                ->add('variables', ParameterType::class)
                ->ifTrue($this->isInstanceOf(UserInterface::class))
                    ->add('user', ModelAutocompleteType::class, [
                        'callback' => [AutocompleteUtility::class, 'callbackForm'],
                        'placeholder' => 'Choose user',
                        'property' => [
                            'email',
                            'name',
                        ],
                        'required' => false,
                    ])
                ->ifEnd()
                ->add('active')
            ->end();
    }

    protected function configureDatagridFilters(DatagridMapper $datagrid): void
    {
        $datagrid
            ->add('blocks.widget')
            ->add('name')
            ->add('active');
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->add('name')
            ->add('variables')
            ->add('active')
            ->add(ListMapper::NAME_ACTIONS, null, [
                'actions' => [
                    'show' => [
                        'template' => '@SpyckVisualizationSonata/dashboard/list_action_show.html.twig',
                    ],
                    'edit' => [],
                    'delete' => [],
                ],
            ]);
    }

    protected function getRemoveRoutes(): iterable
    {
        return [];
    }
}
