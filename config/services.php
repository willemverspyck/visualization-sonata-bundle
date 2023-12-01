<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Loader\Configurator\ReferenceConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services
        ->defaults()
            ->autowire()
            ->autoconfigure();

    $services
        ->load('Spyck\\VisualizationSonataBundle\\', sprintf('%s/../src', __DIR__))
        ->exclude([
            sprintf('%s/../src/Kernel.php', __DIR__),
        ]);
};
