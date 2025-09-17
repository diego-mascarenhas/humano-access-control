<?php

namespace Idoneo\HumanoAccessControl;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Idoneo\HumanoAccessControl\Commands\HumanoAccessControlCommand;

class HumanoAccessControlServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('humano-access-control')
            ->hasConfigFile()
            ->hasViews()
            ->hasRoute('web')
            ->hasMigration('create_humano_access_control_table')
            ->hasCommand(HumanoAccessControlCommand::class);
    }

}
