## Aviso
Devido ao tempo pré-definido para este curso (6 horas), não utilizaremos o padrão repository, mas ao 
final do curso explicarei o motivo de utilizá-lo nos projetos pessoais e de recomendá-lo.

## Requisitos para este curso
As máquinas do laboratório possuem os requisitos para este curso instalados, por isso recomendo a utilização das mesmas 
para melhor aproveitamento das poucas horas de curso.
- php >= 7
- mariadb ou mysql
- composer
## Iniciando com o composer
Composer é o gerenciador de dependências do php, assim como o maven gerencia as dependências de projetos java por exemplo.
Uma dependência pode ser um pacote de geração de tabelas, boletos, integração com meios de pagamento, enfim, pacotes 
úteis feitos por terceiros que podem ser integrados ao nosso projeto de forma simples e rápida.

- Abra o terminal no linux/mac ou git bash no windows.
- Navegue até o diretório que deseja (cd ~/Desktop por exemplo)
- Vamos criar um projeto laravel com o seguinte comando:

```
composer create-project laravel/laravel miniCMS
```

- Ao final da instalação, você verá algo parecido com:

```
Writing lock file
Generating autoload files
> Illuminate\Foundation\ComposerScripts::postAutoloadDump
> @php artisan package:discover
Discovered Package: fideloper/proxy
Discovered Package: laravel/tinker
Package manifest generated successfully.
> @php artisan key:generate Application key [base64:T4OQTrIb1A8skRQH5y8B+LDAQZK6JXSkWrC9ma7/2co=] set successfully.
```


- Entre no diretório da aplicação com o comando abaixo utilizando o terminal no linux/mac ou git bash no windows.
```
cd miniCMS
```

## Arquivo .env

O arquivo .env contém as informações sensíveis do sistema como credências de acesso ao banco de dados, além de 
variáveis de ambiente como nome e url da aplicação, definição se a aplicação está em modo local ou em produção, e 
outros.

Não é recomendado que este arquivo seja comitado, existe uma cópia dele com o nome .env.example com informações 
genéricas que iremos sobreescrever.

- Copie o arquivo e salve com o nome .env
- Ex. no terminal ou git bash:
```
cp .env.example .env
```

- Abra o arquivo em seu editor de código ou nano/vim no terminal e edite as informações como abaixo:
```
DB_DATABASE=cms
DB_USERNAME=root
DB_PASSWORD=root
```

## Checando instalação

Feito os passos acima, criamos uma chave para nossa aplicação com o comando:

```
php artisan key:generate
```

e rodamos o servidor com o comando:


```
php artisan serve
```

O servidor será iniciado no endereço http://localhost:8000, acesse-o e veja a tela de apresentação do Laravel.


## Criação do banco de dados

Essa parte é muito simples, apenas criaremos o banco (Schema), e depos o laravel cuidará da estrutura do banco com as 
migrations.

- Se estiver no terminal do linux/mac, utilize os comandos:
```
mysql -uroot -p

root

create database cms;
```
- Digite ctrl + d para sair
- Ou se preferir, abra o workbench e crie o banco por lá

## Criação do login

```
php artisan make:auth
```

Rodando o comando acima na raiz do projeto você verá todo o processo de autenticação e recuperação de senha foi criado.
O comando abaixo irá rodas as migrations disponíveis (duas foram criadas com o comando make:auth, a tabela de usuário e 
de redefinição de senha). As migrations são o controle de versões do banco de dados.

```
php artisan migrate
```

Acesse http://localhost:8000 novamente

## A aplicação 

Estamos criando um mini cms (gestor de conteúdo, como o Wordpress por exemplo), nessas 6 horas iremos criar a 
gestão de usuários, categorias e posts.

## Criando categories

Vamos criar a entidade Category e todos os outros recursos que precisaremos, como o controller, a migration e a sua 
factory com um só comando:

```
php artisan make:model Category --all
```

Um arquivo será criado dentro de database/migrations com o padrão Y-m-d-timestamp_crate_tabela_table.php.

Abra-o com seu editor de código e vamos editar os campos da nova tabela categories.

Acrescente o campo "name" na migration, limitando o campo a 50 caracteres. Sua migration deve ficar assim:

```php
<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 50);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('categories');
    }
}

```

E pronto, já temos nossa estrutura para a tabela categories. Agora vamos preencher o modelo, abra o arquivo app/Category.php 
e preencha o array $fillable com os campos que podem ser preenchidos via formulário, ou seja, com o campo name que 
criamos anteriormente.

```php
<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Category
 *
 * @package App
 *
 * @property int id
 * @property string name
 * @property \DateTime created_at
 * @property \DateTime updated_at
 */
class Category extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name'
    ];
}

```

Já temos o modelo e a tabela prontas, agora vamos configurar nossa factory, essa camada utiliza o Faker para geração de 
dados fakes em nossa aplicação, economizando horas de preenchimento manual. Juntamente com a seeds (sementes), popularemos 
o banco de dados com dezenas de categorias para efetuarmos nosssos testes.

Abra o arquivo database/factories/CategoryFactory.php e edite-o da seguinte maneira:

```php
<?php

use Faker\Generator as Faker;

$factory->define(App\Category::class, function (Faker $faker) {
    return [
        'name' => $faker->word
    ];
});

```

O campo name será preenchido com alguma palavra aleatória, já os campos id, created_at e updated_at serão preenchidos 
automaticamente com o autoincrement e a data atual respectivamente.

Agora vamos ciar uma seeder para que ela chame a factory criada anteriormente e então popule a tabela no banco de dados 
com dados fakes.

```
php artisan make:seed CategoriesTableSeeder
```

Abra o arquivo criado database/seeds/CategoriesTableSeeder.php e edite-o para que ele faça a chamada da factory criada 
anteriormente:

```php
<?php

use App\Category;
use Illuminate\Database\Seeder;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Category::class, 20)->create();
    }
}
```

Por fim, chame essa seeder e todas as outras que criar posteriormente, no arquivo database/seeds/DatabaseSeeder.php

Pronto, podemos testar agora se a migration será executada corretamente, rode o seguinte comando:

```
php artisan migrate:fresh --seed
```

*Atenção*, o comando acima irá apagar todas as tabelas, recriá-las e semear os dados fakes.

Se uma mensagem parecida com a abaixo aparecer, tudo ocorreu como queremos.

```
Dropped all tables successfully.
Migration table created successfully.
Migrating: 2014_10_12_000000_create_users_table
Migrated:  2014_10_12_000000_create_users_table
Migrating: 2014_10_12_100000_create_password_resets_table
Migrated:  2014_10_12_100000_create_password_resets_table
Migrating: 2017_10_21_165117_create_categories_table
Migrated:  2017_10_21_165117_create_categories_table
Seeding: CategoriesTableSeeder
```
<p align="center"><img src="https://laravel.com/assets/img/components/logo-laravel.svg"></p>

<p align="center">
<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/d/total.svg" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/v/stable.svg" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/license.svg" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel attempts to take the pain out of development by easing common tasks used in the majority of web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, yet powerful, providing tools needed for large, robust applications. A superb combination of simplicity, elegance, and innovation give you tools you need to build any application with which you are tasked.

## Learning Laravel

Laravel has the most extensive and thorough documentation and video tutorial library of any modern web application framework. The [Laravel documentation](https://laravel.com/docs) is thorough, complete, and makes it a breeze to get started learning the framework.

If you're not in the mood to read, [Laracasts](https://laracasts.com) contains over 900 video tutorials on a range of topics including Laravel, modern PHP, unit testing, JavaScript, and more. Boost the skill level of yourself and your entire team by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for helping fund on-going Laravel development. If you are interested in becoming a sponsor, please visit the Laravel [Patreon page](http://patreon.com/taylorotwell):

- **[Vehikl](http://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[British Software Development](https://www.britishsoftware.co)**
- **[Styde](https://styde.net)**
- [Fragrantica](https://www.fragrantica.com)
- [SOFTonSOFA](https://softonsofa.com/)
- [User10](https://user10.com)
- [Soumettre.fr](https://soumettre.fr/)

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](http://laravel.com/docs/contributions).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell at taylor@laravel.com. All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).
