<x-app-layout :assets="$assets ?? []">
   <div>
      <?php
         $id = $id ?? null;
      ?>
      @if(isset($id))
      {!! Form::model($data, ['route' => ['trans.out.update', $id], 'method' => 'patch']) !!}
      @else
      {!! Form::open(['route' => ['trans.out.store'], 'method' => 'post']) !!}
      @endif
      <div class="row">
         <div class="col-xl-12 col-lg-12">
            <div class="card">
               <div class="card-header d-flex justify-content-between">
                  <div class="header-title">
                     <h4 class="card-title">{{$id !== null ? 'Ubah Transaksi Keluar' : 'Transaksi Keluar Baru' }}</h4>
                  </div>
                  <div class="card-action">
                        <a href="{{route('trans.out.index')}}" class="btn btn-sm btn-primary" role="button">Back</a>
                  </div>
               </div>
               <div class="card-body">
                  <div class="new-user-info">
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label class="form-label" for="trans_date">Tanggal: <span class="text-danger">*</span></label>
                                {{ Form::text('trans_date', (old('trans_date') ? old('trans_date') : !empty($data)) ? date('d-m-Y', strtotime($data->trans_date)) : '', ['class' => 'form-control vanila-datepicker', 'placeholder' => 'Tanggal Transaksi', 'required']) }}
                            </div>
                            <div class="form-group col-md-6">
                                <label class="form-label" for="value">Nilai: <span class="text-danger">*</span></label>
                                {{ Form::text('value', old('value'), ['class' => 'form-control', 'placeholder' => 'Nilai Transaksi', 'required']) }}
                            </div>
                            <div class="form-group col-md-6">
                                <label class="form-label" for="received_from">Debit: <span class="text-danger">*</span></label>
                                {{ Form::select('receive_from', $acc_from, old('receive_from') ? old('receive_from') : $data->receive_from ?? '', ['class' => 'form-control', 'required', 'placeholder' => 'Pilih Akun']) }}
                            </div>
                            <div class="form-group col-md-6">
                                <label class="form-label" for="store_to">Kredit: <span class="text-danger">*</span></label>
                                {{ Form::select('store_to', $acc_to, old('store_to') ? old('store_to') : $data->store_to ?? '', ['class' => 'form-control', 'required', 'placeholder' => 'Pilih Akun']) }}
                            </div>
                            <div class="form-group col-md-6">
                                <label class="form-label" for="reference">Referensi:</label>
                                {{ Form::text('reference', old('reference'), ['class' => 'form-control', 'placeholder' => 'Nomor Referensi', '']) }}
                            </div>
                            <div class="form-group col-md-6">
                                <label class="form-label" for="description">Keterangan:</label>
                                {{ Form::text('description', old('description'), ['class' => 'form-control', 'placeholder' => 'Keterangan', '']) }}
                            </div>
                        </div>
                        <hr>
                        <button type="submit" class="btn btn-primary">{{$id !== null ? 'Ubah' : 'Tambah' }} Transaksi</button>
                  </div>
               </div>
            </div>
         </div>
        </div>
        {!! Form::close() !!}
   </div>
</x-app-layout>
