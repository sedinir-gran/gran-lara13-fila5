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

### Debugbar

```bash
composer require fruitcake/laravel-debugbar --dev
php artisan vendor:publish --provider="Fruitcake\LaravelDebugbar\ServiceProvider"
```

Crie o arquivo `storage\debugbar\.gitignore` e adicione:

```yaml
*
!.gitignore
```

```bash
vendor/bin/pest
vendor/bin/phpstan analyse
vendor/bin/pint
git add .
git commit -m "Debugbar"
```

### Filament 5 + Panels

```bash
composer require filament/filament
php artisan filament:install --panels
```

Escolha **admin** como nome do painel.

```bash
php artisan vendor:publish --tag=filament-config
vendor/bin/pint
git add .
git commit -m "Filament 5 + Panels"
```

### Comportamentos globais no App Service Provider

No arquivo `app/Providers/AppServiceProvider.php`, adicione as seguintes instruções:

```php
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
```

E então adicione ao método `boot`:

```php
// Disable mass-assignment protection globally (needed by Filament)
Model::unguard();

// Enable automatic eager loading of accessed relationships to reduce N+1 queries
Model::automaticallyEagerLoadRelationships();

// Enforce stricter model behavior in non-production (helps catch errors early)
Model::shouldBeStrict(! $this->app->isProduction());

// Block DROP/TRUNCATE/DELETE without WHERE in production for safety
DB::prohibitDestructiveCommands($this->app->isProduction());

// Force HTTPS in production
if ($this->app->isProduction()) {
    URL::forceScheme('https');
}

// Set Carbon default locale
Carbon::setLocale($this->app->getLocale());
```

```bash
vendor/bin/pint
git add .
git commit -m "Comportamentos globais no App Service Provider"
```

### is_admin e avatar_url no User

Crie o arquivo de migração `database\migrations\0001_01_01_000003_add_columns_to_users_table.php`, para adicionar as colunas 'is_admin' (boolean - default false) e 'avatar_url' (string - nullable). Crie também o arquivo de teste `tests\Feature\Database\UsersTableTest.php`.

Adicione ao `app\Models\User.php` o 'avatar_url' e 'is_admin' ao array fillable e 'is_admin' à função casts como boolean. Crie também o arquivo de teste `tests\Feature\Models\UserTest.php`.

Adicione ao `database\factories\UserFactory.php`, na função definition:

```php
'is_admin' => fake()->boolean(),
'avatar_url' => fake()->imageUrl(),
```

Adicione ao `database\seeders\DatabaseSeeder.php`, na função run:

```php
User::create([
    'name' => 'Administrador Teste',
    'email' => 'admin@mail.com',
    'password' => bcrypt('12345678'),
    'is_admin' => true,
]);

User::create([
    'name' => 'Usuário Teste',
    'email' => 'usuario@mail.com',
    'password' => bcrypt('12345678'),
    'is_admin' => false,
]);
```

```bash
php artisan migrate:fresh --seed
vendor/bin/phpstan analyse
vendor/bin/pest
vendor/bin/pint
git add .
git commit -m "is_admin e avatar_url no User"
```

### Filament Trait

Altere o arquivo `app\Models\User.php` para:

```php
use App\Traits\FilamentTrait;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;

class User extends Authenticatable implements FilamentUser, HasAvatar
{
    use FilamentTrait;
```

Crie o arquivo `app/Traits/FilamentTrait.php` para o suporte do Filament. Crie também os arquivos de teste `tests\Feature\Traits\FilamentTraitTest.php` e `tests\Feature\Pages\AdminTest.php`.

Adicionar ao arquivo `phpstan.neon`, na seção `ignoreErrors`, as seguintes linhas:

```yaml
# ignores Mockery andReturn() method warning, that is valid in tests.
- '#Call to an undefined method Mockery\\ExpectationInterface\|Mockery\\HigherOrderMessage::andReturn\(\)#'
```

```bash
vendor/bin/phpstan analyse
vendor/bin/pest
vendor/bin/pint
git add .
git commit -m "Filament Trait"
```

### Boost

```bash
composer require laravel/boost --dev
php artisan boost:install
```

Escolha as seguintes opções:
Features: guidelines,skills,mcp
Guidelines: filament/filament
AI: gemini

```bash
php artisan vendor:publish --tag=boost-config
vendor/bin/phpstan analyse
vendor/bin/pest
vendor/bin/pint
git add .
git commit -m "Boost"
```

### Localização (Português do Brasil)

```bash
php artisan lang:publish
composer require lucascudo/laravel-pt-br-localiza --dev
php artisan vendor:publish --tag=laravel-pt-br-localization
vendor/bin/phpstan analyse
vendor/bin/pest
vendor/bin/pint
git add .
git commit -m "Localização (Português do Brasil)"
```

### User Resource

```bash
php artisan make:filament-resource User --generate --panel=admin
```

Escolha 'email' como atributo de título e não para a página de visualização.

Edite os arquivos de recursos criados e crie o arquivo de teste `tests\Feature\Resources\UserResourceTest.php`.

```bash
vendor/bin/phpstan analyse
vendor/bin/pest
vendor/bin/pint
git add .
git commit -m "User Resource"
```

### Filament Edit Profile Plugin

```bash
composer require joaopaulolndev/filament-edit-profile
php artisan vendor:publish --tag="filament-edit-profile-config"
```

Crie o arquivo `app\Providers\Filament\SharedItems.php` com uma função plugins() que retorna um array de Plugin e uma função menu(string panel_id = '') que retorna um array de Action. Crie também o arquivo de teste `tests\Feature\Pages\ProfileTest.php`.

Adicione ao `app\Providers\Filament\AdminPanelProvider.php` return panel:

```php
->plugins([
    ...SharedItems::plugins(),
    // other plugins
])
->userMenuItems([
    ...SharedItems::menu('admin'),
    // other menu items
])
```

```bash
vendor/bin/phpstan analyse
vendor/bin/pest
vendor/bin/pint
git add .
git commit -m "Filament Edit Profile Plugin"
```

### Home Panel

```bash
php artisan make:filament-panel home
```

Adicione em `app\Providers\Filament\HomePanelProvider.php`, após '->id('home')':

```php
->login()
->userMenuItems([
    ...SharedItems::menu('home'),
    // other menu items
])
->plugins([
    ...SharedItems::plugins(),
    // other plugins
])
// altere o path
->path(strtolower(__('Home')))
```

Adicione ao menu return array em `app\Providers\Filament\SharedItems.php`:

```php
Action::make('home')
    ->label(__('Home'))
    ->icon('heroicon-o-home')
    ->url('/'.strtolower(__('Home')))
    ->visible(fn () => $panel_id == 'admin'),
Action::make('admin')
    ->label(__('Administrator'))
    ->icon('heroicon-o-building-library')
    ->url('/admin')
    ->visible(fn () => $panel_id !== 'admin' && auth()->user()?->is_admin),
```

Create also the `tests\Feature\Pages\HomeTest.php` test file.

```bash
vendor/bin/phpstan analyse
vendor/bin/pest
vendor/bin/pint
git add .
git commit -m "Home Panel"
```
