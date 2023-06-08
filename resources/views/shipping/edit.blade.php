{{Form::model($shipping, array('route' => array('shipping.update', $shipping->id), 'method' => 'PUT')) }}
<div class="row">
    <div class="col-12">
        <div class="form-group">
            {{Form::label('name',__('Name'),array('class'=>'col-form-label')) }}
            {{Form::text('name',null,array('class'=>'form-control','placeholder'=>__('Enter Product Category')))}}
            @error('name')
            <span class="invalid-name" role="alert">
                    <strong class="text-danger">{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>
    <div class="col-12">
        <div class="form-group">
            {{Form::label('price',__('Price'),array('class'=>'col-form-label')) }}
            {{Form::text('price',null,array('class'=>'form-control','placeholder'=>__('Enter State Name')))}}
            @error('price')
            <span class="invalid-price" role="alert">
                    <strong class="text-danger">{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>
    <div class="col-12">
        <div class="form-group ">
            {{Form::label('Location',__('Location'),array('class'=>'col-form-label')) }}
            {{ Form::select('location[]', $locations,explode(',',$shipping->location_id), array('class' => 'form-control multi-select','id'=>'choices-multiple','multiple'=>'')) }}
        </div>
    </div>
</div>
<div class="form-group col-12 d-flex justify-content-end col-form-label">
    <input type="button" value="{{ __('Cancel') }}" class="btn btn-secondary btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{ __('Update') }}" class="btn btn-primary ms-2">
</div>
{{Form::close()}}
