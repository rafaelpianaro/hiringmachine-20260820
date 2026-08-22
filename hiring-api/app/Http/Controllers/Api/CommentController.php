<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Recipe;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    use ApiResponser;

    /**
     * Display comments for a recipe.
     */
    public function index(Request $request, Recipe $recipe)
    {
        $comments = $recipe->comments()
            ->with('user:id,name')
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 15));

        return $this->successResponse($comments);
    }

    /**
     * Store a new comment.
     */
    public function store(Request $request, Recipe $recipe)
    {
        $request->validate([
            'content' => 'required|string',
            'rating' => 'nullable|integer|min:1|max:5',
        ]);

        $comment = Auth::user()->comments()->create([
            'recipe_id' => $recipe->id,
            'content' => $request->content,
            'rating' => $request->rating,
        ]);

        return $this->successResponse(
            $comment->load('user:id,name'),
            'Comment added successfully',
            201
        );
    }

    /**
     * Update the specified comment.
     */
    public function update(Request $request, Comment $comment)
    {
        // Check if user owns the comment
        if ($comment->user_id !== Auth::id()) {
            return $this->forbiddenResponse('You do not have permission to edit this comment.');
        }

        $request->validate([
            'content' => 'sometimes|string',
            'rating' => 'nullable|integer|min:1|max:5',
        ]);

        $comment->update($request->only(['content', 'rating']));

        return $this->successResponse(
            $comment->fresh()->load('user:id,name'),
            'Comment updated successfully'
        );
    }

    /**
     * Remove the specified comment.
     */
    public function destroy(Comment $comment)
    {
        // Check if user owns the comment or is admin
        if ($comment->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            return $this->forbiddenResponse('You do not have permission to delete this comment.');
        }

        $comment->delete();

        return $this->successResponse(null, 'Comment deleted successfully');
    }
}
