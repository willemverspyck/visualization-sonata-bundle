<?php

declare(strict_types=1);

namespace Spyck\VisualizationSonataBundle\Admin;

use Exception;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\DoctrineORMAdminBundle\Filter\ChoiceFilter;
use Spyck\SonataExtension\Form\Type\ParameterType;
use Spyck\VisualizationBundle\Entity\Mail;
use Spyck\VisualizationBundle\Entity\UserInterface;
use Spyck\VisualizationBundle\Service\ViewService;
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
    public function __construct(private readonly ViewService $viewService)
    {
        parent::__construct();
    }

    /**
     * @throws Exception
     */
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
                ->add('schedules', null, [
                    'required' => false,
                ])
                ->add('dashboard', null, [
                    'required' => true,
                ])
                ->add('variables', ParameterType::class, [
                    'required' => false,
                ])
                ->add('inline')
                ->add('route')
                ->add('merge')
                ->add('view', ChoiceType::class, [
                    'choices' => $this->getViews(true),
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
            ->add('name')
            ->add('schedules')
            ->add('dashboard')
            ->add('view', ChoiceFilter::class, [
                'field_options' => [
                    'choices' => $this->getViews(true),
                ],
                'field_type' => ChoiceType::class,
            ])
            ->add('active');
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->add('name')
            ->add('schedules')
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
                    'message' => [
                        'template' => '@SpyckVisualizationSonata/mail/list_action_message.html.twig',
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
        yield 'show';
    }

    /**
     * @throws Exception
     */
    private function getViews(bool $inverse = false): array
    {
        $data = $this->viewService->getViews();

        if (false === $inverse) {
            return $data;
        }

        return array_flip($data);
    }
}
