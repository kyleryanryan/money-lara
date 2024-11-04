<?php

namespace App\Services\Validation;

use Illuminate\Support\Facades\Validator;

class PostValidator
{
    /**
     * Validate the given post data.
     *
     * @param array $post
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function validate(array $post)
    {
        return Validator::make($post, [
            'userId' => 'required|integer',
            'id' => 'required|integer',
            'title' => 'required|string|max:255',
            'body' => 'required|string',
        ]);
    }
}
