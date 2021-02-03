@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            @if(session('status'))
                <div class="alert alert-success" role="alert">
                    {!! session('status') !!}
                </div>
            @endif
            <div class="card">
                <div class="card-header">Raise a ticket</div>

                <div class="card-body ">
                    <form method="POST" action="{{ route('tickets.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="form-group row">
                            <label for="author_name" class="col-md-2 col-form-label text-md-right"></label>

                            <div class="col-md-8">
                                <input id="author_name" placeholder="Your Name" type="text" class="form-control @error('author_name') is-invalid @enderror" name="author_name" value="{{ old('author_name') }}" required autocomplete="name" autofocus>

                                @error('author_name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="author_email" class="col-md-2 col-form-label text-md-right"></label>

                            <div class="col-md-8">
                                <input id="author_email" type="email" placeholder="Your Email" class="form-control @error('author_email') is-invalid @enderror" name="author_email" value="{{ old('author_email') }}" required autocomplete="email">

                                @error('author_email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="review_deadline" class="col-md-2 col-form-label text-md-right"></label>

                            <div class="col-md-8">
                                <input id="review_deadline" type="text" placeholder="@lang('cruds.ticket.fields.review_deadline')" class="form-control @error('review_deadline') is-invalid @enderror" name="review_deadline" value="{{ old('review_deadline') }}" required autocomplete="review_deadline">

                                @error('review_deadline')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <input type="hidden" value="{{ $role_id }}" name="roles[]">

                        <div class="form-group row">
                            <label for="title" class="col-md-2 col-form-label text-md-right"></label>

                            <div class="col-md-8">
                                <input id="title" type="text" placeholder="@lang('cruds.ticket.fields.title')" class="form-control @error('title') is-invalid @enderror" name="title" value="{{ old('title') }}" required autocomplete="title">

                                @error('title')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="content" class="col-md-2 col-form-label text-md-right"></label>

                            <div class="col-md-8">
                                <textarea placeholder="@lang('cruds.ticket.fields.content')" class="form-control @error('content') is-invalid @enderror" id="content" name="content" rows="3" required>{{ old('content') }}</textarea>
                                @error('content')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="editorial_requests" class="col-md-2 col-form-label text-md-right"></label>

                            <div class="col-md-8">
                                <textarea placeholder="@lang('cruds.ticket.fields.editorial_requests')" class="form-control @error('editorial_requests') is-invalid @enderror" id="editorial_requests" name="editorial_requests" rows="3" required>{{ old('editorial_requests') }}</textarea>
                                @error('editorial_requests')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="attachments" class="col-md-2 col-form-label text-md-right"></label>

                            <div class="col-md-8">
                                <div class="needsclick dropzone @error('attachments') is-invalid @enderror" id="attachments-dropzone">

                                </div>
                            </div>
                            @error('attachments')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-8 offset-md-2">
                                <button type="submit" class="btn btn-primary">
                                    @lang('global.submit')
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
      $('form').append('<input type="hidden" name="attachments[]" value="' + response.name + '">')
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
              $('form').append('<input type="hidden" name="attachments[]" value="' + file.file_name + '">')
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
