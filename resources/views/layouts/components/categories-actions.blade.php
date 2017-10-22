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