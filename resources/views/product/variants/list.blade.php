<div class="table-responsive">
    <table class="table">
        <thead>
        <tr class="text-center">
            @foreach($variantArray as $variant)
                <th><span>{{ ucwords($variant) }}</span></th>
            @endforeach
            <th><span>{{ __('Price') }}</span></th>
            <th><span>{{ __('Quantity') }}</span></th>
            {{--<th></th>--}}
        </tr>
        </thead>
        <tbody>
        @foreach($possibilities as $counter => $possibility)
            <tr>
                @foreach(explode(' : ', $possibility) as $key => $values)
                    <td>
                        <input type="text" autocomplete="off" spellcheck="false" class="form-control" value="{{ $values }}" name="verians[{{$counter}}][name]">
                    </td>
                @endforeach
                <td>
                    <input type="number" id="vprice_{{ $counter }}" autocomplete="off" spellcheck="false" placeholder="{{ __('Enter Price') }}" class="form-control" name="verians[{{$counter}}][price]">
                </td>
                <td>
                    <input type="number" id="vquantity_{{ $counter }}" autocomplete="off" spellcheck="false" placeholder="{{ __('Enter Quantity') }}" class="form-control" name="verians[{{$counter}}][qty]">
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
