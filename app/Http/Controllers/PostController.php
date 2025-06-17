<?php

namespace App\Http\Controllers;

use App\Application\DTOs\CreatePostDTO;
use App\Application\DTOs\UpdatePostDTO;
use App\Application\Services\PostService;
use App\Http\Requests\CreatePostRequest;
use App\Http\Requests\UpdatePostRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class PostController extends Controller
{
    public function __construct(
        private readonly PostService $postService
    ) {}

    /**
     * Display social feed with all posts
     */
    public function index(): Response
    {
        $posts = $this->postService->getAllPosts(Auth::id());

        return Inertia::render('Posts/Index', [
            'posts' => array_map(function ($post) {
                return [
                    'id' => $post->id,
                    'title' => $post->title,
                    'content' => $post->content,
                    'author_name' => $post->authorName,
                    'user_id' => $post->userId,
                    'created_at' => $post->createdAt->format('M j, Y g:i A'),
                    'updated_at' => $post->updatedAt->format('M j, Y g:i A'),
                    'likes_count' => $post->likesCount,
                    'is_liked' => $post->isLikedByCurrentUser,
                    'comments' => $post->comments,
                    'can_edit' => Auth::id() === $post->userId,
                ];
            }, $posts),
        ]);
    }

    /**
     * Store a new post
     */
    public function store(CreatePostRequest $request): RedirectResponse
    {
        $dto = CreatePostDTO::fromArray(
            $request->validated(),
            Auth::id()
        );

        $this->postService->createPost($dto);

        return redirect()->route('posts.index')
            ->with('success', 'Post created successfully!');
    }

    /**
     * Update an existing post
     */
    public function update(UpdatePostRequest $request, int $id): RedirectResponse
    {
        $dto = UpdatePostDTO::fromArray(
            $request->validated(),
            $id,
            Auth::id()
        );

        $post = $this->postService->updatePost($dto);

        if (!$post) {
            return redirect()->route('posts.index')
                ->with('error', 'Post not found or you do not have permission to edit it.');
        }

        return redirect()->route('posts.index')
            ->with('success', 'Post updated successfully!');
    }

    /**
     * Delete a post
     */
    public function destroy(int $id): RedirectResponse
    {
        $deleted = $this->postService->deletePost($id, Auth::id());

        if (!$deleted) {
            return redirect()->route('posts.index')
                ->with('error', 'Post not found or you do not have permission to delete it.');
        }

        return redirect()->route('posts.index')
            ->with('success', 'Post deleted successfully!');
    }
}
