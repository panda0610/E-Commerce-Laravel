<form method="post" action="{{ route('product-coupon.update', $productCoupon->id) }}" id="product-coupon-store">
    @csrf
    @method('PUT')
    <div class="row">
        <div class="form-group col-md-12">
            {{Form::label('name',__('Name'),array('class'=>'form-label'))}}
            {{Form::text('name',$productCoupon->name,array('class'=>'form-control','placeholder'=>__('Enter Name'),'required'=>'required'))}}
        </div>
        <div class="form-group col-md-12">
            <div class="form-check form-switch">
                <input type="checkbox" class="form-check-input" name="enable_flat" id="enable_flat" {{ ($productCoupon['enable_flat'] == 'on') ? 'checked=checked' : '' }}>
                {{-- <label class="custom-control-label col-form-label" for="enable_flat"></label> --}}
                {{Form::label('enable_flat',__('Flat Discount'),array('class'=>'form-check-label mb-3')) }}
            </div>
        </div>

        <div class="form-group col-md-6 nonflat_discount">
            {{Form::label('discount',__('Discount') ,array('class'=>'col-form-label')) }}
            {{Form::number('discount',$productCoupon->discount,array('class'=>'form-control','step'=>'0.01','placeholder'=>__('Enter Discount')))}}
            <span class="small">{{__('Note: Discount in Percentage')}}</span>
        </div>
        <div class="form-group col-md-6 flat_discount" style="display: none;">
            {{Form::label('pro_flat_discount',__('Flat Discount') ,array('class'=>'col-form-label')) }}
            {{Form::number('pro_flat_discount',$productCoupon->flat_discount,array('class'=>'form-control','step'=>'0.01','placeholder'=>__('Enter Flat Discount')))}}
            <span class="small">{{__('Note: Discount in Value')}}</span>
        </div>
        <div class="form-group col-md-6">
            <label for="limit" class="col-form-label">{{__('Limit')}}</label>
            <input type="number" name="limit" class="form-control" required value="{{$productCoupon->limit}}">
        </div>
        <div class="form-group col-md-12" id="auto">
            {{Form::label('limit',__('Code') ,array('class'=>'col-form-label'))}}
            <div class="input-group">
                {{Form::text('code',$productCoupon->code,array('class'=>'form-control','id'=>'auto-code','required'=>'required'))}}
                <button class="btn btn-outline-secondary" type="button" id="code-generate"><i class="fa fa-history pr-1"></i>{{__(' Generate')}}</button>
            </div>
        </div>
        <div class="form-group col-12 d-flex justify-content-end col-form-label">
            <input type="button" value="{{__('Cancel')}}" class="btn btn-secondary btn-light" data-bs-dismiss="modal">
            <input type="submit" value="{{__('Update')}}" class="btn btn-primary ms-2">
        </div>
    </div>
</form>

