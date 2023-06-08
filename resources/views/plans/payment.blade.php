@extends('layouts.admin')
@section('page-title')
    {{__('Payment')}}
@endsection
@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center mt-5">
            <div class="col-md-8">
                <div class="card payment-card">
                    <div class="card-body">

                        <form role="form" action="{{ route('prepare.payment') }}" method="post" class="require-validation" id="payment-form">
                            @csrf
                            <input type="hidden" name="code" value="{{\Illuminate\Support\Facades\Crypt::encrypt($plan->id)}}">

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="btn-group btn-group-toggle mt-5" data-toggle="buttons">
                                        <label class="btn btn-xl btn-primary active">
                                            <input type="radio" name="payment_processor" value="paypal" autocomplete="off" checked="">
                                            {{ __('Paypal') }}
                                        </label>
                                        <label class="btn btn-xl btn-primary">
                                            <input type="radio" name="payment_processor" value="stripe" autocomplete="off">
                                            {{ __('Stripe') }}
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="btn-group btn-group-toggle mt-5" data-toggle="buttons">
                                        <label class="btn btn-xl btn-primary active">
                                            <input type="radio" name="payment_type" id="one_time_type" value="one-time" autocomplete="off" checked="">
                                            {{ __('One Time') }}
                                        </label>
                                        <label class="btn btn-xl btn-primary">
                                            <input type="radio" name="payment_type" id="recurring_type" value="recurring" autocomplete="off">
                                            {{ __('Reccuring') }}
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mt-5">
                                <input type="text" id="coupon" name="coupon" class="form-control" placeholder="{{__('Enter Coupon Code Here')}}">
                            </div>
                            <button class="btn btn-primary btn-sm rounded-pill d-flex my-4 px-3 float-right" type="submit">
                                <i class="mdi mdi-cash-multiple mr-1"></i> {{__('Checkout')}} (<span class="final-price"></span>)
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <?php $stripe_session = Session::get('stripe_session');?>

    <?php if(isset($stripe_session) && $stripe_session): ?>
    <script src="https://js.stripe.com/v3/"></script>

    <script>
        var stripe = Stripe('{{ env('STRIPE_KEY') }}');
        stripe.redirectToCheckout({
            sessionId: '{{ $stripe_session->id }}',
        }).then((result) => {
        });
    </script>
    <?php endif ?>

    <script src="https://js.stripe.com/v3/"></script>
    <script type="text/javascript">

        $(document).on('change', 'input[name="payment_frequency"], input[name="payment_type"]', function (e) {
            var price = $('input[name="payment_frequency"]:checked').attr('data-price');
            var frequency = $('input[name="payment_frequency"]:checked').val();
            var type = $('input[name="payment_type"]:checked').val();

            var total = per = '';

            if (frequency == 'monthly') {
                var per = '/month';
                $('#recurring_type').parent().show();
            } else if (frequency == 'annual') {
                var per = '/year';
                $('#recurring_type').parent().show();
            }

            if (type == 'recurring') {
                var total = price + per;
            } else if (type == 'one-time') {
                var total = price;
            }
            $('.final-price').text(total);

        });

        $('input[name="payment_frequency"]:first').trigger('change');

        // Apply Coupon
        $(document).on('click', '.apply-coupon', function (e) {
            e.preventDefault();

            var ele = $(this);
            var coupon = ele.closest('.row').find('.coupon').val();

            if (coupon != '') {
                $.ajax({
                    url: '{{route('apply.coupon')}}',
                    datType: 'json',
                    data: {
                        plan_id: '{{ $plan->id }}',
                        coupon: coupon
                    },
                    success: function (data) {
                        $('#stripe_coupon, #paypal_coupon').val(coupon);
                        if (data.is_success) {
                            $('.coupon-tr').show().find('.coupon-price').text(data.discount_price);
                            $('.final-price').text(data.final_price);
                            show_toastr('Success', data.message, 'success');
                        } else {
                            $('.coupon-tr').hide().find('.coupon-price').text('');
                            $('.final-price').text(data.final_price);
                            show_toastr('Error', data.message, 'error');
                        }
                    }
                })
            } else {
                show_toastr('Error', '{{__('Invalid Coupon Code.')}}', 'error');
            }
        });

    </script>
@endpush
