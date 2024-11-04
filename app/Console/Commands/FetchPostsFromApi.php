<?php
namespace App\Console\Commands;

use App\Models\User;
use App\Repositories\PostRepository;
use App\Services\PostApiService;
use App\Services\Validation\PostValidator;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Database\Eloquent\Collection;

class FetchPostsFromApi extends Command
{
    private const CHUNK_SIZE = 50;
    protected $signature = 'app:fetch-posts-from-api';
    protected $description = 'Fetch posts from an external API and store them in the database';

    protected $postApiService;
    protected $postRepository;
    protected $postValidator;

    public function __construct(PostApiService $postApiService, PostRepository $postRepository, PostValidator $postValidator)
    {
        parent::__construct();
        $this->postApiService = $postApiService;
        $this->postRepository = $postRepository;
        $this->postValidator = $postValidator;
    }

    public function handle()
    {
        try {
            $users = $this->fetchUsers();
            if ($users->isEmpty()) {
                $this->error('No users found in the database.');
                return;
            }

            $posts = $this->fetchPostsFromApi();
            if ($posts) {
                $this->processPosts($posts, $users);
            } else {
                $this->error('No posts fetched from the API.');
            }
        } catch (Exception $e) {
            $this->logError('An error occurred during the fetch operation: ' . $e->getMessage(), $e);
        }
    }

    private function fetchUsers(): Collection
    {
        $this->info('Fetching users from the database...');
        return User::all()->keyBy('id');
    }

    private function fetchPostsFromApi(): ?array
    {
        try {
            $this->info('Fetching posts from the external API...');
            return $this->postApiService->fetchPosts();
        } catch (Exception $e) {
            $this->logError('Error fetching posts from the API: ' . $e->getMessage(), $e);
            return null;
        }
    }

    private function processPosts(array $posts, Collection $users)
    {
        foreach (array_chunk($posts, self::CHUNK_SIZE) as $chunkedPosts) {
            $insertData = [];

            foreach ($chunkedPosts as $post) {
                $validator = $this->postValidator->validate($post);

                if ($validator->fails()) {
                    $this->logError("Validation failed for post: " . json_encode($post) . ' - ' . json_encode($validator->errors()), new Exception("Validation Error"));
                    continue;
                }

                if ($users->has($post['userId'])) {
                    if (!$this->postRepository->findByApiId($post['id'])) {
                        $insertData[] = [
                            'id' => $post['id'],
                            'title' => $post['title'],
                            'body' => $post['body'],
                            'user_id' => $post['userId'],
                        ];
                    } else {
                        $this->info("Post ID {$post['id']} already exists.");
                    }
                } else {
                    $this->warn("No matching user found for API user ID {$post['userId']}. Skipping post ID {$post['id']}.");
                }
            }

            if (!empty($insertData)) {
                $this->postRepository->bulkInsert($insertData);
                $this->info('Inserted ' . count($insertData) . ' posts.');
            }
        }

        $this->info('Completed fetching and storing posts.');
    }

    private function logError($message, Exception $e)
    {
        Log::error($message, ['exception' => $e]);
        $this->error($message);
    }
}
