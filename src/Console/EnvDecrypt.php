<?php

namespace Ouun\EnvEncryption\Console;

use App\Console\Commands\Exception;
use Illuminate\Console\Command;
use Illuminate\Encryption\Encrypter;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Console\EnvironmentDecryptCommand as FoundationEnvironmentDecryptCommand;
use Illuminate\Support\Env;
use Illuminate\Support\Str;

class EnvDecrypt extends FoundationEnvironmentDecryptCommand
{
    /**
     * Create a new command instance.
     *
     * @param Filesystem $files
     * @return void
     */
    public function __construct(Filesystem $files)
    {
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
        $key = $this->option('key') ?: Env::get('LARAVEL_ENV_ENCRYPTION_KEY');

        if (! $key) {
            $this->components->error('A decryption key is required.');

            return Command::FAILURE;
        }

        $cipher = $this->option('cipher') ?: 'AES-256-CBC';

        $key = $this->parseKey($key);

        $encryptedFile = ($this->option('env')
                ? Str::finish(dirname($this->laravel->environmentFilePath()), DIRECTORY_SEPARATOR).'.env.'.$this->option('env')
                : $this->laravel->environmentFilePath()).'.encrypted';

        $this->components->info($encryptedFile);

        $outputFile = $this->outputFilePath();

        if (Str::endsWith($outputFile, '.encrypted')) {
            $this->components->error('Invalid filename.');

            return Command::FAILURE;
        }

        if (! $this->files->exists($encryptedFile)) {
            $this->components->error('Encrypted environment file not found.');

            return Command::FAILURE;
        }

        if ($this->files->exists($outputFile) && ! $this->option('force')) {
            $this->components->error('Environment file already exists.');

            return Command::FAILURE;
        }

        try {
            $encrypter = new Encrypter($key, $cipher);

            $this->files->put(
                $outputFile,
                $encrypter->decrypt($this->files->get($encryptedFile))
            );
        } catch (Exception $e) {
            $this->components->error($e->getMessage());

            return Command::FAILURE;
        }

        $this->components->info('Environment successfully decrypted.');

        $this->components->twoColumnDetail('Decrypted file', $outputFile);

        $this->newLine();
    }

    /**
     * Get the output file path that should be used for the command.
     * todo: Remove when PR is merged into Laravel & Acorn (https://github.com/laravel/framework/pull/47984)
     *
     * @return string
     */
    protected function outputFilePath()
    {
        $path = Str::finish($this->option('path') ?: dirname($this->laravel->environmentFilePath()), DIRECTORY_SEPARATOR);

        $outputFile = $this->option('filename') ?: ('.env'.($this->option('env') ? '.'.$this->option('env') : ''));
        $outputFile = ltrim($outputFile, DIRECTORY_SEPARATOR);

        return $path.$outputFile;
    }
}
