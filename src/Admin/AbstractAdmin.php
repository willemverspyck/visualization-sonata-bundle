<?php

declare(strict_types=1);

namespace Spyck\VisualizationSonataBundle\Admin;

use Doctrine\Common\Collections\Criteria;
use Sonata\AdminBundle\Admin\AbstractAdmin as SonataAbstractAdmin;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Filter\Model\FilterData;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\DoctrineORMAdminBundle\Datagrid\ProxyQueryInterface as ProxyQueryOrmInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Contracts\Service\Attribute\Required;

abstract class AbstractAdmin extends SonataAbstractAdmin
{
    protected array $addRoutes = [];
    protected array $removeRoutes = ['create', 'delete', 'show'];

    #[Required]
    public function setServiceTranslation(): void
    {
        $this->setTranslationDomain('SpyckVisualizationSonataBundle');
    }

    public function getCallbackSearchInJson(ProxyQueryOrmInterface $proxyQuery, string $alias, string $field, FilterData $filterData): bool
    {
        if (null === $filterData->getValue()) {
            return false;
        }

        $proxyQuery
            ->andWhere(sprintf('JSON_SEARCH(%s.%s, \'one\', :value) IS NOT NULL', $alias, $field))
            ->setParameter('value', sprintf('%%%s%%', $filterData->getValue()));

        return true;
    }

    public function getAutocompleteSearch(AdminInterface $admin, array $properties, string $value): void
    {
        $datagrid = $admin->getDatagrid();
        $query = $datagrid->getQuery();

        $keywords = $this->getKeywords($value);

        foreach ($keywords as $index => $keyword) {
            $orX = $query->expr()->orX();

            foreach ($properties as $property) {
                if (false === $datagrid->hasFilter($property)) {
                    throw new BadRequestHttpException(sprintf('Filter "%s" not found', $property));
                }

                $filter = $datagrid->getFilter($property);

                $alias = $query->entityJoin($filter->getParentAssociationMappings());

                $key = sprintf('%s_%d', $filter->getFormName(), $index + 1);

                $orX->add(sprintf('%s.%s LIKE :%s', $alias, $filter->getFieldName(), $key));

                $query->setParameter($key, sprintf('%%%s%%', $keyword));
            }

            $query->andWhere($orX);
        }
    }

    protected function configureDefaultSortValues(array &$sortValues): void
    {
        $sortValues['_sort_order'] = Criteria::DESC;
        $sortValues['_sort_by'] = 'id';
    }

    protected function configureRoutes(RouteCollectionInterface $routeCollection): void
    {
        foreach ($this->addRoutes as $route) {
            $routeCollection->add($route, sprintf('%s/%s', $this->getRouterIdParameter(), $route));
        }

        foreach ($this->removeRoutes as $route) {
            $routeCollection->remove($route);
        }
    }

    protected function createNewInstance(): object
    {
        if ($this->isCurrentRoute('clone')) {
            return clone $this->getSubject();
        }

        return parent::createNewInstance();
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

    private function getKeywords(string $data): array
    {
        return array_filter(str_getcsv($data, ' '));
    }
}
