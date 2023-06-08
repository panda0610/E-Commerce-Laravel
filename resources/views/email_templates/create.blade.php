{{ Form::open(array('url' => 'email_template','method' =>'post')) }}
<div class="row">
    <div class="form-group col-md-12">
        {{Form::label('name',__('Name'))}}
        {{Form::text('name',null,array('class'=>'form-control font-style','required'=>'required'))}}
    </div>
    <div class="form-group col-md-12 text-right">
        {{--        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Cancel')}}</button>--}}
        {{Form::submit(__('Create'),array('class'=>'btn btn-primary'))}}
    </div>
</div>
{{ Form::close() }}
