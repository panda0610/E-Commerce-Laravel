{{ Form::model($plan, ['route' => ['plans.update', $plan->id],'method' => 'PUT','enctype' => 'multipart/form-data']) }}
@csrf
@method('put')

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            {{ Form::label('name', __('Name'), ['class' => 'col-form-label']) }}
            {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => __('Enter Name')]) !!}
        </div>
    </div>
    @if ($plan->price > 0)
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('price', __('Price'), ['class' => 'col-form-label']) }}
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span
                            class="input-group-text">{{ env('CURRENCY_SYMBOL') ? env('CURRENCY_SYMBOL') : '$' }}</span>
                    </div>
                    {!! Form::number('price', null, ['class' => 'form-control', 'id' => 'monthly_price', 'min' => '0', 'placeholder' => __('Enter Price')]) !!}
                </div>
            </div>
        </div>
    @endif
    <div class="col-md-6">
        <div class="form-group">
            {{ Form::label('image', __('Image'), ['class' => 'col-form-label']) }}
            {{ Form::file('image', ['class' => 'form-control']) }}
        </div>
    </div>
    <div class="form-group col-md-6">
        {{ Form::label('duration', __('Duration'), ['class' => 'col-form-label']) }}
        {!! Form::select('duration', $arrDuration, null, ['class' => 'form-control multi-select', 'required' => 'required']) !!}
    </div>
    <div class="col-6">
        <div class="form-group">
            {{ Form::label('max_stores', __('Maximum stores'), ['class' => 'col-form-label']) }}
            {!! Form::text('max_stores', null, ['class' => 'form-control', 'id' => 'max_stores', 'placeholder' => __('Enter Max Stores')]) !!}
            <span><small>{{ __("Note: '-1' for Unlimited") }}</small></span>
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            {{ Form::label('max_products', __('Maximum Product Per Store'), ['class' => 'col-form-label']) }}
            {!! Form::text('max_products', null, ['class' => 'form-control', 'id' => 'max_products', 'placeholder' => __('Enter Products Per Store')]) !!}
            <span><small>{{ __("Note: '-1' for Unlimited") }}</small></span>
        </div>
    </div>
    <div class="col-6">
        <div class="custom-control form-switch pt-2">
            <input type="checkbox" class="form-check-input" name="enable_custdomain" id="enable_custdomain"
                {{ $plan['enable_custdomain'] == 'on' ? 'checked=checked' : '' }}>
            <label class="custom-control-label form-check-label"
                for="enable_custdomain">{{ __('Enable Domain') }}</label>
        </div>
    </div>
    <div class="col-6">
        <div class="custom-control form-switch pt-2">
            <input type="checkbox" class="form-check-input" name="enable_custsubdomain" id="enable_custsubdomain"
                {{ $plan['enable_custsubdomain'] == 'on' ? 'checked=checked' : '' }}>
            <label class="custom-control-label form-check-label"
                for="enable_custsubdomain">{{ __('Enable Sub Domain') }}</label>
        </div>
    </div>
    <div class="col-6">
        <div class="custom-control form-switch pt-2">
            <input type="checkbox" class="form-check-input" name="additional_page" id="additional_page"
                {{ $plan['additional_page'] == 'on' ? 'checked=checked' : '' }}>
            <label class="custom-control-label form-check-label"
                for="additional_page">{{ __('Enable Additional Page') }}</label>
        </div>
    </div>
    <div class="col-6">
        <div class="custom-control form-switch pt-2">
            <input type="checkbox" class="form-check-input" name="blog" id="blog"
                {{ $plan['blog'] == 'on' ? 'checked=checked' : '' }}>
            <label class="custom-control-label form-check-label" for="blog">{{ __('Enable Blog') }}</label>
        </div>
    </div>
    <div class="col-6">
        <div class="custom-control form-switch pt-2">
            <input type="checkbox" class="form-check-input" name="shipping_method" id="shipping_method"
                {{ $plan['shipping_method'] == 'on' ? 'checked=checked' : '' }}>
            <label class="custom-control-label form-check-label"
                for="shipping_method">{{ __('Enable Shipping Method') }}</label>
        </div>
    </div>
    <div class="col-12 mt-3">
        <div class="form-group">
            {{ Form::label('description', __('Description'), ['class' => 'col-form-label']) }}
            {!! Form::textarea('description', null, ['class' => 'form-control', 'id' => 'description', 'rows' => 2, 'placeholder' => __('Enter Description')]) !!}
        </div>
    </div>
</div>
<div class="form-group col-12 d-flex justify-content-end col-form-label">
    <input type="button" value="{{ __('Cancel') }}" class="btn btn-secondary btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{ __('Update') }}" class="btn btn-primary ms-2">
</div>
</form>
