<?php

namespace App\Http\Controllers;

use App\Application\DTOs\CreateCommentDTO;
use App\Application\Services\CommentService;
use App\Http\Requests\CreateCommentRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function __construct(
        private readonly CommentService $commentService
    ) {}

    /**
     * Add a comment to a post
     */
    public function store(CreateCommentRequest $request, int $postId): RedirectResponse
    {
        $validatedData = $request->validated();
        $dto = CreateCommentDTO::fromArray(
            $validatedData,
            $postId,
            Auth::id(),
            $validatedData['parent_comment_id'] ?? null
        );

        $this->commentService->addComment($dto);

        return redirect()->route('posts.index')
            ->with('success', 'Comment added successfully!');
    }

    /**
     * Delete a comment
     */
    public function destroy(int $commentId): RedirectResponse
    {
        $deleted = $this->commentService->deleteComment($commentId, Auth::id());

        if (!$deleted) {
            return redirect()->route('posts.index')
                ->with('error', 'Comment not found or you do not have permission to delete it.');
        }

        return redirect()->route('posts.index')
            ->with('success', 'Comment deleted successfully!');
    }
}
