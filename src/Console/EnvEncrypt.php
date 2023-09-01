<?php

namespace Ouun\EnvEncryption\Console;

use App\Console\Commands\Exception;
use Illuminate\Console\Command;
use Illuminate\Encryption\Encrypter;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Console\EnvironmentEncryptCommand as FoundationEnvironmentEncryptCommand;
use Illuminate\Support\Str;

class EnvEncrypt extends FoundationEnvironmentEncryptCommand
{
    /**
     * Create a new command instance.
     *
     * @param Filesystem $files
     * @return void
     */
    public function __construct(Filesystem $files)
    {
        app()->useEnvironmentPath(
            dirname(ABSPATH, 2)
        );

        parent::__construct($files);
    }

    /**
     * Execute the console command.
     * todo: Remove when PR is merged into Laravel & Acorn (https://github.com/laravel/framework/pull/47984)
     *
     * @return void
     */
    public function handle()
    {
        $cipher = $this->option('cipher') ?: 'AES-256-CBC';

        $key = $this->option('key');

        $keyPassed = $key !== null;

        $environmentFile = $this->option('env')
            ? Str::finish(dirname($this->laravel->environmentFilePath()), DIRECTORY_SEPARATOR).'.env.'.$this->option('env')
            : $this->laravel->environmentFilePath();

        $encryptedFile = $environmentFile.'.encrypted';

        if (! $keyPassed) {
            $key = Encrypter::generateKey($cipher);
        }

        if (! $this->files->exists($environmentFile)) {
            $this->components->error('Environment file not found.');

            return Command::FAILURE;
        }

        if ($this->files->exists($encryptedFile) && ! $this->option('force')) {
            $this->components->error('Encrypted environment file already exists.');

            return Command::FAILURE;
        }

        try {
            $encrypter = new Encrypter($this->parseKey($key), $cipher);

            $this->files->put(
                $encryptedFile,
                $encrypter->encrypt($this->files->get($environmentFile))
            );
        } catch (Exception $e) {
            $this->components->error($e->getMessage());

            return Command::FAILURE;
        }

        $this->components->info('Environment successfully encrypted.');

        $this->components->twoColumnDetail('Key', $keyPassed ? $key : 'base64:'.base64_encode($key));
        $this->components->twoColumnDetail('Cipher', $cipher);
        $this->components->twoColumnDetail('Encrypted file', $encryptedFile);

        $this->newLine();
    }
}
