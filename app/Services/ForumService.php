<?php

namespace App\Services;

use App\Forum;
use App\Http\Requests\StoreForumRequest;

//Extract Class
//Handles the creation of a forum post, encapsulating the related business logic
class ForumService
{
    public function createPost($data, $projectId, $userId)
    {
        Forum::create([
            'title' => $data['title'],
            'content' => $data['content'],
            'category' => $data['category'],
            'image_urls' => $data['image_urls'],
            'project_id' => $projectId,
            'user_id' => $userId,
        ]);
    }

    // Add other related methods as needed
}
