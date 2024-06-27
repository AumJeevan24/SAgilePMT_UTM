<?php

namespace App\Http\Controllers;

use App\Forum;
use App\Http\Requests\StoreForumRequest;
use App\Services\ForumService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

//Inline Method
//Utilizes the ForumService for creating posts, delegating the responsibility to the service class
class ForumController extends Controller
{
    protected $forumService;
    protected $projectId;

    public function __construct(ForumService $forumService)
    {
        $this->forumService = $forumService;

        $this->middleware(function ($request, $next) {
            $this->projectId = $request->route('projectId');
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $categoryFilter = $request->input('category');

        $forumPosts = Forum::where('project_id', $this->projectId)
            ->when($categoryFilter, function ($query) use ($categoryFilter) {
                return $query->where('category', $categoryFilter);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('forum.index', [
            'forumPosts' => $forumPosts,
            'selectedCategory' => $categoryFilter,
            'projectId' => $this->projectId,
        ]);
    }

    public function view($forumPostId)
    {
        $forumPost = Forum::where('project_id', $this->projectId)->findOrFail($forumPostId);

        return view('forum.view', [
            'projectId' => $this->projectId,
            'forumPost' => $forumPost,
        ]);
    }

    public function create()
    {
        return view('forum.create', ['projectId' => $this->projectId]);
    }

    public function store(StoreForumRequest $request)
    {
        $userId = Auth::id();

        $this->forumService->createPost(
            $request->validated(),
            $this->projectId,
            $userId
        );

        return redirect()->route('forum.index', ['projectId' => $this->projectId])->with('success', 'Forum post created successfully!');
    }

    public function edit($id)
    {
        $forumPost = Forum::findOrFail($id);
        $projectId = $forumPost->project_id;

        return view('forum.edit', compact('forumPost', 'projectId'));
    }

    public function update(Request $request, Forum $forumPost)
    {
        $forumPost->update(['content' => $request->input('updatedContent')]);

        return redirect()->back()->with('success', 'Forum post updated successfully!');
    }

    public function destroy(Forum $forumPost)
    {
        $forumPost->delete();

        return redirect()->route('forum.index', ['projectId' => $forumPost->project_id])->with('success', 'Forum post deleted successfully');
    }
}
