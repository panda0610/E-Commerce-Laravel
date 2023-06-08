@extends('storefront.layout.theme5')
@section('page-title')
    {{__('Cart')}}
@endsection
@section('content')
@section('head-title')
    {{__('Welcome').', '.\Illuminate\Support\Facades\Auth::guard('customers')->user()->name}}
@endsection
@section('content')
   
    {{--HEADER IMG--}}
    @if($storethemesetting['enable_header_img'] == 'on')
        <section class="contain-product container">
            <div class="row">
                <div class="col-lg-4 col-md-12">
                    <div class="banner-contain">
                        <h1>{{__('Products you purchased')}}</h1>
                        <p>
                        </p>
                        <a href="{{route('store.slug',$store->slug)}}" class="btn btn-sm btn-primary btn-icon shadow hover-shadow-lg hover-translate-y-n3" id="pro_scroll">
                            <span class="btn-inner--text">{{__('Back to home')}}</span>
                            <span class="btn-inner--icon">
                                <i class="fas fa-shopping-basket"></i>
                        </span>
                        </a>
                    </div>
                </div>
                
            </div>
        </section>
    @endif

    
    <section class="slice slice-lg delimiter-bottom">
        <div class="container">
            
                @if(!empty($orders) && count($orders) > 0)
                    <div class="card">
                        <div class="table-responsive">
                            <table class="table align-items-center">
                                <thead>
                                <tr>
                                    <th scope="col">{{__('Order')}}</th>
                                    <th scope="col" class="sort">{{__('Date')}}</th>
                                    <th scope="col" class="sort">{{__('Value')}}</th>
                                    <th scope="col" class="sort">{{__('Payment Type')}}</th>
                                    <th scope="col" class="text-right">{{__("Action")}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($orders as $order)
                                    <tr>
                                        <th scope="row">
                                            <a href="{{route('customer.order',[$store->slug,Crypt::encrypt($order->id)])}}" class="btn btn-sm btn-white btn-icon rounded-pill text-dark">
                                                <span class="btn-inner--text">{{'#'.$order->order_id}}</span>
                                            </a>
                                        </th>
                                        <td class="order">
                                            <span class="h6 text-sm font-weight-bold mb-0">{{\App\Models\Utility::dateFormat($order->created_at)}}</span>
                                        </td>
                                       
                                        <td>
                                            <span class="value text-sm mb-0">{{\App\Models\Utility::priceFormat($order->price)}}</span>
                                        </td>
                                        <td>
                                            <span class="taxes text-sm mb-0">{{$order->payment_type}}</span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center justify-content-end">
                                                @if($order->status != 'Cancel Order')
                                                    <button type="button" class="btn btn-sm {{($order->status == 'pending')?'btn-soft-info':'btn-soft-success'}} btn-icon rounded-pill">
                                                        <span class="btn-inner--icon">
                                                         @if($order->status == 'pending')
                                                                <i class="fas fa-check soft-success"></i>
                                                            @else
                                                                <i class="fa fa-check-double soft-success"></i>
                                                            @endif
                                                        </span>
                                                        @if($order->status == 'pending')
                                                            <span class="btn-inner--text">
                                                                {{__('Pending')}}: {{\App\Models\Utility::dateFormat($order->created_at)}}
                                                            </span>
                                                        @else
                                                            <span class="btn-inner--text">
                                                                {{__('Delivered')}}: {{\App\Models\Utility::dateFormat($order->updated_at)}}
                                                            </span>
                                                        @endif
                                                    </button>
                                                @else
                                                    <button type="button" class="btn btn-sm btn-soft-danger btn-icon rounded-pill">
                                                        <span class="btn-inner--icon">
                                                            @if($order->status == 'pending')
                                                                <i class="fas fa-check soft-success"></i>
                                                            @else
                                                                <i class="fa fa-check-double soft-success"></i>
                                                            @endif
                                                        </span>
                                                        <span class="btn-inner--text">
                                                            {{__('Cancel Order')}}: {{\App\Models\Utility::dateFormat($order->created_at)}}
                                                        </span>
                                                    </button>
                                            @endif
                                            <!-- Actions -->
                                                <div class="actions ml-3">
                                                    <a href="{{route('customer.order',[$store->slug,Crypt::encrypt($order->id)])}}" class="action-item mr-2"  data-toggle="tooltip" data-title="{{__('Details')}}">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @else
                    <tr>
                        <td colspan="7">
                            <div class="text-center">
                                <i class="fas fa-folder-open text-gray" style="font-size: 48px;"></i>
                                <h2>{{ __('Opps...') }}</h2>
                                <h6> {!! __('No data Found.') !!} </h6>
                            </div>
                        </td>
                    </tr>
                @endif
            
        </div>
    </section>
@endsection
@push('script-page')
@endpush
