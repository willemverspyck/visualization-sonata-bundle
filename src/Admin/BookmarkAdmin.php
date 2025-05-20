<?php

declare(strict_types=1);

namespace Spyck\VisualizationSonataBundle\Admin;

use Exception;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelAutocompleteType;
use Sonata\DoctrineORMAdminBundle\Filter\ModelFilter;
use Spyck\SonataExtension\Form\Type\ParameterType;
use Spyck\SonataExtension\Utility\AutocompleteUtility;
use Spyck\VisualizationBundle\Entity\Bookmark;
use Spyck\VisualizationBundle\Entity\UserInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('sonata.admin', [
    'group' => 'Visualization',
    'manager_type' => 'orm',
    'model_class' => Bookmark::class,
    'label' => 'Bookmark',
])]
final class BookmarkAdmin extends AbstractAdmin
{
    protected function getAddRoutes(): iterable
    {
        yield 'clone';
    }

    protected function getRemoveRoutes(): iterable
    {
        yield 'show';
    }

    /**
     * @throws Exception
     */
    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->with('Fields')
                ->ifTrue($this->isInstanceOf(UserInterface::class))
                    ->add('user', ModelAutocompleteType::class, [
                        'callback' => [AutocompleteUtility::class, 'callbackForm'],
                        'placeholder' => 'Choose user',
                        'property' => [
                            'email',
                            'name',
                        ],
                        'required' => true,
                    ])
                ->ifEnd()
                ->add('dashboard', ModelAutocompleteType::class, [
                    'callback' => [AutocompleteUtility::class, 'callbackForm'],
                    'placeholder' => 'Choose dashboard',
                    'property' => [
                        'name',
                    ],
                    'required' => true,
                ])
                ->add('name')
                ->add('variables', ParameterType::class, [
                    'required' => false,
                ])
            ->end();
    }

    /**
     * @throws Exception
     */
    protected function configureDatagridFilters(DatagridMapper $datagrid): void
    {
        $datagrid
            ->add('user', ModelFilter::class, [
                'field_options' => [
                    'property' => [
                        'email',
                        'name',
                    ],
                ],
                'field_type' => ModelAutocompleteType::class,
            ])
            ->add('dashboard', ModelFilter::class, [
                'field_options' => [
                    'property' => [
                        'name',
                    ],
                ],
                'field_type' => ModelAutocompleteType::class,
            ])
            ->add('name');
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->add('user')
            ->add('dashboard')
            ->add('name')
            ->add(ListMapper::NAME_ACTIONS, null, [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'clone' => [
                        'template' => '@SpyckSonataExtension/list_action_clone.html.twig',
                    ],
                    'delete' => [],
                ],
            ]);
    }
}
