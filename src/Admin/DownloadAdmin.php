<?php

declare(strict_types=1);

namespace Spyck\VisualizationSonataBundle\Admin;

use Exception;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\FieldDescription\FieldDescriptionInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelAutocompleteType;
use Sonata\DoctrineORMAdminBundle\Filter\ChoiceFilter;
use Sonata\DoctrineORMAdminBundle\Filter\ModelFilter;
use Spyck\SonataExtension\Form\Type\ParameterType;
use Spyck\SonataExtension\Utility\AutocompleteUtility;
use Spyck\VisualizationBundle\Entity\Download;
use Spyck\VisualizationBundle\Entity\UserInterface;
use Spyck\VisualizationBundle\Service\ViewService;
use Spyck\VisualizationSonataBundle\Controller\DownloadController;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

#[AutoconfigureTag('sonata.admin', [
    'controller' => DownloadController::class,
    'group' => 'Visualization',
    'manager_type' => 'orm',
    'model_class' => Download::class,
    'label' => 'Download',
])]
final class DownloadAdmin extends AbstractAdmin
{
    public function __construct(private readonly ViewService $viewService)
    {
        parent::__construct();
    }

    protected function getAddRoutes(): iterable
    {
        yield 'clone';
    }

    protected function getRemoveRoutes(): iterable
    {
        yield 'edit';
        yield 'delete';
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
                ->add('widget', ModelAutocompleteType::class, [
                    'callback' => [AutocompleteUtility::class, 'callbackForm'],
                    'placeholder' => 'Choose widget',
                    'property' => [
                        'adapter',
                        'name',
                    ],
                    'required' => true,
                ])
                ->add('name')
                ->add('view', ChoiceType::class, [
                    'choices' => $this->getViews(true),
                    'required' => true,
                ])
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
            ->add('widget', ModelFilter::class, [
                'field_options' => [
                    'property' => [
                        'adapter',
                        'name',
                    ],
                ],
                'field_type' => ModelAutocompleteType::class,
            ])
            ->add('name')
            ->add('view', ChoiceFilter::class, [
                'field_options' => [
                    'choices' => $this->getViews(true),
                ],
                'field_type' => ChoiceType::class,
            ]);
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->add('user')
            ->add('widget')
            ->add('name')
            ->add('view', FieldDescriptionInterface::TYPE_CHOICE, [
                'choices' => $this->getViews(),
            ])
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
