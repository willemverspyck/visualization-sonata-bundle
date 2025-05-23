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
use Spyck\VisualizationBundle\Entity\Preload;
use Spyck\VisualizationBundle\Entity\UserInterface;
use Spyck\VisualizationSonataBundle\Controller\PreloadController;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('sonata.admin', [
    'controller' => PreloadController::class,
    'group' => 'Visualization',
    'manager_type' => 'orm',
    'model_class' => Preload::class,
    'label' => 'Preload',
])]
final class PreloadAdmin extends AbstractAdmin
{
    /**
     * @throws Exception
     */
    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->with('Fields')
                ->add('schedules', null, [
                    'required' => false,
                ])
                ->add('dashboard', null, [
                    'required' => true,
                ])
                ->add('variables', ParameterType::class, [
                    'required' => false,
                ])
                ->ifTrue($this->isInstanceOf(UserInterface::class))
                    ->add('users', null, [
                        'multiple' => true,
                        'required' => true,
                    ])
                ->ifEnd()
                ->add('active')
            ->end();
    }

    /**
     * @throws Exception
     */
    protected function configureDatagridFilters(DatagridMapper $datagrid): void
    {
        $datagrid
            ->add('schedules')
            ->add('dashboard')
            ->add('users', ModelFilter::class, [
                'field_options' => [
                    'property' => [
                        'email',
                        'name',
                    ],
                ],
                'field_type' => ModelAutocompleteType::class,
            ])
            ->add('active');
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->add('schedules')
            ->add('dashboard')
            ->add('variables')
            ->add('active')
            ->add(ListMapper::NAME_ACTIONS, null, [
                'actions' => [
                    'show' => [
                        'template' => '@SpyckVisualizationSonata/preload/list_action_show.html.twig',
                    ],
                    'edit' => [],
                    'clone' => [
                        'template' => '@SpyckSonataExtension/list_action_clone.html.twig',
                    ],
                    'delete' => [],
                    'message' => [
                        'template' => '@SpyckVisualizationSonata/preload/list_action_message.html.twig',
                    ],
                ],
            ]);
    }

    protected function getAddRoutes(): iterable
    {
        yield 'clone';
        yield 'message';
    }

    protected function getRemoveRoutes(): iterable
    {
        return [];
    }
}
