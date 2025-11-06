<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Validation\Rule;

class CommentController extends Controller
{
    /**
     * GET /api/posts/{post}/comments
     * Public: chỉ trả comment đã duyệt.
     * Hỗ trợ ?per_page=..., tối đa 100.
     */
    public function index(Request $request, Post $post)
    {
        $perPage = (int) $request->integer('per_page', 10);
        $perPage = $perPage > 0 ? min($perPage, 100) : 10;

        $page = $post->comments()
            ->with('user:id,name')             // thêm avatar nếu có: user:id,name,avatar
            ->where('status', 'approved')
            ->latest()
            ->paginate($perPage);

        return response()->json([
            'message' => 'OK',
            'data'    => $page->items(),
            'meta'    => [
                'current_page' => $page->currentPage(),
                'last_page'    => $page->lastPage(),
                'per_page'     => $page->perPage(),
                'total'        => $page->total(),
            ],
        ]);
    }

    /**
     * POST /api/posts/{post}/comments
     * Yêu cầu đăng nhập (auth:sanctum).
     * Comment tạo ở trạng thái pending để chờ kiểm duyệt.
     */
    public function store(Request $request, Post $post)
    {
        // Bảo đảm trả JSON khi validate fail
        $request->headers->set('Accept', 'application/json');

        $data = $request->validate([
            'content' => ['required','string','min:1','max:2000'],
        ]);

        $comment = $post->comments()->create([
            'user_id' => $request->user()->id,
            'content' => $data['content'],
            'status'  => 'pending',
        ]);

        return response()->json([
            'message' => 'Created',
            'data'    => $comment->load('user:id,name'),
        ], 201);
    }

    /**
     * POST /api/comments/{comment}/moderate   (role:admin)
     * action = approve | reject
     * Có thể kèm note sau này nếu muốn (vd: lý do reject).
     */
    public function moderate(Request $request, Comment $comment)
    {
        $request->headers->set('Accept', 'application/json');

        // Phòng tuyến 2: nếu có PostPolicy@publish thì bật dòng này.
        // Route đã có middleware('role:admin') nên bạn có thể bỏ nếu chưa tạo policy.
        $this->authorize('publish', $comment->post);

        $data = $request->validate([
            'action' => ['required', Rule::in(['approve','reject'])],
        ]);

        $newStatus = $data['action'] === 'approve' ? 'approved' : 'rejected';

        try {
            $comment->update(['status' => $newStatus]);

            return response()->json([
                'message' => 'Moderated',
                'data'    => $comment->fresh()->load('user:id,name'),
            ]);
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'Cannot moderate comment',
            ], 422);
        }
    }
}
