<?php

namespace App\Http\Controllers\Admin;

use Gate;
use Symfony\Component\HttpFoundation\Response;
use App\Ticket;
use App\Role, Illuminate\Support\Facades\Auth;

class HomeController
{
    public function index()
    {
        abort_if(Gate::denies('dashboard_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $normal_user_role_id = Role::where('title', 'User role')->value('id');

        $user_role_id = Auth::user()->roles->pluck('id')->first();

        if ($user_role_id == $normal_user_role_id) {

            $totalTickets = Ticket::where('author_email', Auth::user()->email)->count();
            $openTickets = Ticket::where('author_email', Auth::user()->email)->whereHas('status', function ($query) {
                $query->whereName('Open');
            })->count();
            $closedTickets = Ticket::where('author_email', Auth::user()->email)->whereHas('status', function ($query) {
                $query->whereName('Closed');
            })->count();
        } else {

            $totalTickets = Ticket::count();
            $openTickets = Ticket::whereHas('status', function ($query) {
                $query->whereName('Open');
            })->count();
            $closedTickets = Ticket::whereHas('status', function ($query) {
                $query->whereName('Closed');
            })->count();
        }

        return view('home', compact('totalTickets', 'openTickets', 'closedTickets'));
    }
}
