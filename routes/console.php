<?php

use App\Console\Commands\FetchPostsFromApi;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Artisan::command('fetch-posts', function () {
    Artisan::call(FetchPostsFromApi::class);
})->purpose('Fetch posts from external API and store them')->hourly();
