<?php

declare(strict_types=1);

namespace Spyck\VisualizationSonataBundle\Admin;

use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\FieldDescription\FieldDescriptionInterface;
use Sonata\AdminBundle\Form\Type\ModelAutocompleteType;
use Sonata\DoctrineORMAdminBundle\Filter\CallbackFilter;
use Sonata\DoctrineORMAdminBundle\Filter\ChoiceFilter;
use Sonata\DoctrineORMAdminBundle\Filter\ModelFilter;
use Sonata\DoctrineORMAdminBundle\Filter\NullFilter;
use Spyck\SonataExtension\Filter\DateRangeFilter;
use Spyck\SonataExtension\Utility\AutocompleteUtility;
use Spyck\VisualizationBundle\Entity\Log;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

#[AutoconfigureTag('sonata.admin', [
    'group' => 'Visualization',
    'manager_type' => 'orm',
    'model_class' => Log::class,
    'label' => 'Log',
])]
final class LogAdmin extends AbstractAdmin
{
    protected function configureDatagridFilters(DatagridMapper $datagrid): void
    {
        $datagrid
            ->add('dashboard', ModelFilter::class, [
                'field_options' => [
                    'property' => [
                        'name',
                    ],
                ],
                'field_type' => ModelAutocompleteType::class,
            ])
            ->add('user', ModelFilter::class, [
                'field_options' => [
                    'property' => [
                        'email',
                        'name',
                    ],
                ],
                'field_type' => ModelAutocompleteType::class,
            ])
            ->add('timestamp', DateRangeFilter::class)
            ->add('view', ChoiceFilter::class, [
                'field_options' => [
                    'choices' => Log::getViewData(),
                ],
                'field_type' => ChoiceType::class,
            ])
            ->add('type', ChoiceFilter::class, [
                'field_options' => [
                    'choices' => Log::getTypeData(),
                ],
                'field_type' => ChoiceType::class,
            ])
            ->add('messages', CallbackFilter::class, [
                'callback' => [AutocompleteUtility::class, 'callbackFilterInJson'],
            ])
            ->add('messagesIsNull', NullFilter::class, [
                'field_name' => 'messages',
            ]);
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->add('dashboard')
            ->add('user')
            ->add('timestamp', null, [
                'format' => 'Y-m-d H:i:s',
            ])
            ->add('variables')
            ->add('view', FieldDescriptionInterface::TYPE_CHOICE, [
                'choices' => Log::getViewData(),
            ])
            ->add('type', FieldDescriptionInterface::TYPE_CHOICE, [
                'choices' => Log::getTypeData(),
            ])
            ->add('messages', null, [
                'template' => '@SpyckSonataExtension/list_break.html.twig',
            ])
            ->add(ListMapper::NAME_ACTIONS, null, [
                'actions' => [
                    'show' => [
                        'template' => '@SpyckVisualizationSonata/log/list_action_show.html.twig',
                    ],
                    'edit' => [],
                    'delete' => [],
                ],
            ]);
    }

    protected function getRemoveRoutes(): iterable
    {
        yield 'create';
        yield 'delete';
        yield 'edit';
    }
}
