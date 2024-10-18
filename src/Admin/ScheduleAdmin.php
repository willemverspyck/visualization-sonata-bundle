<?php

declare(strict_types=1);

namespace Spyck\VisualizationSonataBundle\Admin;

use DateTimeImmutable;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Spyck\VisualizationBundle\Entity\AbstractSchedule;
use Spyck\VisualizationBundle\Entity\ScheduleForEvent;
use Spyck\VisualizationBundle\Entity\ScheduleForSystem;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

#[AutoconfigureTag('sonata.admin', [
    'group' => 'Visualization',
    'manager_type' => 'orm',
    'model_class' => AbstractSchedule::class,
    'label' => 'Schedule',
])]
final class ScheduleAdmin extends AbstractAdmin
{
    public function __construct()
    {
        $this->setSubClasses([
            'Schedule (Event)' => ScheduleForEvent::class,
            'Schedule (System)' => ScheduleForSystem::class,
        ]);

        parent::__construct();
    }

    protected function configureFormFields(FormMapper $form): void
    {
        $subject = $this->getSubject();

        $matchHours = range(0, 23);
        $matchDays = range(1, 31);
        $matchWeeks = range(1, 52);
        $matchWeekdays = range(1, 7);

        $form
            ->with('Fields')
                ->add('name', null, [
                    'required' => true,
                ])
                ->ifTrue($subject instanceof ScheduleForEvent)
                    ->add('code')
                ->ifEnd()
                ->add('matchHours', ChoiceType::class, [
                    'choices' => array_combine($matchHours, $matchHours),
                    'multiple' => true,
                    'required' => false,
                ])
                ->add('matchDays', ChoiceType::class, [
                    'choices' => array_combine($matchDays, $matchDays),
                    'multiple' => true,
                    'required' => false,
                ])
                ->add('matchWeeks', ChoiceType::class, [
                    'choices' => array_combine($matchWeeks, $matchWeeks),
                    'multiple' => true,
                    'required' => false,
                ])
                ->add('matchWeekdays', ChoiceType::class, [
                    'choices' => array_combine($matchWeekdays, $matchWeekdays),
                    'choice_label' => function (int $value) {
                        $date = new DateTimeImmutable('Sunday');

                        return $date->modify(sprintf('+%d day', $value))->format('l');
                    },
                    'multiple' => true,
                    'required' => false,
                ])
                ->add('active')
            ->end();
    }

    protected function configureDatagridFilters(DatagridMapper $datagrid): void
    {
        $datagrid
            ->add('name')
            ->add('active');
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->add('name')
            ->add('active')
            ->add(ListMapper::NAME_ACTIONS, null, [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'delete' => [],
                ],
            ]);
    }

    protected function getRemoveRoutes(): iterable
    {
        yield 'show';
    }
}
