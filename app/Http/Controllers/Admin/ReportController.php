<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Report;
use App\Models\Event;
use App\Models\Comment;
use App\Models\User;

class ReportController extends Controller
{
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Pending,Accepted,Rejected',
        ]);
    
        // Load report with relationships
        $report = Report::with(['event', 'reportedUser'])->findOrFail($id);
    
        // Update Report Status
        $report->status = $request->status;
        $report->save();
    
        // If the report is accepted -> take action
        if ($request->status === 'Accepted') {
    
            // If report links to an event -> delete that event
            if ($report->event && $report->event->count() > 0) {
                foreach ($report->event as $event) {
    
                    // Delete event
                    $event->delete();
    
                    
                    $report->event()->detach($event->eventid);
                }
            }
    
            // If report links to a user -> ban that user
            if ($report->reportedUser && $report->reportedUser->count() > 0) {
    
                foreach ($report->reportedUser as $usr) {
                    $usr->isbanned = true;
                    $usr->save();
                }
            }

            if ($report->comment && $report->comment->count() > 0) {
                foreach ($report->comment as $comment) {
    
                    // Delete comment
                    $comment->delete();
    
                    
                    $report->comment()->detach($comment->commentid);
                }
            }
        }

        return redirect()->back()->with('success', 'Report status updated!');
    }

    public function pending()
{
    $pendingReports = Report::where('status', 'Pending')
        ->with(['reporter', 'event', 'comment', 'reportedUser'])
        ->orderBy('reporttime', 'desc')
        ->get();

    return view('admin.reports', compact('pendingReports'));
}

public function index()
{
    
    $reports = \App\Models\Report::with(['reporter', 'event', 'comment', 'reportedUser'])
        ->orderBy('reporttime', 'desc')
        ->paginate(10);

    return view('admin.reports', compact('reports'));
}

}
