<?php

namespace Ouun\EnvEncryption\Providers;

use Illuminate\Support\ServiceProvider;
use Ouun\EnvEncryption\Console\EnvDecrypt;
use Ouun\EnvEncryption\Console\EnvEncrypt;

class EnvEncryptionServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->commands([
            EnvEncrypt::class,
            EnvDecrypt::class
        ]);
    }
}
