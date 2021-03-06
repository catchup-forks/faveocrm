<?php
namespace App\Http\ViewComposers;

use App\Model\helpdesk\Staff\Department;
use App\Model\helpdesk\Mailboxes\Mailboxes;
use App\Model\helpdesk\Settings\Company;
use App\Model\helpdesk\Ticket\Tickets;
use App\Staff;
use Auth;
use Illuminate\View\View;

class StaffLayout
{
    /**
     * The user repository implementation.
     *
     * @var UserRepository
     */
    protected $company;
    protected $users;
    protected $tickets;
    protected $department;
    protected $mailboxes;

    /**
     * Create a new profile composer.
     *
     * @param
     *
     * @return void
     */
    public function __construct(Company $company, Staff $users, Tickets $tickets, Department $department, Mailboxes $mailboxes)
    {
        $this->company = $company;
        $this->auth = Auth::user();
        $this->users = $users;
        $this->tickets = $tickets;
        $this->department = $department;
        $this->emails = $mailboxes;
    }

    /**
     * Bind data to the view.
     *
     * @param View $view
     *
     * @return void
     */
    public function compose(View $view)
    {
        $notifications = \App\Http\Controllers\Common\NotificationController::getNotifications();
        $view->with([
            'company' => $this->company,
            'notifications' => $notifications,
            'myticket' => $this->myTicket(),
            'unassigned' => $this->unassigned(),
            'followup_ticket' => $this->followupTicket(),
            'deleted' => $this->deleted(),
            'tickets' => $this->inbox(),
            'department' => $this->departments(),
            'overdues' => $this->overdues(),
            'due_today' => $this->getDueToday(),
            'is_mail_conigured' => $this->getEmailConfig(),
            'ticket_policy' => new \App\Policies\TicketPolicy(),
        ]);
    }

    public function users()
    {
        return $this->users->select('id', 'profile_pic');
    }

    public function tickets()
    {
        return $this->tickets->select('id', 'ticket_number');
    }

    public function departments()
    {
        $array = [];
        $tickets = $this->tickets;
        if (\Auth::user()->role == 'staff') {
            $tickets = $tickets->where('tickets.dept_id', '=', \Auth::user()->primary_dpt);
        }
        $tickets = $tickets
            ->leftJoin('department as dep', 'tickets.dept_id', '=', 'dep.id')
            ->leftJoin('tickets__statuses', 'tickets.status', '=', 'tickets__statuses.id')
            ->select('dep.name as name', 'tickets__statuses.name as status', \DB::raw('COUNT(tickets__statuses.name) as count'))
            ->groupBy('dep.name', 'tickets__statuses.name')
            ->get();
        $grouped = $tickets->groupBy('name');
        $status = [];
        foreach ($grouped as $key => $group) {
            $status[$key] = $group->keyBy('status');
        }
        return collect($status);
    }

    public function myTicket()
    {
        $ticket = $this->tickets();
        if ($this->auth->role == 'admin') {
            return $ticket->where('assigned_to', $this->auth->id)
                ->where('status', '1');
        } elseif ($this->auth->role == 'staff') {
            return $ticket->where('assigned_to', $this->auth->id)
                ->where('status', '1');
        }
    }

    public function unassigned()
    {
        $ticket = $this->tickets();
        if ($this->auth->role == 'admin') {
            return $ticket->where('assigned_to', '=', null)
                ->where('status', '=', '1')
                ->select('id');
        } elseif ($this->auth->role == 'staff') {
            return $ticket->where('assigned_to', '=', null)
                ->where('status', '=', '1')
                ->where('dept_id', '=', $this->auth->primary_dpt)
                ->select('id');
        }
    }

    public function followupTicket()
    {
        $ticket = $this->tickets();
        if ($this->auth->role == 'admin') {
            return $ticket->where('status', '1')->where('follow_up', '1')->select('id');
        } elseif ($this->auth->role == 'staff') {
            return $ticket->where('status', '1')->where('follow_up', '1')->select('id');
        }
    }

    public function deleted()
    {
        $ticket = $this->tickets();
        if ($this->auth->role == 'admin') {
            return $ticket->where('status', '5')->select('id');
        } elseif ($this->auth->role == 'staff') {
            return $ticket->where('status', '5')->where('dept_id', '=', $this->auth->primary_dpt)
                ->select('id');
        }
    }

    public function inbox()
    {
        $table = $this->tickets();
        if (Auth::user()->role == 'staff') {
            $id = Auth::user()->primary_dpt;
            $table = $table->where('tickets.dept_id', '=', $id)->orWhere('assigned_to', '=', Auth::user()->id);
        }
        return $table->Join('tickets__statuses', function ($join) {
            $join->on('tickets__statuses.id', '=', 'tickets.status')
                ->whereIn('tickets__statuses.id', [1, 7]);
        });
    }

    public function overdues()
    {
        $ticket = $this->tickets();
        if ($this->auth->role == 'admin') {
            return $ticket->where('status', '=', 1)
                ->where('isanswered', '=', 0)
                ->whereNotNull('tickets.duedate')
                ->where('tickets.duedate', '!=', '00-00-00 00:00:00')
                ->where('tickets.duedate', '<', \Carbon\Carbon::now())
                ->select('tickets.id');
        } elseif ($this->auth->role == 'staff') {
            return $ticket->where('status', '=', 1)
                ->where('isanswered', '=', 0)
                ->whereNotNull('tickets.duedate')
                ->where('dept_id', '=', $this->auth->primary_dpt)
                ->where('tickets.duedate', '!=', '00-00-00 00:00:00')
                ->where('tickets.duedate', '<', \Carbon\Carbon::now())
                ->select('tickets.id');
        }
    }

    public function getDueToday()
    {
        $ticket = $this->tickets();
        if ($this->auth->role == 'admin') {
            return $ticket->where('status', '=', 1)
                ->where('status', '=', 1)
                ->where('isanswered', '=', 0)
                ->whereNotNull('duedate')
                ->whereRaw('date(duedate) = ?', [date('Y-m-d')]);
        } elseif ($this->auth->role == 'staff') {
            return $ticket->where('status', '=', 1)
                ->where('status', '=', 1)
                ->where('isanswered', '=', 0)
                ->whereNotNull('duedate')
                ->where('dept_id', '=', $this->auth->primary_dpt)
                ->whereRaw('date(duedate) = ?', [date('Y-m-d')]);
        }
    }

    /**
     * @category function to check configured mails
     *
     * @param null
     *
     * @var $mailboxes
     *
     * @return bool true/false
     */
    public function getEmailConfig()
    {
        $mailboxes = $this->emails->where('sending_status', '=', 1)->where('fetching_status', '=', 1)->count();
        if ($mailboxes >= 1) {
            return true;
        }
        return false;
    }
}
