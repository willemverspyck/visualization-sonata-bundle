<?php

declare(strict_types=1);

namespace Spyck\VisualizationSonataBundle\Admin;

use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\FieldDescription\FieldDescriptionInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Spyck\VisualizationBundle\Entity\Widget;
use Spyck\VisualizationSonataBundle\Controller\WidgetController;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

#[AutoconfigureTag('sonata.admin', [
    'controller' => WidgetController::class,
    'group' => 'Visualization',
    'manager_type' => 'orm',
    'model_class' => Widget::class,
    'label' => 'Widget',
])]
final class WidgetAdmin extends AbstractAdmin
{
    protected array $addRoutes = ['clone'];
    protected array $removeRoutes = ['show'];

    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->with('Fields')
                ->add('group')
                ->add('name')
                ->add('description')
                ->add('descriptionEmpty')
                ->add('adapter')
                ->add('parameters', ChoiceType::class, [
                    'choices' => [
                        'Stacked' => 'stacked',
                    ],
                    'multiple' => true,
                    'required' => false,
                ])
                ->add('charts', ChoiceType::class, [
                    'choices' => Widget::getChartData(true),
                    'multiple' => true,
                    'required' => false,
                ])
                ->add('groups')
                ->add('active')
            ->end();
    }

    protected function configureDatagridFilters(DatagridMapper $datagrid): void
    {
        $datagrid
            ->add('name')
            ->add('adapter');
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->add('name')
            ->add('adapter')
            ->add('parameters', FieldDescriptionInterface::TYPE_ARRAY)
            ->add('active')
            ->add(ListMapper::NAME_ACTIONS, null, [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'clone' => [
                        'template' => '@SpyckVisualizationSonata/list_action_clone.html.twig',
                    ],
                    'delete' => [],
                ],
            ]);
    }
}
