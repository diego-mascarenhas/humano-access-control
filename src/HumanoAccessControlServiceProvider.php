<?php

namespace Idoneo\HumanoAccessControl;

use Idoneo\HumanoAccessControl\Commands\HumanoAccessControlCommand;
use Idoneo\HumanoAccessControl\Models\SystemModule;
use Illuminate\Support\Facades\Schema;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

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

    /**
     * Ensure module registry row exists on install/boot.
     */
    public function bootingPackage()
    {
        parent::bootingPackage();

        try {
            if (Schema::hasTable('modules')) {
                // Register module if not present (works with/without host App\Models\Module)
                if (class_exists(\App\Models\Module::class)) {
                    \App\Models\Module::updateOrCreate(
                        ['key' => 'access-control'],
                        [
                            'name' => 'Access Control',
                            'icon' => 'ti ti-shield-lock',
                            'description' => 'User roles and permissions management module',
                            'is_core' => false,
                            'group' => null,  // General Management (no group)
                            'order' => 6,
                            'status' => 1,
                        ]
                    );
                } else {
                    SystemModule::query()->updateOrCreate(
                        ['key' => 'access-control'],
                        [
                            'name' => 'Access Control',
                            'icon' => 'ti ti-shield-lock',
                            'description' => 'User roles and permissions management module',
                            'is_core' => false,
                            'status' => 1,
                        ]
                    );
                }
            }
        } catch (\Throwable $e) {
            // Silently ignore if host app hasn't migrated yet
        }
    }
}
