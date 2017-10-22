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
                    <div class="panel-heading">Categorias - Editar</div>

                    <div class="panel-body">
                        <form method="post" action="{{ route('categories.update', $category) }}">
                            {{ csrf_field() }}
                            {{ method_field('PUT') }}
                            @include('layouts.components.name', ['data' => $category])
                            @include('layouts.components.save-button')
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection