<?php

namespace App\Infrastructure\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'user_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function likes(): HasMany
    {
        return $this->hasMany(Like::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class)
            ->whereNull('parent_comment_id')
            ->with(['user', 'replies.user', 'replies.replies.user'])
            ->orderBy('created_at', 'desc');
    }

    /**
     * Convert to domain entity
     */
    public function toDomainEntity(?int $currentUserId = null): \App\Domain\Entities\Post
    {
        $likesCount = $this->likes()->count();
        $isLikedByCurrentUser = $currentUserId ? $this->likes()->where('user_id', $currentUserId)->exists() : false;

        $comments = $this->comments->map(fn (Comment $comment) => [
            'id' => $comment->id,
            'content' => $comment->content,
            'author_name' => $comment->user->name,
            'user_id' => $comment->user_id,
            'created_at' => $comment->created_at->format('M j, Y g:i A'),
            'can_delete' => $currentUserId === $comment->user_id,
            'replies' => $comment->replies->map(fn (Comment $reply) => [
                'id' => $reply->id,
                'content' => $reply->content,
                'author_name' => $reply->user->name,
                'user_id' => $reply->user_id,
                'created_at' => $reply->created_at->format('M j, Y g:i A'),
                'can_delete' => $currentUserId === $reply->user_id,
            ])->toArray(),
        ])->toArray();

        return \App\Domain\Entities\Post::create(
            id: $this->id,
            title: $this->title,
            content: $this->content,
            userId: $this->user_id,
            authorName: $this->user->name,
            createdAt: $this->created_at->toDateTime(),
            updatedAt: $this->updated_at->toDateTime(),
            likesCount: $likesCount,
            isLikedByCurrentUser: $isLikedByCurrentUser,
            comments: $comments
        );
    }
}
