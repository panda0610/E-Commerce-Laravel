@extends('layouts.admin')
@section('page-title')
    {{__('Location')}}
@endsection
@section('title')
    <div class="d-inline-block">
        <h5 class="h5 d-inline-block text-white font-weight-bold mb-0 ">{{__('Location')}}</h5>
    </div>
@endsection
@section('action-btn')

@endsection
@section('filter')
@endsection
@section('content')
    <div class="card">
        <!-- Table -->
        <div class="table-responsive">
            <div class="employee_menu view_employee">
                <div class="card-header actions-toolbar border-0">
                    <div class="row justify-content-between align-items-center">
                        <div class="col">
                            <h6 class="d-inline-block mb-0 text-capitalize">{{__('Location')}}</h6>
                        </div>
                        <div class="col text-right">
                            <div class="actions">
                                <div class="rounded-pill d-inline-block search_round">
                                    <div class="input-group input-group-sm input-group-merge input-group-flush">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-transparent"><i class="fas fa-search"></i></span>
                                        </div>
                                        <input type="text" id="user_keyword" class="form-control form-control-flush search-user" placeholder="{{__('Search Location')}}">
                                    </div>
                                </div>
                                <a href="#" data-size="lg" data-url="{{ route('location.create') }}" data-ajax-popup="true" data-title="{{__('Create New Location')}}" class="btn btn-sm btn-white btn-icon-only rounded-circle">
                                    <i class="ti ti-plus"></i></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Table -->
                <div class="table-responsive">
                    <table class="table align-items-center employee_tableese">
                        <thead>
                        <tr>
                            <th scope="col" class="sort" data-sort="name">{{__('Name')}}</th>
                            <th scope="col" class="sort" data-sort="name">{{__('Created At')}}</th>
                            <th class="text-right">{{__('Action')}}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($locations as $location)
                            <tr data-name="{{$location->name}}">
                                <td class="sorting_1">{{$location->name}}</td>
                                <td class="sorting_1">{{\App\Models\Utility::dateFormat($location->created_at)}}</td>
                                <td class="action text-right">
                                    <a href="#" data-size="lg" data-url="{{ route('location.edit',$location->id) }}" data-toggle="tooltip" data-original-title="{{__('Edit')}}" data-ajax-popup="true" data-title="{{__('Edit type')}}" class="action-item">                                        <i class="far fa-edit"></i>
                                    </a>
                                    <a href="#" class="action-item" data-toggle="tooltip" data-original-title="{{__('Delete')}}" data-confirm="{{__('Are You Sure?').' | '.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$location->id}}').submit();">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                    {!! Form::open(['method' => 'DELETE', 'route' => ['location.destroy', $location->id],'id'=>'delete-form-'.$location->id]) !!}
                                    {!! Form::close() !!}
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('script-page')
    <script>
        $(document).ready(function () {
            $(document).on('keyup', '.search-user', function () {
                var value = $(this).val();
                $('.employee_tableese tbody>tr').each(function (index) {
                    var name = $(this).attr('data-name');
                    if (name.includes(value)) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            });
        });
    </script>
@endpush
