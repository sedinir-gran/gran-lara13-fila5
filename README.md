# Guia de Configuração do Projeto Modelo - Laravel 13 + Filament 5 + PHP 8.5

Este repositório serve como um guia prático e um modelo inicial para projetos modernos utilizando Laravel 13, Filament 5 e PHP 8.5. Ele foi estruturado para oferecer uma base limpa, organizada e pronta para desenvolvimento, incluindo boas práticas de configuração, integração com o painel administrativo Filament e otimizações voltadas para produtividade e escalabilidade. Ideal tanto para iniciar novos projetos quanto para servir como referência na padronização de ambientes Laravel atualizados.

Desenvolvido como atividade complementar ao curso de Tecnólogo em Sistemas para Internet, pelo aluno Sedinir Consentini de Souza, do Gran Centro Universitário. Em Abril de 2026.

## Requisitos

- Git
- Herd + PHP 8.5
- DBngin + TablePlus

### Configuração do Git

```bash
git config --global user.email "seuemail@dominio.com"
git config --global user.name "seu-usuario-git"
git credential-manager erase
protocol=https
host=github.com
```

Faça login com o seu token, criado no github.

### Laravel installer

```bash
composer global require laravel/installer
```

## Criando um Novo Projeto

### Instalar Laravel

```bash
laravel new fla-lara13-fila5
```

Escolha as seguintes opções:

- [none] No starter kit
- [0] Pest
- [no] Boost
- [mysql] MySQL

- [no] Do not run the default database migrations
- [no] Do not run npm install and npm run build

No arquivo `.env.example` altere:

```dotenv
APP_NAME=Gran-Lara13-Fila5

APP_URL=http://gran-lara13-fila5.test

APP_LOCALE=pt_BR

APP_FAKER_LOCALE=pt_BR

FILESYSTEM_DISK=public
```

```bash
del .env
cp .env.example .env
php artisan key:generate
php artisan storage:link
```

No arquivo `composer.json` altere as linhas 3 e 12 para:

```json
"name": "sedinir-gran/gran-lara13-fila5",

"php": "^8.5",
```

Inicie o repositório no VSCode para que o nome correto do branch seja aplicado.

```bash
composer update
vendor/bin/pest
vendor/bin/pint
git add .
git commit -m "Laravel 13, none kit, pest, no boost, mysql"

### Livewire 4

```bash
composer require livewire/livewire
php artisan livewire:layout
php artisan livewire:config
vendor/bin/pint
git add .
git commit -m "Livewire 4"
```

### Larastan + Peststan

```bash
composer require --dev "larastan/larastan"
composer require --dev "mrpunyapal/peststan"
```

Crie o arquivo `phpstan.neon`  no diretório raiz do projeto, contendo:

```yaml
includes:
    - ./vendor/larastan/larastan/extension.neon
    - ./vendor/mrpunyapal/peststan/extension.neon

parameters:
    level: 10
    paths:
        - app
        - routes
        - database
        - resources
        - tests

    ignoreErrors:
    
    reportUnmatchedIgnoredErrors: false

    treatPhpDocTypesAsCertain: true
```

No arquivo `tests\Pest.php` descomente na linha 18:

```php
->use(RefreshDatabase::class)
```

on line 19 add 'Unit' to the in method:

```php
->in('Feature', 'Unit');
```

on line 48 add an return void to the function:

```php
function something(): void
```

Altere o teste `tests\Unit\ExampleTest.php` para que passe no Larastan.

```bash
vendor/bin/phpstan analyse
vendor/bin/pest
vendor/bin/pint
git add .
git commit -m "Larastan + Peststan"
```

### Pest Plugin Browser

```bash
composer require pestphp/pest-plugin-browser --dev
Set-ExecutionPolicy -Scope Process -ExecutionPolicy Bypass
npm install
npm install playwright@latest
npx playwright install
npm run build
php artisan migrate
```

Exclua o arquivo `tests\Feature\ExampleTest.php` e crie `tests\Feature\Pages\HomeTest.php` com o seguinte teste:

```php
<?php

test('the home page returns a successful response', function () {
    $url = config('app.url');
    assert(is_string($url));
    visit("{$url}/")->assertNoSmoke();
});

```

```bash
vendor/bin/pest
vendor/bin/phpstan analyse
vendor/bin/pint
git add .
git commit -m "Pest Plugin Browser"
```
