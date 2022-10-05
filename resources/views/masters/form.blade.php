<x-app-layout :assets="$assets ?? []">
   <div>
      <?php
         $id = $id ?? null;
      ?>
      @if(isset($id))
      {!! Form::model($data, ['route' => ['master.update', $id], 'method' => 'patch']) !!}
      @else
      {!! Form::open(['route' => ['master.store'], 'method' => 'post']) !!}
      @endif
      <div class="row">
         <div class="col-xl-12 col-lg-12">
            <div class="card">
               <div class="card-header d-flex justify-content-between">
                  <div class="header-title">
                     <h4 class="card-title">{{$id !== null ? 'Ubah Akun' : 'Akun Baru' }}</h4>
                  </div>
                  <div class="card-action">
                        <a href="{{route('master.account')}}" class="btn btn-sm btn-primary" role="button">Back</a>
                  </div>
               </div>
               <div class="card-body">
                  <div class="new-user-info">
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label class="form-label" for="fname">Kode: <span class="text-danger">*</span></label>
                                {{ Form::text('code', old('code'), ['class' => 'form-control', 'placeholder' => 'Kode Akun', 'required']) }}
                            </div>
                            <div class="form-group col-md-6">
                                <label class="form-label" for="lname">Nama: <span class="text-danger">*</span></label>
                                {{ Form::text('name', old('name'), ['class' => 'form-control', 'placeholder' => 'Nama Akun' ,'required']) }}
                            </div>
                            <div class="form-group col-md-6">
                                <label class="form-label" for="cname">Kategori: <span class="text-danger">*</span></label>
                                {{ Form::select('category_id', $category, old('category_id') ? old('category_id') : $data->category_id ?? '', ['class' => 'form-control', 'required', 'placeholder' => 'Select Kategori Akun']) }}
                            </div>
                            <div class="form-group col-md-6">
                                <label class="form-label">Status:</label>
                                <div class="grid" style="--bs-gap: 1rem">
                                    <div class="form-check g-col-6">
                                        {{ Form::radio('status', 'active',old('status') || true, ['class' => 'form-check-input', 'id' => 'status-active']); }}
                                        <label class="form-check-label" for="status-active">
                                            Active
                                        </label>
                                    </div>
                                    <div class="form-check g-col-6">
                                        {{ Form::radio('status', 'inactive',old('status'), ['class' => 'form-check-input', 'id' => 'status-inactive']); }}
                                        <label class="form-check-label" for="status-inactive">
                                            Inactive
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <button type="submit" class="btn btn-primary">{{$id !== null ? 'Ubah' : 'Tambah' }} Akun</button>
                  </div>
               </div>
            </div>
         </div>
        </div>
        {!! Form::close() !!}
   </div>
</x-app-layout>
