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
use Spyck\VisualizationBundle\Entity\Log;
use Spyck\VisualizationSonataBundle\Filter\DateRangeFilter;
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
    protected array $removeRoutes = ['create', 'delete', 'edit'];

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
                    'choices' => Log::getViews(),
                ],
                'field_type' => ChoiceType::class,
            ])
            ->add('type', ChoiceFilter::class, [
                'field_options' => [
                    'choices' => Log::getTypes(),
                ],
                'field_type' => ChoiceType::class,
            ])
            ->add('log', CallbackFilter::class, [
                'callback' => [$this, 'getCallbackSearchInJson'],
            ])
            ->add('logIsNull', NullFilter::class, [
                'field_name' => 'log',
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
                'choices' => Log::getViews(),
            ])
            ->add('type', FieldDescriptionInterface::TYPE_CHOICE, [
                'choices' => Log::getTypes(),
            ])
            ->add('log', null, [
                'template' => '@SpyckVisualizationSonata/list_break.html.twig',
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
}
