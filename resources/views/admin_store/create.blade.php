{{Form::open(array('url'=>'store-resource','method'=>'post'))}}
<div class="row">
   <!--  @if(\Auth::user()->type == 'super admin')
        <div class="col-12">
            <div class="form-group">
                {{Form::label('store_enable',__('Store Display'),array('class'=>'form-label'))}}
                <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" name="is_store_enabled" id="is_store_enabled">
                    <label class="custom-control-label form-control-label" for="is_store_enabled"></label>
                </div>
            </div>
        </div>
    @endif -->

    <div class="col-12">
        <div class="form-group">
            {{Form::label('store_name',__('Store Name'),array('class'=>'form-label'))}}

            {{Form::text('store_name',null,array('class'=>'form-control','placeholder'=>__('Enter Store Name'),'required'=>'required'))}}
        </div>
    </div>
    @if(\Auth::user()->type == 'super admin')
    <div class="col-12">
        <div class="form-group">
            {{Form::label('name',__('Name'),array('class'=>'form-label'))}}
            {{Form::text('name',null,array('class'=>'form-control','placeholder'=>__('Enter Name'),'required'=>'required'))}}
        </div>
    </div>
    <div class="col-12">
        <div class="form-group">
            {{Form::label('email',__('Email'),array('class'=>'form-label'))}}
            {{Form::email('email',null,array('class'=>'form-control','placeholder'=>__('Enter Email'),'required'=>'required'))}}
        </div>
    </div>
    <div class="col-12">
        <div class="form-group">
            {{Form::label('password',__('Password'),array('class'=>'form-label'))}}
            {{Form::password('password',array('class'=>'form-control','placeholder'=>__('Enter Password'),'required'=>'required'))}}
        </div>
    </div>
    @endif
    <div class="form-group col-12 d-flex justify-content-end col-form-label">
        <input type="button" value="{{__('Cancel')}}" class="btn btn-secondary btn-light" data-bs-dismiss="modal">
        <input type="submit" value="{{__('Save')}}" class="btn btn-primary ms-2">
    </div>
</div>
{{Form::close()}}
