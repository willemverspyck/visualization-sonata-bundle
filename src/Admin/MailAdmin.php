<?php

declare(strict_types=1);

namespace Spyck\VisualizationSonataBundle\Admin;

use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Spyck\SonataExtension\Form\Type\ParameterType;
use Spyck\VisualizationBundle\Entity\Mail;
use Spyck\VisualizationBundle\Entity\UserInterface;
use Spyck\VisualizationSonataBundle\Controller\MailController;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

#[AutoconfigureTag('sonata.admin', [
    'controller' => MailController::class,
    'group' => 'Visualization',
    'manager_type' => 'orm',
    'model_class' => Mail::class,
    'label' => 'Mail',
])]
final class MailAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->with('Fields')
                ->add('name', null, [
                    'required' => true,
                ])
                ->add('description', null, [
                    'required' => false,
                ])
                ->add('schedule', null, [
                    'required' => false,
                ])
                ->add('dashboard', null, [
                    'required' => true,
                ])
                ->add('variables', ParameterType::class, [
                    'required' => false,
                ])
                ->add('route')
                ->add('merge')
                ->add('view', ChoiceType::class, [
                    'choices' => Mail::getViews(true),
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

    protected function configureDatagridFilters(DatagridMapper $datagrid): void
    {
        $datagrid
            ->add('name')
            ->add('schedule')
            ->add('dashboard')
            ->add('active');
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->add('name')
            ->add('schedule')
            ->add('dashboard')
            ->add('variables')
            ->add('active')
            ->add(ListMapper::NAME_ACTIONS, null, [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'clone' => [
                        'template' => '@SpyckSonataExtension/list_action_clone.html.twig',
                    ],
                    'activities' => [
                        'template' => '@SpyckVisualizationSonata/mail/list_action_log.html.twig',
                    ],
                    'delete' => [],
                    'send' => [
                        'template' => '@SpyckVisualizationSonata/mail/list_action_send.html.twig',
                    ],
                ],
            ]);
    }

    protected function getAddRoutes(): iterable
    {
        yield 'clone';
        yield 'send';
    }

    protected function getRemoveRoutes(): iterable
    {
        yield 'show';
    }
}
