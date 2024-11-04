<?php

namespace App\Repositories;

use App\Models\Post;
use Illuminate\Support\Facades\DB;

class PostRepository
{
    /**
     * Find an existing post by its API post ID.
     *
     * @param int $postId
     * @return Post|null
     */
    public function findByApiId(int $postId)
    {
        return Post::find($postId);
    }

    /**
     * Create a new post.
     *
     * @param array $data
     * @return Post
     */
    public function create(array $data)
    {
        return Post::create($data);
    }

    /**
     * Get posts with optional search query and pagination.
     *
     * @param string|null $query
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getPosts(?string $query, int $perPage = 5)
    {
        return Post::orderBy('created_at', 'desc')
            ->with('user')
            ->when($query, function ($queryBuilder) use ($query) {
                $queryBuilder->where('title', 'like', "%{$query}%");
            })
            ->paginate($perPage)
            ->appends(['search' => $query]);
    }

    public function bulkInsert(array $posts)
    {
        $timestamp = now();

        foreach ($posts as &$post) {
            $post['created_at'] = $timestamp;
            $post['updated_at'] = $timestamp;
        }

        DB::table('posts')->insert($posts);
    }
}
