<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;
use App\Models\Event;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;
use App\Events\NotificationCreated;

class CommentController extends Controller
{
    public function index($eventId){
        $event = Event::with(['comments' => function ($query) {
         $query->withCount('voters')
               ->orderByDesc('voters')
               ->whereNull('parentid');
        }])->findOrFail($eventId);
        $this->authorize('comment',$event);
        $comments = $event->comments()->with('author', 'replies.author')->get();
         return response()->json($event->comments);
    }

    public function store(Request $request, $eventId){
        $request->validate([
            'content' => 'required|string|max:1000',
            'parentid' => 'nullable|integer|exists:comment,commentid',
        ]);

            $event = Event::findOrFail($eventId);
            $authorId = Auth::user()->userid;

        $comment = Comment::create([
            'content' => $request -> input('content'),
            'authorid' => Auth::id(),
            'eventid' => $eventId,
            'parentid' => $request -> parentid
        ]);

        $recipientIds = $event->attendees()
            ->get()
            ->pluck('userid')
            ->push($event->createdby)
            ->unique()
            ->reject(fn($id) => (int)$id === (int)$authorId);


        foreach ($recipientIds as $uid) {
            $n = Notification::where('userid', $uid)
                ->where('notificationtype', 'NewComment')
                ->orderByDesc('notificationid')
                ->first();

        if ($n) {
            event(new NotificationCreated($n->toArray()));
        }
    }        
        return redirect()->back();
    }

    public function editComment(Request $request, Comment $comment)
    {
        $this->authorize('update', $comment); 
    
        $request->validate([
            'content' => 'required|string|max:1000',
        ]);
    
        $comment->update([
            'content' => $request->input('content'),
        ]);
    
        return redirect()->back();
    }
    
    public function deleteComment(Comment $comment)
     {
                 $this->authorize('delete', $comment); 
    
                 $comment->delete();
    
                return redirect()->back();
    }

    public function vote(Comment $comment)
    {
        $user = Auth::user();

        if($comment->voters()->where('vote.userid',$user->userid)->exists()) {
           $comment->voters()->detach($user->userid);
        }else{
            $comment->voters()->attach($user->userid);
        }
        return redirect()->back();
    }
}
