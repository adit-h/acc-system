<?php
    $id = $id ?? null;
?>
@if(isset($id))
{!! Form::model($data, ['route' => ['permission.update', $id], 'method' => 'patch']) !!}
@else
{!! Form::open(['route' => ['permission.store'], 'method' => 'post']) !!}
@endif
    <div class="form-group">
        <label class="form-label">Permission Title</label>
        {{ Form::text('title', old('title') ? old('title') : !empty($data) ? $data->title : '', ['class' => 'form-control','id' => 'permission-title', 'placeholder' => 'Permission Title', 'required']) }}
    </div>
    <button type="submit" class="btn btn-primary" data-bs-dismiss="modal">Save</button>
    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
{{ Form::close() }}
