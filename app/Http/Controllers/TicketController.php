<?php

namespace App\Http\Controllers;

use App\Ticket;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Notifications\CommentEmailNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Str;
use App\Notifications\CreateNormalUserNotification;
use App\Notifications\TicketConfirmationNotification;
use App\Role;

class TicketController extends Controller
{
    use MediaUploadingTrait;

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $role_id = Role::where('title', 'User role')->value('id');

        return view('tickets.create', compact('role_id'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'title'         => 'required',
            'content'       => 'required',
            'author_name'   => 'required',
            'author_email'  => 'required|email',
            'editorial_requests' => 'required',
            'review_deadline' => 'required',
            'roles' => 'required',
            'attachments' => 'required',
            'version_control' => 'required',
        ]);

        $queue_number = $this->getTheQueueNumber() ?? 1;

        $request->request->add([
            'category_id'   => 1,
            'status_id'     => 1,
            'priority_id'   => 1,
            'version_control' => $request->version_control,
            'queue_number' => $queue_number,
        ]);


        $ticket = Ticket::create($request->all());

        $user = $this->createNormalUser($request);

        #send ticket created email
        $user->notify(new TicketConfirmationNotification($user, $ticket));

        foreach ($request->input('attachments', []) as $file) {
            $ticket->addMedia(storage_path('tmp/uploads/' . $file))->toMediaCollection('attachments');
        }

        return redirect()->back()->withStatus('Your ticket has been submitted. You are in position: ' . $ticket->queue_number . '  in the queue. We will be in touch on mail. You can view ticket status <a href="' . route('admin.tickets.show', $ticket->id) . '">here</a>');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Ticket  $ticket
     * @return \Illuminate\Http\Response
     */
    public function show(Ticket $ticket)
    {
        $ticket->load('comments');

        return view('tickets.show', compact('ticket'));
    }

    public function storeComment(Request $request, Ticket $ticket)
    {
        $request->validate([
            'comment_text' => 'required'
        ]);

        $comment = $ticket->comments()->create([
            'author_name'   => $ticket->author_name,
            'author_email'  => $ticket->author_email,
            'comment_text'  => $request->comment_text
        ]);

        $ticket->sendCommentNotification($comment);

        return redirect()->back()->withStatus('Your comment added successfully');
    }

    public function createNormalUser($request)
    {
        #check if user exists
        $user = User::where('email', '=', $request->author_email)->first();

        if ($user) {
        } else {
            #Save user
            $user = new User();
            $user->name = $request->author_name;
            $user->email = $request->author_email;
            $user->password = str_replace("-", "", Str::uuid()->toString());
            $user->remember_token = str_replace("-", "", Str::uuid()->toString());
            $user->save();

            $user->roles()->sync($request->roles);

            #send user activation email
            $user->notify(new CreateNormalUserNotification($user));
        }

        return $user;
    }


    public function getTheQueueNumber()
    {
        $data = Ticket::orderBy('id', 'desc')->first();
        if ($data) {
            return $data->queue_number + 1;
        }
        return 1;
    }
}
