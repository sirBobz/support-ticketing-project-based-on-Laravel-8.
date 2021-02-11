@extends('layouts.admin')
@section('content')
<div class="card">
   <div class="card-header">
      {{ trans('global.show') }} {{ trans('cruds.ticket.title') }}
   </div>
   <div class="card-body">
      @if(session('status'))
      <div class="alert alert-success" role="alert">
         {{ session('status') }}
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
      <div class="mb-2">
         <table class="table table-bordered table-striped">
            <tbody>
               <tr>
                  <th>
                     Internal queue number
                  </th>
                  <td>
                     @if($ticket->queue_number == 0)
                       Processed
                     @else
                      {{ $ticket->queue_number }}
                     @endif
                  </td>
               </tr>
               <tr>
                <th>
                   Internal Review Deadline
                </th>
                <td>
                   {{ $ticket->review_deadline }}
                </td>
             </tr>
               <tr>
                  <th>
                     Version control
                  </th>
                  <td>
                     @foreach( json_decode($ticket->version_control) as $value)
                            {{ $value }} <br>
                     @endforeach
                  </td>
               </tr>
               <tr>
                  <th>
                     {{ trans('cruds.ticket.fields.created_at') }}
                  </th>
                  <td>
                     {{ $ticket->created_at }}
                  </td>
               </tr>
               <tr>
                  <th>
                     {{ trans('cruds.ticket.fields.title') }}
                  </th>
                  <td>
                     {{ $ticket->title }}
                  </td>
               </tr>
               <tr>
                  <th>
                     {{ trans('cruds.ticket.fields.editorial_requests') }}
                  </th>
                  <td>
                     {!! $ticket->editorial_requests !!}
                  </td>
               </tr>
               <tr>
                  <th>
                     {{ trans('cruds.ticket.fields.content') }}
                  </th>
                  <td>
                     {!! $ticket->content !!}
                  </td>
               </tr>
               <tr>
                  <th>
                     {{ trans('cruds.ticket.fields.attachments') }}
                  </th>
                  <td>
                     @foreach($ticket->attachments as $attachment)
                     <a href="{{ $attachment->getUrl() }}" target="_blank">{{ $attachment->file_name }}</a>
                     @endforeach
                  </td>
               </tr>
               <tr>
                  <th>
                     {{ trans('cruds.ticket.fields.status') }}
                  </th>
                  <td>
                     {{ $ticket->status->name ?? '' }}
                  </td>
               </tr>
               <tr>
                  <th>
                     {{ trans('cruds.ticket.fields.priority') }}
                  </th>
                  <td>
                     {{ $ticket->priority->name ?? '' }}
                  </td>
               </tr>
               <tr>
                  <th>
                     {{ trans('cruds.ticket.fields.category') }}
                  </th>
                  <td>
                     {{ $ticket->category->name ?? '' }}
                  </td>
               </tr>
               <tr>
                  <th>
                     {{ trans('cruds.ticket.fields.author_name') }}
                  </th>
                  <td>
                     {{ $ticket->author_name }}
                  </td>
               </tr>
               <tr>
                  <th>
                     {{ trans('cruds.ticket.fields.author_email') }}
                  </th>
                  <td>
                     {{ $ticket->author_email }}
                  </td>
               </tr>
               <tr>
                  <th>
                     {{ trans('cruds.ticket.fields.assigned_to_user') }}
                  </th>
                  <td>
                     {{ $ticket->assigned_to_user->name ?? '' }}
                  </td>
               </tr>
               <tr>
                  <th>
                     {{ trans('cruds.ticket.fields.comments') }}
                  </th>
                  <td>
                     @forelse ($ticket->comments as $comment)
                     <div class="row">
                        <div class="col">
                           <p class="font-weight-bold"><a href="mailto:{{ $comment->author_email }}">{{ $comment->author_name }}</a> ({{ $comment->created_at }})</p>
                           <p>{{ $comment->comment_text }}</p>
                           <p>
                           @foreach((array) json_decode($comment->files) as $value)
                              <a href="{{ url('/comments/' .  $value) }}">{{ substr($value, strpos($value, "_") + 1) }}</a><br>
                           @endforeach
                        </p>

                        </div>
                     </div>
                     <hr />
                     @empty
                     <div class="row">
                        <div class="col">
                           <p>There are no comments.</p>
                        </div>
                     </div>
                     <hr />
                     @endforelse
                     <form action="{{ route('admin.tickets.storeComment', $ticket->id) }}" enctype="multipart/form-data" method="POST">
                        @csrf
                        <div class="form-group">
                           <label for="comment_text">Leave a comment</label>
                           <textarea class="form-control" id="comment_text" name="comment_text" rows="3" required></textarea>
                        </div>
                        <div class="custom-file">
                            <input type="file" name="files[]" multiple class="custom-file-input" id="customFile">
                            <label class="custom-file-label" for="customFile">Choose file</label>
                        </div>
                        <br> <br>
                        <button type="submit" class="btn btn-primary">@lang('global.submit')</button>
                     </form>
                  </td>
               </tr>
            </tbody>
         </table>
      </div>
      <a class="btn btn-default my-2" href="{{ route('admin.tickets.index') }}">
      {{ trans('global.back_to_list') }}
      </a>
      <a href="{{ route('admin.tickets.edit', $ticket->id) }}" class="btn btn-primary">
      @lang('global.edit') @lang('cruds.ticket.title_singular')
      </a>
      <nav class="mb-3">
         <div class="nav nav-tabs">
         </div>
      </nav>
   </div>
</div>
@endsection
@section('scripts')
<script>
    // Add the following code if you want the name of the file appear on select
    $(".custom-file-input").on("change", function() {
      var fileName = $(this).val().split("\\").pop();
      $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
    });
</script>
@endsection
