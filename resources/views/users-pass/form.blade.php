<x-app-layout :assets="$assets ?? []">
    <div>
        <?php
        $id = $id ?? null;
        ?>
        {!! Form::model($data, ['route' => ['userPass.update', $id], 'method' => 'patch' , 'enctype' => 'multipart/form-data']) !!}
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">Change Password</h4>
                        </div>
                        <div class="card-action">
                            <!-- <a href="{{route('users.index')}}" class="btn btn-sm btn-primary" role="button">Back</a> -->
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="new-user-info">
                            <h6 class="mb-3">New password will replace old one.</h6>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label class="form-label" for="pass">New Password:</label>
                                    {{ Form::password('password', ['class' => 'form-control', 'placeholder' => '******']) }}
                                </div>
                                <!-- <div class="form-group col-md-6">
                                <label class="form-label" for="rpass">Repeat Password:</label>
                                {{ Form::password('password_confirmation', ['class' => 'form-control', 'placeholder' => 'Repeat Password']) }}
                            </div> -->
                            </div>
                            <button type="submit" class="btn btn-primary">{{$id !== null ? 'Update' : 'Add' }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {!! Form::close() !!}
    </div>
</x-app-layout>
