@extends('layouts.admin')
@section('page-title')
    {{__('User Edit')}}
@endsection
@section('action-btn')
@endsection
@section('title')
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0 text-white">{{__('User Edit (')}} {{ $user->name}} {{')'}}</h5>
    </div>
@endsection
@section('content')
    <div class="row">
        <div class="col-auto">
            <!--account edit -->
            <div id="account_edit" class="tabs-card">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center h-40  ">
                            <div class="p-0">
                                <h6 class="mb-0">{{__('Overview')}}</h6>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        {{Form::model($user,array('route' => array('user.update', $user->id), 'method' => 'PUT')) }}
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    {{Form::label('name',__('Name')) }}
                                    {{Form::text('name',null,array('class'=>'form-control','placeholder'=>__('Enter Name'),'required'=>'required'))}}
                                    @error('name')
                                    <span class="invalid-name" role="alert">
                                <strong class="text-danger">{{ $message }}</strong>
                                </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    {{Form::label('title',__('Title')) }}
                                    {{Form::text('title',null,array('class'=>'form-control','placeholder'=>__('Enter Title'),'required'=>'required'))}}
                                    @error('title')
                                    <span class="invalid-title" role="alert">
                                <strong class="text-danger">{{ $message }}</strong>
                                </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    {{Form::label('email',__('Email')) }}
                                    {{Form::text('email',null,array('class'=>'form-control','placeholder'=>__('Enter Email'),'required'=>'required','disabled'))}}
                                    @error('email')
                                    <span class="invalid-email" role="alert">
                                <strong class="text-danger">{{ $message }}</strong>
                                </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    {{Form::label('phone',__('Phone')) }}
                                    {{Form::text('phone',null,array('class'=>'form-control','placeholder'=>__('Enter Phone'),'required'=>'required'))}}
                                    @error('phone')
                                    <span class="invalid-phone" role="alert">
                                <strong class="text-danger">{{ $message }}</strong>
                                </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-group">
                                    {{Form::label('name',__('Is Active')) }}
                                    <div>
                                        <input type="checkbox" class="align-middle" name="is_active" {{($user->is_active == 1)? 'checked': ''}}>
                                    </div>
                                </div>
                            </div>
                            <div class="w-100 mt-3 text-right">
                                {{Form::submit(__('Save'),array('class'=>'btn btn-sm btn-primary rounded-pill mr-auto'))}}
                            </div>
                        </div>
                        {{Form::close()}}
                    </div>
                </div>
            </div>
            <!--account edit end-->
        </div>
    </div>
@endsection

