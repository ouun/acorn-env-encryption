# Acorn .env Encryption

Laravel v9.32.0 brought the release of two new Artisan commands: env:encrypt and env:decrypt. 
These commands allow you to encrypt and decrypt your .env files as well as environment specific files such as `.env.testing`, `.env.local`, `.env.production`, etc.

This package adds that encryption and decryption support to Roots Acorn.
You can now store your .env file in your version control system (e.g. GIT) without worrying about exposing sensitive information.
We are using it for Continuous Integration and Continuous Deployment (CI/CD) to deploy our Acorn applications to staging and production environments with GitHub Actions.

The commands are adjusted by this package to work with Acorn, as paths to the .env files are different from Laravel.
However, I opened a PR to add support for Acorn to the Laravel core, so this package hopefully will be obsolete in the future: [PR #48271](https://github.com/laravel/framework/pull/48271)

## Installation

You can install this package with Composer:

```bash
composer require ouun/acorn-env-encryption
```

You can publish the config file with:

```shell
$ wp acorn vendor:publish --provider="Ouun\EnvEncryption\Providers\EnvEncryptionServiceProvider"
```

## Usage

Please refer to the [Laravel documentation](https://laravel.com/docs/10.x/configuration#encryption) for more information on how to encrypt & decrypt .env files.

Please just note that instead of `$ php artisan` you will need to use WP-CLI:

```shell
$ wp acorn env:encrypt
```
