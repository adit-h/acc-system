<x-app-layout :assets="$assets ?? []">
   <div>
      <?php
         $id = $id ?? null;
      ?>
      @if(isset($id))
      {!! Form::model($data, ['route' => ['budget.update', $id], 'method' => 'patch']) !!}
      @else
      {!! Form::open(['route' => ['budget.store'], 'method' => 'post']) !!}
      @endif
      <div class="row">
         <div class="col-xl-12 col-lg-12">
            <div class="card">
               <div class="card-header d-flex justify-content-between">
                  <div class="header-title">
                     <h4 class="card-title">{{$id !== null ? 'Ubah Budget' : 'Budget Baru' }}</h4>
                  </div>
                  <div class="card-action">
                        <a href="{{route('budget.index')}}" class="btn btn-sm btn-primary" role="button">Back</a>
                  </div>
               </div>
               <div class="card-body">
                  <div class="new-user-info">
                        <div class="row">
                        <div class="form-group col-md-6">
                                <label class="form-label" for="trans_date">Tanggal: <span class="text-danger">*</span></label>
                                {{ Form::text('trans_date', (old('trans_date') ? old('trans_date') : !empty($data)) ? date('d-m-Y', strtotime($data->trans_date)) : '', ['class' => 'form-control vanila-datepicker', 'placeholder' => 'Tanggal Budget', 'required']) }}
                                {{-- Form::text('trans_date', (old('trans_date') ? old('trans_date') : ''), ['class' => 'form-control vanila-datepicker', 'placeholder' => 'Tanggal Budget', 'required']) --}}
                            </div>
                            <div class="form-group col-md-6">
                                <label class="form-label" for="received_from">Akun: <span class="text-danger">*</span></label>
                                {{ Form::select('acc_id', $acc_list, old('acc_id') ? old('acc_id') : $data->acc_id ?? '', ['class' => 'form-control', 'required', 'placeholder' => 'Pilih Akun']) }}
                            </div>
                            <div class="form-group col-md-6">
                                <label class="form-label" for="value">Nilai: <span class="text-danger">*</span></label>
                                {{ Form::text('value', old('value'), ['class' => 'form-control', 'placeholder' => 'Nilai Budget', 'required']) }}
                            </div>
                            <div class="form-group col-md-6">
                                <label class="form-label" for="description">Keterangan:</label>
                                {{ Form::text('description', old('description'), ['class' => 'form-control', 'placeholder' => 'Keterangan', '']) }}
                            </div>
                        </div>
                        <hr>
                        <button type="submit" class="btn btn-primary">{{$id !== null ? 'Ubah' : 'Tambah' }} Budget</button>
                  </div>
               </div>
            </div>
         </div>
        </div>
        {!! Form::close() !!}
   </div>
</x-app-layout>
