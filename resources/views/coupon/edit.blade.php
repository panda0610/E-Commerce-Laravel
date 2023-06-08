<form method="post" action="{{ route('coupons.update', $coupon->id) }}">
    @csrf
    @method('PUT')
    <div class="row">
        <div class="form-group col-md-12">
            <label for="name" class="form-label">{{ __('Name') }}</label>
            <input type="text" name="name" class="form-control" required value="{{ $coupon->name }}">
        </div>

        <div class="form-group col-md-6">
            <label for="discount" class="form-label">{{ __('Discount') }}</label>
            <input type="number" name="discount" class="form-control" required step="0.01"
                value="{{ $coupon->discount }}">
            <span class="small">{{ __('Note: Discount in Percentage') }}</span>
        </div>
        <div class="form-group col-md-6">
            <label for="limit" class="form-label">{{ __('Limit') }}</label>
            <input type="number" name="limit" class="form-control" required value="{{ $coupon->limit }}">
        </div>
        <div class="form-group col-md-12" id="auto">
            <label for="code" class="form-label">{{ __('Code') }}</label>
            <div class="input-group">

                <input class="form-control" name="code" type="text" id="auto-code" value="{{ $coupon->code }}">
                <button type="button" class="btn btn-outline-secondary" id="code-generate"><i class="fa fa-history pr-1"></i>
                    {{ __('Generate') }}</button>

            </div>
        </div>
        <div class="form-group col-12 d-flex justify-content-end col-form-label">
            <input type="button" value="{{ __('Cancel') }}" class="btn btn-secondary btn-light"
                data-bs-dismiss="modal">
            <input type="submit" value="{{ __('Update') }}" class="btn btn-primary ms-2">
        </div>
    </div>
</form>
