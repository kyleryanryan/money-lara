<?php

namespace App\Services;

class PostApiService extends ApiService
{
    public function __construct()
    {
        parent::__construct();
        $this->baseUrl = config('app.post_api');
    }

    /**
     * Fetch posts from the external API.
     *
     * @return array|null
     */
    public function fetchPosts()
    {
        return $this->get('/');
    }
}
