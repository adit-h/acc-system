<x-app-layout :assets="$assets ?? []">
    <div>
        <?php
        $id = $id ?? null;
        ?>
        @if(isset($id))
        {!! Form::model($data, ['route' => ['transLimit.update', $id], 'method' => 'patch']) !!}
        @else
        {!! Form::open(['route' => ['transLimit.store'], 'method' => 'post']) !!}
        @endif
        <div class="row">
            <div class="col-xl-12 col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">{{$id !== null ? 'Ubah Limit' : 'Limit Transaksi' }}</h4>
                        </div>
                        <div class="card-action">
                            <!-- <a href="{{route('transLimit.index')}}" class="btn btn-sm btn-primary" role="button">Back</a> -->
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="new-user-info">
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label class="form-label" for="date_start">Tanggal Awal : <span class="text-danger">*</span></label>
                                    {{ Form::text('date_start', (old('date_start') ? old('date_start') : !empty($data)) ? date('d-m-Y', strtotime($data->date_start)) : '', ['class' => 'form-control vanila-datepicker', 'placeholder' => 'Pilih Tanggal', 'required']) }}
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="form-label" for="date_end">Tanggal Akhir : <span class="text-danger">*</span></label>
                                    {{ Form::text('date_end', (old('date_end') ? old('date_end') : !empty($data)) ? date('d-m-Y', strtotime($data->date_end)) : '', ['class' => 'form-control vanila-datepicker', 'placeholder' => 'Pilih Tanggal', 'required']) }}
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="form-label" for="received_from">Status : <span class="text-danger">*</span></label>
                                    {{ Form::select('status', $slist, old('status') ? old('status') : $data->status ?? '', ['class' => 'form-control', 'required', 'placeholder' => 'Pilih Status']) }}
                                </div>
                            </div>
                            <hr>
                            <button type="submit" class="btn btn-primary">{{$id !== null ? 'Update' : 'Save' }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {!! Form::close() !!}
    </div>
</x-app-layout>
