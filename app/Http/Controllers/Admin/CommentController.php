<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /* ── Index ── */

    public function index(Request $request)
    {
        $query = Comment::with('post')->latest();

        if ($status = $request->input('status')) {
            $query->when($status === 'pending',  fn ($q) => $q->pending());
            $query->when($status === 'approved', fn ($q) => $q->approved());
        }

        $comments = $query->paginate(20)->withQueryString();

        return view('admin.comments.index', compact('comments'));
    }

    /* ── Toggle approval ── */

    public function approve(Comment $comment)
    {
        $comment->approved = !$comment->approved;
        $comment->save();

        $label = $comment->approved ? 'approuvé' : 'désapprouvé';

        return back()->with('success', "Commentaire {$label}.");
    }

    /* ── Destroy ── */

    public function destroy(Comment $comment)
    {
        $comment->delete();

        return back()->with('success', 'Commentaire supprimé.');
    }
}
