@extends('storefront.layout.theme3')
@section('page-title')
    {{__('Cart')}}
@endsection
@section('content')
@section('head-title')
    {{__('Welcome').', '.\Illuminate\Support\Facades\Auth::guard('customers')->user()->name}}
@endsection
@section('content')
  @if($storethemesetting['enable_header_img'] == 'on')  
  <div class="home-banner-slider">
    @if(isset($storethemesetting['enable_banner_img']) && $storethemesetting['enable_banner_img'] == 'on')
            <div class="banner-img" width="660" height="766" style="background: url({{asset(Storage::url('uploads/store_logo/'.(!empty($storethemesetting['banner_img'])?$storethemesetting['banner_img']:'header_img_3.png')))}});"></div>
        @endif
    <div class="container">
                <div class="row">
                    <div class="col-lg-6 col-md-12 col-sm-12 p-0">
                        <h1 class=" mt-4 store-title t-secondary w-75">{{__('Products you purchased')}}</h1>
                        <div class="row mt-5">
                            <div class="col-lg-6 col-md-6 col-sm-12">
                                <div class="black-border"></div>
                                <ul class="banner-list">
                                    <a href="{{route('store.slug',$store->slug)}}" class="btn btn-sm btn-black btn-icon" >
                                        <span class="btn-inner--text text-white">{{__('Back to home')}}</span>
                                        <span class="btn-inner--icon">
                                        <i class="fas fa-angle-right"></i>
                                    </span>
                                    </a>
                                </ul>
                            </div>
                        </div>
                       

                    </div>
                </div>
            </div>
        </div> 
          
            
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
