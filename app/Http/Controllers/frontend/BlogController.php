<?php

namespace App\Http\Controllers\frontend;

use App\Models\BlogComment;
use App\Models\Blog;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BlogController extends Controller
{
  public function index()
  {
    $blogs = Blog::latest()
      ->paginate(3);
    return view('frontend.blogs.index', compact('blogs'));
  }

  public function show($id)
  {
    $blog = Blog::findOrFail($id);


    $previousBlog = Blog::where('id', '>', $blog->id)
      ->orderBy('id')
      ->first();

    $nextBlog = Blog::where('id', '<', $blog->id)
      ->orderBy('id', 'desc')
      ->first();

    $averageRating = $blog->ratings()->avg('rating');
    $ratingCount = $blog->ratings()->count();
    $userRating = auth()->check()
      ? $blog->ratings()->where('user_id', auth()->id())->value('rating')
      : null;
    $comments = $blog->comments()
      ->whereNull('parent_id')
      ->with([
        'user:id,name,avatar',
        'replies.user:id,name,avatar',
      ])
      ->latest()
      ->get();

    return view('frontend.blogs.show', compact(
      'blog',
      'previousBlog',
      'nextBlog',
      'averageRating',
      'ratingCount',
      'userRating',
      'comments'
    ));
  }

  public function rate(Request $request)
  {
    $validated = $request->validate([
      'id_blog' => ['required', 'integer', 'exists:blogs,id'],
      'rating' => ['required', 'integer', 'min:1', 'max:5'],
    ]);

    $blog = Blog::findOrFail($validated['id_blog']);

    $blog->ratings()->updateOrCreate(
      ['user_id' => $request->user()->id],
      ['rating' => $validated['rating']]
    );

    $averageRating = $blog->ratings()->avg('rating');

    return response()->json([
      'message' => 'Đánh giá thành công.',
      'rating' => $validated['rating'],
      'averageRating' => number_format($averageRating, 1),
      'ratingCount' => $blog->ratings()->count(),
    ]);
  }

  public function comment(Request $request)
  {
    $validated = $request->validate([
        'id_blog' => ['required', 'integer', 'exists:blogs,id'],
        'parent_id' => ['nullable', 'integer', 'exists:blog_comments,id'],
        'content' => ['required', 'string', 'max:2000'],
    ]);

    $blog = Blog::findOrFail($validated['id_blog']);

    if (!empty($validated['parent_id'])) {
        $parentExistsOnThisBlog = $blog->comments()
            ->whereKey($validated['parent_id'])
            ->whereNull('parent_id')
            ->exists();

        if (!$parentExistsOnThisBlog) {
            return response()->json([
                'message' => 'Bình luận không có trong bài viết.',
            ], 422);
        }
    }

    $comment = $blog->comments()->create([
        'user_id' => $request->user()->id,
        'parent_id' => $validated['parent_id'] ?? null,
        'content' => $validated['content'],
    ]);

    $comment->load('user:id,name,avatar');

    return response()->json([
        'data' => $comment,
    ]);
  }
}
