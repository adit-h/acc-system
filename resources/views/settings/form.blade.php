<x-app-layout :assets="$assets ?? []">
    <div>
        <?php
        $id = $id ?? null;
        ?>
        @if(isset($id))
        {!! Form::model($data, ['route' => ['settings.update', $id], 'method' => 'patch']) !!}
        @else
        {!! Form::open(['route' => ['settings.store'], 'method' => 'post']) !!}
        @endif
        <div class="row">
            <div class="col-xl-12 col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">{{$id !== null ? 'Ubah Settings' : 'Settings Baru' }}</h4>
                        </div>
                        <div class="card-action">
                            <a href="{{route('settings.index')}}" class="btn btn-sm btn-primary" role="button">Back</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="new-user-info">
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label class="form-label" for="trans_date">Key: <span class="text-danger">*</span></label>
                                    {{ Form::text('key', old('key'), ['class' => 'form-control', 'placeholder' => 'Key field', 'required']) }}
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="form-label" for="value">Value: <span class="text-danger">*</span></label>
                                    {{ Form::text('value', old('value'), ['class' => 'form-control', 'placeholder' => 'Value', 'required']) }}
                                </div>
                            </div>
                            <hr>
                            <button type="submit" class="btn btn-primary">{{$id !== null ? 'Ubah' : 'Tambah' }} Settings</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {!! Form::close() !!}
    </div>
</x-app-layout>
