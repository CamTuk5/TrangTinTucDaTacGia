<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    /**
     * GET /api/categories
     * Hỗ trợ:
     *  - ?q=... (search theo name)
     *  - ?per_page=... (tối đa 100, mặc định 10)
     *  - ?sort=created_at|name|id & ?dir=asc|desc (mặc định id desc)
     */
    public function index(Request $request)
    {
        $perPage = (int) $request->integer('per_page', 10);
        $perPage = $perPage > 0 ? min($perPage, 100) : 10;

        $sort = $request->get('sort', 'id');
        $dir  = strtolower($request->get('dir', 'desc')) === 'asc' ? 'asc' : 'desc';
        // Chỉ cho phép sort theo các cột an toàn:
        $sortable = ['id','name','created_at'];
        if (! in_array($sort, $sortable, true)) {
            $sort = 'id';
        }

        $q = trim((string) $request->get('q', ''));

        $query = Category::query()
            ->select('id','name','slug','created_at')
            ->when($q !== '', fn($qq) => $qq->where('name', 'like', '%'.$q.'%'))
            ->orderBy($sort, $dir);

        $page = $query->paginate($perPage);

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
            ],
        ]);
    }

    /**
     * POST /api/categories  (role:admin)
     */
    public function store(Request $request)
    {
        // Trả lỗi validate dạng JSON
        $request->headers->set('Accept', 'application/json');

        $data = $request->validate([
            'name' => ['required','string','max:255', 'unique:categories,name'],
            'slug' => ['required','string','max:255', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', 'unique:categories,slug'],
        ]);

        try {
            $cat = Category::create($data);

            return response()->json([
                'message' => 'Created',
                'data'    => $cat,
            ], 201);
        } catch (QueryException $e) {
            // 23000 = integrity constraint violation (FK/unique)
            $code = (int) ($e->errorInfo[0] ?? 0);
            return response()->json([
                'message' => 'Cannot create category',
                'code'    => $code,
            ], 422);
        }
    }

    /**
     * PUT /api/categories/{category}  (role:admin)
     */
    public function update(Request $request, Category $category)
    {
        $request->headers->set('Accept', 'application/json');

        $data = $request->validate([
            'name' => [
                'sometimes','required','string','max:255',
                Rule::unique('categories','name')->ignore($category->id),
            ],
            'slug' => [
                'sometimes','required','string','max:255',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('categories','slug')->ignore($category->id),
            ],
        ]);

        try {
            $category->update($data);

            return response()->json([
                'message' => 'Updated',
                'data'    => $category,
            ]);
        } catch (QueryException $e) {
            $code = (int) ($e->errorInfo[0] ?? 0);
            return response()->json([
                'message' => 'Cannot update category',
                'code'    => $code,
            ], 422);
        }
    }

    /**
     * DELETE /api/categories/{category}  (role:admin)
     */
    public function destroy(Category $category)
    {
        try {
            $category->delete();

            return response()->json([
                'message' => 'Deleted',
                'deleted' => true,
            ]);
        } catch (QueryException $e) {
            // Ví dụ: đang có posts tham chiếu category_id → FK ràng buộc
            $isFkViolation = ((string)($e->errorInfo[0] ?? '')) === '23000';
            return response()->json([
                'message' => $isFkViolation
                    ? 'Cannot delete category: in use by other records'
                    : 'Cannot delete category',
                'deleted' => false,
            ], 422);
        }
    }
}
