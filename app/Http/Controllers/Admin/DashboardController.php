<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Statistics
        $totalUsers = DB::table('user')->count();
        $totalEvents = DB::table('event')->count();
        $eventsByStatus = DB::table('event')
            ->select('eventstatus', DB::raw('count(*) as total'))
            ->groupBy('eventstatus')
            ->get();

        $pendingReports = DB::table('report')->where('status', 'Pending')->count();
        $pendingJoinRequests = DB::table('joinrequest')->where('j_status', 'Pending')->count();

        // Last events
        $latestEvents = DB::table('event')
            ->join('user', 'event.createdby', '=', 'user.userid')
            ->select(
                'event.eventid',
                'event.eventname',
                'event.eventstatus',
                'user.username as creator',
                'event.startdate',
                'event.enddate'
            )
            ->orderBy('event.startdate', 'desc')
            ->limit(5)
            ->get();

        // Last reports
        $latestReports = DB::table('report')
            ->leftJoin('eventreport', 'report.reportid', '=', 'eventreport.reportid')
            ->leftJoin('commentreport', 'report.reportid', '=', 'commentreport.reportid')
            ->leftJoin('userreport', 'report.reportid', '=', 'userreport.reportid')
            ->leftJoin('user', 'report.userid', '=', 'user.userid')
            ->select(
                'report.reportid',
                'report.reporttype',
                'report.status',
                'report.description',
                'user.username as reporter',
                'eventreport.eventid',
                'commentreport.commentid',
                'userreport.userid as reporteduserid'
            )
            ->orderBy('report.reporttime', 'desc')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalEvents',
            'eventsByStatus',
            'pendingReports',
            'pendingJoinRequests',
            'latestEvents',
            'latestReports'
        ));
    }

    public function browseEvents(Request $request)
{
    $query = DB::table('event')
        ->join('user', 'event.createdby', '=', 'user.userid')
        ->select(
            'event.eventid',
            'event.eventname',
            'event.eventstatus',
            'event.description',
            'user.username as creator',
            'event.startdate',
            'event.enddate'
        )
        ->orderBy('event.startdate', 'desc');

    // Search filter
    if ($request->has('search') && $request->search != '') {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('event.eventname', 'ilike', "%{$search}%")
              ->orWhere('event.description', 'ilike', "%{$search}%");
        });
    }

    $events = $query->paginate(12)->appends($request->all());

    return view('events.browse', compact('events'));
}

}
