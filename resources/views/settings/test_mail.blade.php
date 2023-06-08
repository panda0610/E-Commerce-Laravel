{{ Form::open(array('route' => array('test.send.mail'))) }}
<div class="row">   
    <div class="form-group col-md-12">
        {{ Form::label('email', __('Email'),array('class'=>'col-form-label')) }}
        {{ Form::text('email', '', array('class' => 'form-control','required'=>'required')) }}
        @error('email')
        <span class="invalid-email" role="alert">
            <strong class="text-danger">{{ $message }}</strong>
        </span>
        @enderror
    </div>
</div>
<div class="modal-footer p-0">
    <div class="form-group col-12 d-flex justify-content-end col-form-label mb-0">
        <input type="button" value="{{__('Cancel')}}" class="btn btn-secondary btn-light" data-bs-dismiss="modal">
        <input type="submit" value="{{__('Send')}}" class="btn btn-primary ">
    </div>
</div>
{{ Form::close() }}
