<?php

namespace App\Infrastructure\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'content',
        'user_id',
        'post_id',
        'parent_comment_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'parent_comment_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(Comment::class, 'parent_comment_id')->with(['user', 'replies']);
    }

    /**
     * Convert to domain entity
     */
    public function toDomainEntity(): \App\Domain\Entities\Comment
    {
        $currentUserId = Auth::id();

        $replies = $this->replies->map(fn (Comment $reply) => [
            'id' => $reply->id,
            'content' => $reply->content,
            'author_name' => $reply->user->name,
            'user_id' => $reply->user_id,
            'created_at' => $reply->created_at->format('M j, Y g:i A'),
            'can_delete' => $currentUserId === $reply->user_id,
            'replies' => $reply->replies->map(fn (Comment $nestedReply) => [
                'id' => $nestedReply->id,
                'content' => $nestedReply->content,
                'author_name' => $nestedReply->user->name,
                'user_id' => $nestedReply->user_id,
                'created_at' => $nestedReply->created_at->format('M j, Y g:i A'),
                'can_delete' => $currentUserId === $nestedReply->user_id,
            ])->toArray(),
        ])->toArray();

        return \App\Domain\Entities\Comment::create(
            id: $this->id,
            content: $this->content,
            userId: $this->user_id,
            authorName: $this->user->name,
            postId: $this->post_id,
            parentCommentId: $this->parent_comment_id,
            createdAt: $this->created_at->toDateTime(),
            updatedAt: $this->updated_at->toDateTime(),
            replies: $replies
        );
    }
}
