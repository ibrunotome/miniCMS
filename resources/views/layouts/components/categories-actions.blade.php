<a href="{{ route('categories.edit', $model) }}" class="label label-sm label-warning" title="Editar"><i
            class="fa fa-edit"></i></a>
<a href="{{ url('/categories/' . $model->id . '/destroy') }}" class="label label-sm label-danger" title="Deletar"><i
            class="fa fa-times"></i></a>