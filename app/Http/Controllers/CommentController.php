<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;
use App\Models\Event;
use Illuminate\Support\Facades\Auth;
class CommentController extends Controller
{
    public function index($eventId){
        $event = Event::findOrFail($eventId);
        $this->authorize('comment',$event);
        $comments = $event->comments()->with('author', 'replies.author')->get();
        return response()->json($event->comments);
    }

    public function store(Request $request, $eventId){
        $request->validate([
            'content' => 'required|string|max:1000',
            'parentid' => 'nullable|integer|exists:comment,commentid',
        ]);

        $comment = Comment::create([
            'content' => $request -> input('content'),
            'authorid' => Auth::id(),
            'eventid' => $eventId,
            'parentid' => $request -> parentid
        ]);
        return redirect()->back();
    }

    public function showComments($eventId){
        $event = Event::with(['comments' => function($query){
            $query->whereNull('parentid')->with('replies.author');
        }])->find($eventId);
        return view('events.show', compact('event'));
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
