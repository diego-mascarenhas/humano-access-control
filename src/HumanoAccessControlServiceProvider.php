<?php

namespace Idoneo\HumanoAccessControl;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Idoneo\HumanoAccessControl\Commands\HumanoAccessControlCommand;
use Illuminate\Support\Facades\Route;

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
            ->hasMigration('create_humano_access_control_table')
            ->hasCommand(HumanoAccessControlCommand::class);
    }

	public function bootingPackage()
	{
		// Load routes for static demo pages
		Route::middleware(['web', 'auth'])
			->group(function ()
			{
				Route::view('/app/access-roles', 'humano-access-control::content.apps.app-access-roles')->name('app-access-roles');
				Route::view('/app/access-permission', 'humano-access-control::content.apps.app-access-permission')->name('app-access-permission');
			});
	}
}
