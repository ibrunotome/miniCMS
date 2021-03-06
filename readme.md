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
            $table->string('name', 50)->unique();
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
        'name' => $faker->unique()->word
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

Vamos aproveitar e criar uma seed para nosso usuário, pois não queremos ter que cadastrá-lo a cada refresh no banco de dados.

```
php artisan make:seed UsersTableSeeder
```

Abra o arquivo criado database/seeds/CategoriesTableSeeder.php e edite-o para que ele faça a chamada da factory criada 
anteriormente:

```php
<?php

use App\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(User::class)->create([
            'name'     => 'Bruno Tomé',
            'email'    => 'ibrunotome@gmail.com',
            'password' => bcrypt('secretxxx')
        ]);
    }
}

```

Por fim, chame essa seeder e todas as outras que criar posteriormente, no arquivo database/seeds/DatabaseSeeder.php

```php
<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(CategoriesTableSeeder::class);
        $this->call(UsersTableSeeder::class);
    }
}

```

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
Seeding: UsersTableSeeder
```

## Rotas e controllers

O Laravel utiliza por padrão a arquitetura Model, View, Controller (MVC). Já criamos o modelo Category, ainda temos que 
criar as visões de interação e também editar o controller que foi criado automaticamente no momento em que rodamos o comando
php artisan make:model Category --all

A visão solicitará dados ao controller, que irá interagir com o model e devolver os dados de volta para a visão, porém 
o responsável pela ligação entre a camada view e controller ainda não foi editado, as rotas. A partir delas conseguimos 
entender todas as possíveis ações em nossa aplicação, veja abaixo:

```php
<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

```

É fácil entender o comportamento da aplicação pelo seu arquivo de rotas, no exemplo acima por exemplo, vemos que se a 
rota (url) for vazia (http://localhost:8000 em localhost, ou seudominio.x em produção), uma tela de boas vindas será chamada. 
Já se a url /home for chamada, a função index dentro de HomeController será encarregada de algo.

Vamos editar as rotas para que somente usuários logados possam interagir com as categorias. Edite o arquivo routes/web.php 
adicionando o seguinte conteúdo:

```php
Route::group(['middleware' => 'auth:web'], function () {
    Route::resource('categories', 'CategoryController', ['except' => 'show']);
});

```

Ou seja, usuários autenticados podem requisitar todo o resource (CRUD) localizado dentro de app/Http/Controllers/CategoriesController.php 

Abra o arquivo e veja que ele já possui um início da implementação dos métodos necessários para o CRUD, removemos a função 
show que serve apenas para exibição individual dos dados, então teremos as seguintes funções no arquivo:

- **index:** Listar o conteúdo da tabela categories.
- **create:** Retorna uma view que contém um formulário para criar uma category.
- **store:** Armazena no banco de dados uma nova category.
- **show:** Exibe individualmente uma categoria, podemos apagar essa função neste mini curso, pois não a utilizaremos.
- **edit:** Retorna uma view que contém um formulário para editar uma category.
- **update:** Atualiza uma category no banco de dados.
- **destroy:** Apaga uma category do banco de dados.

Como podemos ver, os métodos estão em branco, cabe a nós escolhermos a melhor maneira de implementá-los de acordo com a demanda.

Vamos começar a desenvolver os métodos.

## index

Primeiro acesse a url http://localhost:8000/categories, você verá uma tela em branco, pois o método index não contém nada, 
então vamos começar a retornar alguns dados para a tela. Vamos pegar todos os dados da tabela categories e retorná-los.

```php
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = Category::all();

        return $categories;
    }
```

Acesse novamente a url http://localhost:8000/categories, você verá todas as categorias cadastradas na tela, em formato json.
Cabe ao desenvolvedor criar uma interface para consumir esses dados no frontend. Mas o ponto aqui é, vejam como foi fácil 
capturar os dados do banco de dados, sem escrever uma única consulta mysql, pois quem cuidou disso foi o ORM eloquent.

Se houvessem milhares de categorias no banco de dados, ficaria inviável exibir todas ao mesmo tempo, então vamos substituir o 
método all() por:

```
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = Category::with([])->paginate();

        return $categories;
    }
```

Acesse novamente a url http://localhost:8000/categories, agora temos uma paginação em todos os dados. Porém queremos mesmo é 
listar todas essas categorias numa tabela, com campo de pesquisa e tudo mais, felizmente já existe um pacote gratuito 
que utiliza o datatables.net para exibir os dados, o pacote criado pelo usuário Yajra pode ser adicionado rodando os seguintes 
comandos na raiz do projeto:

```
composer require yajra/laravel-datatables-buttons
composer require yajra/laravel-datatables-oracle
php artisan vendor:publish
```

No último comando, utiliza a opção 0 para publicar todos os providers.

Vamos criar uma datatable para a tabela categories.

```
php artisan datatables:make Category --model
```

Vamos substituir o método index que implementamos anteriormente, pela chamada da datatable.

```php
    /**
     * Display a listing of the resource.
     *
     * @param CategoryDataTable $dataTable
     *
     * @return \Illuminate\Http\Response
     */
    public function index(CategoryDataTable $dataTable)
    {
        return $dataTable->render('layouts.categories.list');
    }
```

Edite o conteúdo do arquivo app/DataTables/CategoryDataTable.php como abaixo:

```php
<?php

namespace App\DataTables;

use App\Category;
use Yajra\DataTables\Services\DataTable;

class CategoryDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        return datatables($query)
            ->addColumn('action', function (Category $model) {
                return '<a href="/categories/' . $model->id . '/edit">Editar</a>';
            });
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Category $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Category $model)
    {
        return $model->newQuery()->select($this->getColumns());
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->addAction(['width' => '80px'])
            ->parameters($this->getBuilderParameters());
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        return [
            'id',
            'name',
            'created_at',
            'updated_at'
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'category_' . time();
    }
}

```

Apenas adicionamos uma url para edição na tabela.

Se tentarmos acessar a url novamente, veremos um erro nos avisando que a view categories/list não existe. Vamos criá-la 
em resources/views/layouts/categories/list.blade.php

```blade
@extends('layouts.app')

@push('stylesheets')
    <link href="{{ asset('css/datatables/dataTables.buttons.min.css') }}"
          rel="stylesheet">
    <link href="{{ asset('css/datatables/dataTables.responsive.min.css') }}"
          rel="stylesheet">
@endpush

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                @if (session('status'))
                    <div class="alert alert-success">
                        {{ session('status') }}
                    </div>
                @endif

                @if($errors->any())
                    @foreach ($errors->all() as $error)
                        <div class="has-error">{{ $error }}</div>
                    @endforeach
                @endif
                <div class="panel panel-default">
                    <div class="panel-heading">Categorias</div>

                    <div class="panel-body">
                        {!! $dataTable->table() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/datatables/dataTables.jquery.min.js') }}"></script>
    <script src="{{ asset('js/datatables/dataTables.bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/datatables/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('js/datatables/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/buttons.server-side.js') }}"></script>

    {!! $dataTable->scripts() !!}
@endpush
```
Podemos ver que a tabela requisita alguns assets (Css e js) que ainda não temos no projeto, devido a questão de curto 
tempo para esse minicurso, utilize-as prontas a partir do link abaixo:

**Pasta public:** https://www.dropbox.com/sh/srqsbxdy17mczub/AACbX6S6b50G2Tumh3QfoFrha?dl=0

Adicione as scripts e stylesheets no arquivo resources/views/layouts/app.blade.php

```blade
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    @stack('stylesheets')
</head>
<body>
<div id="app">
    <nav class="navbar navbar-default navbar-static-top">
        <div class="container">
            <div class="navbar-header">

                <!-- Collapsed Hamburger -->
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                        data-target="#app-navbar-collapse">
                    <span class="sr-only">Toggle Navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>

                <!-- Branding Image -->
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>
            </div>

            <div class="collapse navbar-collapse" id="app-navbar-collapse">
                <!-- Left Side Of Navbar -->
                <ul class="nav navbar-nav">
                    &nbsp;
                </ul>

                <!-- Right Side Of Navbar -->
                <ul class="nav navbar-nav navbar-right">
                    <!-- Authentication Links -->
                    @guest
                        <li><a href="{{ route('login') }}">Login</a></li>
                        <li><a href="{{ route('register') }}">Register</a></li>
                        @else
                            <li class="{{ request()->is('categories') ? 'active' : '' }}"><a
                                        href="{{ route('categories.index') }}">Categorias</a></li>
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                                   aria-expanded="false">
                                    {{ Auth::user()->name }} <span class="caret"></span>
                                </a>

                                <ul class="dropdown-menu" role="menu">
                                    <li>
                                        <a href="{{ route('logout') }}"
                                           onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                            Logout
                                        </a>

                                        <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                              style="display: none;">
                                            {{ csrf_field() }}
                                        </form>
                                    </li>
                                </ul>
                            </li>
                            @endguest
                </ul>
            </div>
        </div>
    </nav>

    @yield('content')
</div>

<!-- Scripts -->
<script src="{{ asset('js/app.js') }}"></script>
@stack('scripts')
</body>
</html>
```

Acesse http://localhost:8000/categories, uma tabela com todas as categorias será exibida, com funções de busca, ordenação e exportação já inclusas.

## create

Agora vamos implementar a opção de criar uma nova categoria, altere a função no CategoryController para:

```php
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('layouts.categories.create');
    }
```

Ela apenas retorna uma view, crie-a ao lado da list.blade.php, com o nome create.blade.php

```blade
@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                @if (session('status'))
                    <div class="alert alert-success">
                        {{ session('status') }}
                    </div>
                @endif

                @if($errors->any())
                    @foreach ($errors->all() as $error)
                        <li class="text-danger">{{ $error }}</li>
                    @endforeach
                @endif
                <div class="panel panel-default">
                    <div class="panel-heading">Categorias - Adicionar</div>

                    <div class="panel-body">
                        <form method="post" action="{{ route('categories.store') }}">
                            {{ csrf_field() }}
                            @include('layouts.components.name')
                            @include('layouts.components.save-button')
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
```

Ele inclui dois componentes, crie-os em resources/views/components

**name.blade.php**
```blade
<label for="name">Nome</label>
<div class="input-group">
    <span class="input-group-addon"><i class="fa fa-edit"></i></span>
    <input type="text"
           id="name"
           name="name"
           class="form-control"
           placeholder="Nome"
           value="{{ old('name') }}"
           required>
</div>
```

**save-button.blade.php**
```blade
<br>
<button type="submit" class="btn btn-success pull-right" value="Salvar">Salvar</button>
```

Temos a view pronta, agora temos que criar a função de store. Vamos validar os dados antes de salvá-los no banco de dados, 
faremos isso na request.

```
php artisan make:request CategoryCreateRequest
```

Abra o arquivo criado app/Http/Requests/CategoryCreateRequest.php e faça a seguinte validação, autorizando a criação de uma 
categoria apenas quando o nome foi passado como parâmetro, tendo no máximo 50 caracteres.

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoryCreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:50'
        ];
    }
}

```

## store

Agora vamos ao controller criar nossa função de store, que armazenará a categoria no banco de dados.

```php
    /**
     * Store a newly created resource in storage.
     *
     * @param CategoryCreateRequest|Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(CategoryCreateRequest $request)
    {
        $data = $request->all();

        Category::create($data);

        session()->flash('status', 'Categoria criada com sucesso!');

        return redirect(route('categories.index'));
    }
```

Teste se tudo funcionou.

## edit

Altere a função edit do controller para:

```php
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Category $category
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Category $category)
    {
        return view('layouts.categories.edit', compact('category'));
    }
``` 

O laravel irá pegar o id contido na rota (ex: categories/18/edit) e automaticamente buscar a categoria com essa id no 
banco de dados. Então basta devolvermos a variável para a view usando um compact.

Crie uma CategoryUpdateRequest.

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoryUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $id = request()->segment(2);

        return [
            'name' => 'required|string|max:50|unique:categories,name,' . $id
        ];
    }
}
```

## update

Por fim, altere o método update para:

```php
    /**
     * Update the specified resource in storage.
     *
     * @param CategoryUpdateRequest|Request $request
     * @param  \App\Category $category
     * 
     * @return \Illuminate\Http\Response
     */
    public function update(CategoryUpdateRequest $request, Category $category)
    {
        $data = $request->all();

        $category->update($data);

        session()->flash('status', 'Categoria editada com sucesso!');

        return redirect(route('categories.index'));
    }
```

## destroy

Para utilizarmos o método destroy seguindo as requests no formate RESTful, devemos enviar o método DELETE na requisição.
Altere o CategoryDataTable para:

```php
<?php

namespace App\DataTables;

use App\Category;
use Yajra\DataTables\Services\DataTable;

class CategoryDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        return datatables($query)
            ->addColumn('action', function (Category $model) {
                return view('layouts.components.categories-actions', compact('model'))->render();
            });
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Category $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Category $model)
    {
        return $model->newQuery()->select($this->getColumns());
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->addAction(['width' => '80px'])
            ->parameters($this->getBuilderParameters());
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        return [
            'id',
            'name',
            'created_at',
            'updated_at'
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'category_' . time();
    }
}
```

Crie o componente categories-actions.blade.php na pasta components.

```blade
<a href="{{ route('categories.edit', $model) }}" class="label label-sm label-warning" title="Editar"><i class="fa fa-edit"></i></a>
<a href="{{ route('categories.destroy', $model) }}"
   class="label label-sm label-danger"
   title="Deletar"
   onclick="event.preventDefault();document.getElementById('delete-form').submit();">
    <i class="fa fa-times"></i>
</a>

<form id="delete-form" action="{{ route('categories.destroy', $model) }}" method="POST"
      style="display: none;">
    {{ csrf_field() }}
    {{ method_field('DELETE') }}
</form>
```

Por fim, altere o método delete no controller para:

```php
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Category $category
     * 
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {
        Category::destroy($category->id);

        session()->flash('status', 'Categoria deletada com sucesso!');

        return redirect(route('categories.index'));
    }
```

Finalizamos o crud de categorias.

**Criem um CRUD de posts, basicamente o mesmo processo que fizemos para as categories, os campos da tabela serão**

```php
$table->string('title', 100);
$table->text('description');
$table->unsignedInteger('category_id');
$table->foreign('category_id')->references('id')->on('categories');
```

**E no modelo App\Post existirá um relacionamento:**

```php
public function category() {
    return $this->hasOne('App\Category', 'id', 'category_id');
}
```

**E no modelo App\Category existirá um relacionamento:**

```php
public function posts() {
    return $this->belongsToMany('App\Post');
}
```

**Como realizar queries com o relacionamento**

Na query, adicione o campo with.

$category = Category::with('posts')->get();

O resultado será todas as categorias com todos os seus posts aninhados.

$post = Post::with('category')->get();

O resultado será todos os posts com sua categoria aninhada.

## Dicas para usar em projetos

Por questões de pouco tempo não é possível mostrar muita coisa, segue abaixo dicas de como estruturar o projeto para que 
ele se torne mais escalável.

- **Service Layer:** Camada de serviços, abstraia seus métodos de store e update para essa camada. Basta criar uma pasta 
Services dentro de app/. E nela criar suas services. Um exemplo seria PostService.php, e nessa classe os métodos de store e 
update com os devidos tratamentos de erros, podendo ainda disparar notificações a partir desses métodos.

- **Notifications:** Pasta localizada em app/ com o intuito de reunir todas as classes de notificações do projeto. Uma notificação 
pode ser disparada em momentos como: Saldo baixo, um novo usuário se registrou, um usuário solicitou reset de senha, ocorreu uma exception no sistema.

- **Repository Layer:** Camada para abstração do banco de dados, criando um reaproveitamento de código muito maior e ainda tornando 
possível a troca de ORM de forma mais rápida.

- **Jobs:** Trabalhos que são muito demorados, podem ser jogados em filas, o Laravel faz isso muito bem, você dispara um job, ele
executa em background e depois você pode analizar facilmente os resultados, se foram positivos ou se o job falhou.

- **Horizon:** O horizon possui uma dashboard para analizar os jobs ditos acima. Do mesmo criador do Laravel.
Ex: https://www.dropbox.com/s/aoa8fb0yiwjhjlf/Screenshot%202017-10-25%2000.23.35.png?dl=0

- **Redis:** Redis é um servidor muito utilizado para gerenciar cache, sessions e as queues da aplicação. É muito rápido 
pois gerencia tudo na memória.

Também utilizando horizon e as queues (filas), um bom exemplo que posso dar é que com as configurações certas no redis, 
conseguimos gerar 35 mil boletos na celulaweb, em 4 minutos e meio.

Com o estudo e implementação correta do que está acima, é possível criar aplicações rápidas e escaláveis, mas isso é só a base, como diz 
um ditado "a cada dia 23 novos frameworks javascript são criados", esse ditado também serve para "a cada dia surgem 23 novas soluções/novidades" no ramo. 

**NÃO leve para produção projetos com apenas o conteúdo que foi mostrado no minicurso, ou seja, projetos sem camadas auxiliares como 
as mostradas acima, e sem o devido tratamento de erros, o que não fizemos nesse minicurso.**

## Dica de IDE

- **PhpStorm:** Para mim não há melhor, é paga porém você como estudante pode solicitar uma licença de 1 ano (ou enquanto estudar) em https://www.jetbrains.com/student/


## Dica de como colocar em produção

**Servidor**
- Digital Ocean: https://www.digitalocean.com
- Linode: https://www.linode.com
- Vultr: https://www.vultr.com

## Colocando em produção

### Configurações do servidor

* CentOS 7
* Nginx
* Php 7
* Zend OpCache
* Redis
* MariaDB
* Supervisor

### OneInStack

Para instalar nginx, php7, zend opcache, redis e mariadb, pode-se utilizar o script oneinstack:

https://github.com/lj2007331/oneinstack

## Dicas de estudo

O mini curso foi muito simples, 6 horas não dá pra mostrar quase nada e ainda não dá pra mostrar da forma "correta" como nos items acima, 
pra aprender Laravel de verdade recomendo os links abaixo:

- Laravel (Grátis): https://laravel.com/docs/master/
- Laracasts ($9 mês, en_US): https://laracasts.com
- SchoolOfNet (R$ 30 mês, pt_BR): https://schoolofnet.com
- CodeEducation (Preços únicos (800 + - reais), porém são projetos práticos, pt_BR): https://code.education
- CodeCasts (Grátis, pt_BR): https://www.youtube.com/channel/UCTluPqMkm90zyw6mCde561A/playlists 
- Laravel Brasil no facebook: https://www.facebook.com/groups/laravelbrasil/

# Fim

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
