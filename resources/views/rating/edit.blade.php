{{Form::model($rating, array('route' => array('rating.update', $rating->id), 'method' => 'PUT')) }}
<div class="row">
    <div class="col-12">
        <div class="form-group">
            {{Form::label('name',__('Name')) }}
            {{Form::text('name',null,array('class'=>'form-control','placeholder'=>__('Enter Name'),'required'=>'required'))}}
        </div>
    </div>
    <div class="col-12">
        <div class="form-group">
            {{Form::label('title',__('Title')) }}
            {{Form::text('title',null,array('class'=>'form-control','placeholder'=>__('Enter Title'),'required'=>'required'))}}
        </div>
    </div>
    <div class="col-sm-12 pb-2">
        {{Form::label('title',__('Ratting')) }}
        <div id="rating_div">
            <div class="rate pl-0">
                <input type="radio" class="rating" id="star5" name="rate" value="5" {{($rating->ratting == '5')?'checked':''}}>
                <label for="star5" title="text">5 stars</label>
                <input type="radio" class="rating" id="star4" name="rate" value="4" {{($rating->ratting == '4')?'checked':''}}>
                <label for="star4" title="text">4 stars</label>
                <input type="radio" class="rating" id="star3" name="rate" value="3" {{($rating->ratting == '3')?'checked':''}}>
                <label for="star3" title="text">3 stars</label>
                <input type="radio" class="rating" id="star2" name="rate" value="2" {{($rating->ratting == '2')?'checked':''}}>
                <label for="star2" title="text">2 stars</label>
                <input type="radio" class="rating" id="star1" name="rate" value="1" {{($rating->ratting == '1')?'checked':''}}>
                <label for="star1" title="text">1 star</label>
            </div>
        </div>
    </div>
    <div class="col-12">
        <div class="form-group">
            {{Form::label('description',__('Description')) }}
            {{Form::textarea('description',null,array('class'=>'form-control','rows'=>3,'placeholder'=>__('Enter Description'),'required'=>'required'))}}
        </div>
    </div>
    <div class="form-group col-12 d-flex justify-content-end col-form-label">
        <input type="button" value="{{__('Cancel')}}" class="btn btn-secondary btn-light" data-bs-dismiss="modal">
        <input type="submit" value="{{__('Save')}}"  id="saverating" class="btn btn-primary ms-2">
    </div>
</div>
{{Form::close()}}
