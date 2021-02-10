@extends('layouts.app')
@section('styles')
<style>input[type=email]{width:100%;padding:12px 20px;margin:8px 0;box-sizing:border-box;border:1px solid grey;border-radius:4px}::-webkit-input-placeholder{color:#ffdab9;font-size:12px}::-moz-placeholder{color:#ffdab9;font-size:12px}:-ms-input-placeholder{color:#ffdab9;font-size:12px}::placeholder{color:#ffdab9;font-size:12px}</style>
@endsection
@section('content')

<div class="container">
  <div class="row justify-content-center">
    <div class="col-md-12">
      @if(session('status'))
      <div class="alert alert-success" role="alert">
        {!! session('status') !!}
      </div>
      @endif
      @if ($errors->any())
      <div class="alert alert-danger">
        <ul>
          @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
      @endif
      <div class="card">
        <div class="card-header">Raise a ticket</div>
        <div class="card-body ">
          <form method="POST" action="{{ route('tickets.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="row">
              <div class="form-group col-md-6">
                <input id="author_name" placeholder="Your Name" type="text" class="form-control @error('author_name') is-invalid @enderror" name="author_name" value="{{ old('author_name') }}" required autocomplete="name" autofocus>
                @error('author_name')
                <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
                </span>
                @enderror
              </div>
              <div class="form-group col-md-6">
                <input id="author_email" type="email" placeholder="Your Valid Email" title="This email will be used to set an account." class="form-control @error('author_email') is-invalid @enderror" name="author_email" value="{{ old('author_email') }}" required autocomplete="email">
                @error('author_email')
                <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
                </span>
                @enderror
              </div>
            </div>
            <div class="row">
              <div class="form-group col-md-6">
                <input id="review_deadline" type="text" placeholder="@lang('cruds.ticket.fields.review_deadline')" class="form-control @error('review_deadline') is-invalid @enderror" name="review_deadline" value="{{ old('review_deadline') }}" required autocomplete="review_deadline">
                @error('review_deadline')
                <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
                </span>
                @enderror
              </div>
              <div class="form-group col-md-6">
                <input id="title" type="text" placeholder="@lang('cruds.ticket.fields.title')" class="form-control @error('title') is-invalid @enderror" name="title" value="{{ old('title') }}" required autocomplete="title">
                @error('title')
                <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
                </span>
                @enderror
              </div>
            </div>
            <div class="row">
                <div class="form-group col-md-6">
                    <textarea style="border: 1px solid grey;" placeholder="@lang('cruds.ticket.fields.content')" class="form-control @error('content') is-invalid @enderror" id="content" name="content" rows="3" required>{{ old('content') }}</textarea>
                    @error('content')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="form-group col-md-6">
                    <textarea style="border: 1px solid grey;" placeholder="@lang('cruds.ticket.fields.editorial_requests')" class="form-control @error('editorial_requests') is-invalid @enderror" id="editorial_requests" name="editorial_requests" rows="3" required>{{ old('editorial_requests') }}</textarea>
                    @error('editorial_requests')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
              </div>
            <input type="hidden" value="{{ $role_id }}" name="roles[]">

            <div class="row">
                <div class="form-group col-md-6">
                    <div class="needsclick dropzone @error('attachments') is-invalid @enderror" id="attachments-dropzone">
                    @error('attachments')
                        <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                    </div>
              </div>
              <div class="form-group col-md-6">
                <br> <br>
                <input type="checkbox" value="The document has completed all internal reviews (Country office and HO level)" name="version_control[]">
                <label style="font-size:12px;"> The document has completed all internal reviews (Country office and HO level) </label><br>
                <input type="checkbox" value="The document has completed all CDC and other external reviews" name="version_control[]">
                <label style="font-size:12px;">The document has completed all CDC and other external reviews</label><br>
                <input type="checkbox" value="None of the above" name="version_control[]">
                <label style="font-size:12px;"> None of the above</label>

            </div>
            </div>

            <div class="form-group row">
              <div class="col-md-12">
                <br>
                <button type="submit" class="btn btn-primary float-right">
                @lang('global.submit') ticket
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
@section('scripts')
<script>
  var uploadedAttachmentsMap = {}
  Dropzone.options.attachmentsDropzone = {
  url: '{{ route('tickets.storeMedia') }}',
  maxFilesize: 2, // MB
  addRemoveLinks: true,
  headers: {
    'X-CSRF-TOKEN': "{{ csrf_token() }}"
  },
  params: {
    size: 2
  },
  success: function (file, response) {
    $('form').append('<input type="hidden" required name="attachments[]" value="' + response.name + '">')
    uploadedAttachmentsMap[file.name] = response.name
  },
  removedfile: function (file) {
    file.previewElement.remove()
    var name = ''
    if (typeof file.file_name !== 'undefined') {
      name = file.file_name
    } else {
      name = uploadedAttachmentsMap[file.name]
    }
    $('form').find('input[name="attachments[]"][value="' + name + '"]').remove()
  },
  init: function () {
  @if(isset($ticket) && $ticket->attachments)
        var files =
          {!! json_encode($ticket->attachments) !!}
            for (var i in files) {
            var file = files[i]
            this.options.addedfile.call(this, file)
            file.previewElement.classList.add('dz-complete')
            $('form').append('<input type="hidden" required name="attachments[]" value="' + file.file_name + '">')
          }
  @endif
  },
   error: function (file, response) {
       if ($.type(response) === 'string') {
           var message = response //dropzone sends it's own error messages in string
       } else {
           var message = response.errors.file
       }
       file.previewElement.classList.add('dz-error')
       _ref = file.previewElement.querySelectorAll('[data-dz-errormessage]')
       _results = []
       for (_i = 0, _len = _ref.length; _i < _len; _i++) {
           node = _ref[_i]
           _results.push(node.textContent = message)
       }

       return _results
   }
  }
</script>
@stop

