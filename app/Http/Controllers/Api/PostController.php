<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Validation\Rule;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $perPage = (int) $request->integer('per_page', 10);
        $perPage = $perPage > 0 ? min($perPage, 100) : 10;

        $sort = $request->get('sort', 'published_at');
        $dir  = strtolower($request->get('dir', 'desc')) === 'asc' ? 'asc' : 'desc';

        $sortable = ['id', 'published_at', 'views', 'title'];
        if (!in_array($sort, $sortable, true)) {
            $sort = 'published_at';
        }

        $q = trim((string) $request->get('q', ''));
        $categorySlug = $request->get('category_slug');

        $page = Post::query()
            ->with(['user:id,name', 'category:id,name,slug'])
            ->published()
            ->when($q !== '', fn($qq) => $qq->where('title', 'like', '%'.$q.'%'))
            ->when($categorySlug, fn($qq) => $qq->whereHas('category', fn($c) => $c->where('slug', $categorySlug)))
            ->orderBy($sort, $dir)
            ->paginate($perPage);

        return response()->json([
            'message' => 'OK',
            'data'    => $page->items(),
            'meta'    => [
                'current_page' => $page->currentPage(),
                'last_page'    => $page->lastPage(),
                'per_page'     => $page->perPage(),
                'total'        => $page->total(),
                'sort'         => $sort,
                'dir'          => $dir,
                'q'            => $q,
                'category'     => $categorySlug,
            ],
        ]);
    }

    public function showBySlug(string $slug)
    {
        $post = Post::with(['user:id,name', 'category:id,name,slug'])
            ->where('slug', $slug)
            ->published()
            ->firstOrFail();

        $post->increment('views');

        return response()->json([
            'message' => 'OK',
            'data'    => $post->fresh(['user:id,name', 'category:id,name,slug']),
        ]);
    }

    public function store(Request $request)
    {
        $request->headers->set('Accept', 'application/json');

        $data = $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'slug'        => ['required', 'string', 'max:255', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', 'unique:posts,slug'],
            'content'     => ['required', 'string'],
            'category_id' => ['required', 'exists:categories,id'],
        ]);

        $payload = array_merge($data, [
            'user_id'      => $request->user()->id,
            'status'       => 'draft',
            'published_at' => null,
            'views'        => 0,
        ]);

        try {
            $post = Post::create($payload);

            return response()->json([
                'message' => 'Created',
                'data'    => $post->load(['user:id,name', 'category:id,name,slug']),
            ], 201);
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'Cannot create post',
            ], 422);
        }
    }

    public function update(Request $request, Post $post)
    {
        $this->authorize('update', $post);
        $request->headers->set('Accept', 'application/json');

        $data = $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'slug'        => ['required', 'string', 'max:255', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', Rule::unique('posts','slug')->ignore($post->id)],
            'content'     => ['required', 'string'],
            'category_id' => ['required', 'exists:categories,id'],
            'status'      => ['sometimes', 'in:draft,published'],
        ]);

        if (isset($data['status']) && $data['status'] === 'published' && !$post->published_at) {
            unset($data['status']);
        }

        $post->update($data);

        return response()->json([
            'message' => 'Updated',
            'data'    => $post->fresh(['user:id,name', 'category:id,name,slug']),
        ]);
    }

    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);

        try {
            $post->delete();

            return response()->json([
                'message' => 'Deleted',
                'deleted' => true,
            ]);
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'Cannot delete post',
                'deleted' => false,
            ], 422);
        }
    }

    public function publish(Request $request, Post $post)
    {
        $this->authorize('publish', $post);

        $post->update([
            'status'       => 'published',
            'published_at' => now(),
        ]);

        return response()->json([
            'message' => 'Published',
            'post'    => $post->fresh(['user:id,name', 'category:id,name,slug']),
        ]);
    }

    public function unpublish(Request $request, Post $post)
    {
        $this->authorize('publish', $post);

        $post->update([
            'status'       => 'draft',
            'published_at' => null,
        ]);

        return response()->json([
            'message' => 'Unpublished',
            'post'    => $post->fresh(['user:id,name', 'category:id,name,slug']),
        ]);
    }
}
