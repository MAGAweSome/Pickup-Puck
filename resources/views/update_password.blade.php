@extends('layouts.app')

@section('content')

<!-- <div class="w-75 bg-light h-100"> -->
    <div class="m-5 card">
        <h1 class="text-center">Password Management</h1>
        <hr>

        <form action="" method="post" enctype="multipart/form-data">
            @csrf

            <div class="form-group mt-2">
                <label for="currentpasswordField">Current Password<label>
                <input type="password" id="currentpasswordField" name="current_password" class="form-control" placeholder="Current Password">
                @error("currentpasswordField")
                <span>{{ $message }}</span>
                @enderror
            </div>

            <div class="mt-2">
                <label for="newpasswordField">New Password</label>
                <input type="password" id="newpasswordField" name="new_password" class="form-control" placeholder="New Password">
                @error("newpasswordField")
                <span>{{ $message }}</span>
                @enderror
            </div>

            <div class="mt-2">
                <label for="confirmpasswordField">Confirm Password</label>
                <input type="password" id="confirmpasswordField" name="confirm_password" class="form-control" placeholder="Confirm new Password">
                @error("confirmpasswordField")
                <span>{{ $message }}</span>
                @enderror
            </div>

            <div class="mt-2">
                <input type="hidden" name="hidden_id" value="<?php //if(isset($id)) echo $id; ?>">
                <input type="hidden" name="token" value="<?php //if(function_exists('_token')) echo _token(); ?>">
                <button class="submit">{{ __("Update") }}</button>
            </div>
        </form>
    
    </div>

    <div class="m-5 card">
        <div class="card-header">
            <h1 class="text-center">Password Management</h1>
        </div>

        <div class="card-body">
            <form method="POST" enctype="multipart/form-data"> <!--action="{{ route('login') }}"-->
                @csrf

                <div class="row mb-3">
                    <label for="currentpasswordField" class="col-md-4 col-form-label text-md-end">{{ __('Current Password') }}</label>

                    <div class="col-md-6">
                    <input type="password" id="currentpasswordField" name="current_password" class="form-control" placeholder="Current Password">
                        @error('currentpasswordField')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <label for="newpasswordField" class="col-md-4 col-form-label text-md-end">{{ __('New Password') }}</label>

                    <div class="col-md-6">
                        <input type="password" id="newpasswordField" name="new_password" class="form-control" placeholder="New Password">
                        @error('newpasswordField')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <label for="confirmpasswordField" class="col-md-4 col-form-label text-md-end">{{ __('Confirm Password') }}</label>

                    <div class="col-md-6">
                    <input type="password" id="confirmpasswordField" name="confirm_password" class="form-control" placeholder="Confirm new Password">
                        @error('confirmpasswordField')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="row mb-0">
                    <div class="col-md-8 offset-md-4">
                        <input type="hidden" name="hidden_id" value="<?php //if(isset($id)) echo $id; ?>">
                        <input type="hidden" name="token" value="<?php //if(function_exists('_token')) echo _token(); ?>">
                        <button type="submit" class="btn btn-primary">
                            {{ __('Update') }}
                        </button>
                    </div>
                </div>

                <!-- <div class="row mb-0">
                    <div class="col-md-8 offset-md-4">
                        <button type="submit" class="btn btn-primary">
                            {{ __('Login') }}
                        </button>
                    </div>
                </div> -->
            </form>
        </div>
    </div>

<!-- </div> -->
@endsection
