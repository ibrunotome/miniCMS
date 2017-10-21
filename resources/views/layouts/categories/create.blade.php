@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">Categorias - Adicionar</div>

                    <div class="panel-body">
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