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