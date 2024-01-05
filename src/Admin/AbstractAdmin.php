<?php

declare(strict_types=1);

namespace Spyck\VisualizationSonataBundle\Admin;

use Doctrine\Common\Collections\Criteria;
use Spyck\SonataExtension\Admin\AbstractAdmin as BaseAbstractAdmin;
use Symfony\Contracts\Service\Attribute\Required;

abstract class AbstractAdmin extends BaseAbstractAdmin
{
    #[Required]
    public function setServiceTranslation(): void
    {
        $this->setTranslationDomain('SpyckVisualizationSonataBundle');
    }

    protected function configureDefaultSortValues(array &$sortValues): void
    {
        $sortValues['_sort_order'] = Criteria::DESC;
        $sortValues['_sort_by'] = 'id';
    }

    protected function getRemoveRoutes(): iterable
    {
        yield 'create';
        yield 'delete';
        yield 'show';
    }

    protected function isInstanceOf(string $instanceOf): bool
    {
        $classes = array_keys($this->getConfigurationPool()->getAdminClasses());

        foreach ($classes as $class) {
            if (is_a($class, $instanceOf, true)) {
                return true;
            }
        }

        return false;
    }
}
