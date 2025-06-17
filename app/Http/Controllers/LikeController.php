<?php

namespace App\Http\Controllers;

use App\Application\Services\LikeService;
use App\Http\Requests\ToggleLikeRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    public function __construct(
        private readonly LikeService $likeService
    ) {}

    /**
     * Toggle like on a post
     */
    public function toggle(ToggleLikeRequest $request, int $postId): RedirectResponse
    {
        $isLiked = $this->likeService->toggleLike($postId, Auth::id());

        $message = $isLiked ? 'Post liked!' : 'Post unliked!';

        return back()->with('success', $message);
    }
}
