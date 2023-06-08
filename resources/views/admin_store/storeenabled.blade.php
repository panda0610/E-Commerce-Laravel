
{{Form::model($users, array('route' => array('store-resource.display', $users->id), 'method' => 'PUT','enctype'=>'multipart/form-data')) }}
@csrf
@method('put')
<div>
	<p>This action can not be undone. Do you want to continue?</p>
	</div>
<div class="form-group text-right">
</div>
<div class="form-group col-12 d-flex justify-content-end col-form-label">
    <input type="button" value="{{__('Cancel')}}" class="btn btn-secondary btn-light" data-bs-dismiss="modal">
    <button class="btn btn-primary ms-2" value="{{$users->store_display}}" type="submit">{{ __('Yes') }}</button>
</div>

{{Form::close()}}
