{{Form::open(array('url'=>'product_categorie','method'=>'post'))}}
<div class="row">
    <div class="col-12">
        <div class="form-group">
            {{Form::text('name',null,array('class'=>'form-control','placeholder'=>__('Enter Product Category'),'required'=>'required'))}}
        </div>
    </div>
    <div class="w-100 text-right">
        {{Form::submit(__('Save'),array('class'=>'btn btn-sm btn-primary rounded-pill mr-auto'))}}
    </div>
</div>
{{Form::close()}}
