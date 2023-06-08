{{Form::model($socialblog, array('route' => array('store.socialblog'), 'method' => 'post','enctype'=>'multipart/form-data','id'=>'blog_social_form')) }}
<div class="row">
    <div class="form-group col-md-12 enable_blog_button">
        {{Form::label('enable_social_button',__('Enable Blog Social Button'),array('class'=>'form-control-label mb-1')) }}
        <div class="custom-control custom-switch">
            <input type="checkbox" class="custom-control-input {{($socialblog->enable_social_button == 'on')?'data-check':''}}" name="enable_social_button" id="enable_social_button" {{($socialblog->enable_social_button == 'on')?'checked':''}}>
            <label class="custom-control-label form-control-label" for="enable_social_button"></label>
        </div>
            <input type="hidden" name="blog_id" value="{{$socialblog->id}}">
    </div>

    <div class="social-btn p-2" style="display: {{($socialblog->enable_social_button == 'on')?'block':'none'}}">
        <div class="row">
            <div class="col-6 mb-2">
                {{Form::label('enable_whatsapp',__('Enable Whatsapp'),array('class'=>'form-control-label mb-1')) }}
                <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" name="enable_whatsapp" id="enable_whatsapp" {{($socialblog->enable_whatsapp == 'on')?'checked':''}}>
                    <label class="custom-control-label form-control-label" for="enable_whatsapp"></label>
                </div>
            </div>
            <div class="col-6 mb-2">
                {{Form::label('enable_facebook',__('Enable Facebook'),array('class'=>'form-control-label mb-1')) }}
                <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" name="enable_facebook" id="enable_facebook" {{($socialblog->enable_facebook == 'on')?'checked':''}}>
                    <label class="custom-control-label form-control-label" for="enable_facebook"></label>
                </div>
            </div>
            <div class="col-6 mb-2">
                {{Form::label('enable_linkedIn',__('Enable LinkedIn'),array('class'=>'form-control-label mb-1')) }}
                <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" name="enable_linkedIn" id="enable_linkedIn" {{($socialblog->enable_linkedIn == 'on')?'checked':''}}>
                    <label class="custom-control-label form-control-label" for="enable_linkedIn"></label>
                </div>
            </div>
            <div class="col-6 mb-2">
                {{Form::label('enable_googleplus',__('Enable Google'),array('class'=>'form-control-label mb-1')) }}
                <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" name="enable_googleplus" id="enable_googleplus" {{($socialblog->enable_googleplus == 'on')?'checked':''}}>
                    <label class="custom-control-label form-control-label" for="enable_googleplus"></label>
                </div>
            </div>
            <div class="col-6 mb-2">
                {{Form::label('enable_email',__('Enable Email'),array('class'=>'form-control-label mb-1')) }}
                <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" name="enable_email" id="enable_email" {{($socialblog->enable_email == 'on')?'checked':''}}>
                    <label class="custom-control-label form-control-label" for="enable_email"></label>
                </div>
            </div>
            <div class="col-6 mb-2">
                {{Form::label('enable_twitter',__('Enable Twitter'),array('class'=>'form-control-label mb-1')) }}
                <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" name="enable_twitter" id="enable_twitter" {{($socialblog->enable_twitter == 'on')?'checked':''}}>
                    <label class="custom-control-label form-control-label" for="enable_twitter"></label>
                </div>
            </div>
            <div class="col-6 mb-2">
                {{Form::label('enable_pinterest',__('Enable Pinterest'),array('class'=>'form-control-label mb-1')) }}
                <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" name="enable_pinterest" id="enable_pinterest" {{($socialblog->enable_pinterest == 'on')?'checked':''}}>
                    <label class="custom-control-label form-control-label" for="enable_pinterest"></label>
                </div>
            </div>
            <div class="col-6 mb-2">
                {{Form::label('enable_stumbleupon',__('Enable Stumbleupon'),array('class'=>'form-control-label mb-1')) }}
                <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" name="enable_stumbleupon" id="enable_stumbleupon" {{($socialblog->enable_stumbleupon == 'on')?'checked':''}}>
                    <label class="custom-control-label form-control-label" for="enable_stumbleupon"></label>
                </div>
            </div>
        </div>
    </div>
    <div class="w-100 text-right">
        {{Form::submit(__('Save'),array('class'=>'btn btn-sm btn-primary rounded-pill mr-auto'))}}
    </div>
</div>
{{Form::close()}}
