<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Gate;

class UserController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(User::class, 'user', [
            'except' => ['show'], // Không dùng show, dùng edit
        ]);
    }

    public function authorizeResource($model, $parameter = null, array $options = [], $request = null)
    {
        // Áp dụng Gate 'manage-users' cho tất cả các action của resource controller này
        // (trừ khi có Policy cụ thể hơn được định nghĩa và ưu tiên)
        $this->middleware(function ($request, $next) {
            if (!Gate::allows('manage-users')) {
                abort(403);
            }
            return $next($request);
        });
    }


    public function index(Request $request)
    {
        $query = User::query();
        if ($request->filled('role_filter') && in_array($request->role_filter, ['admin', 'giangvien', 'sinhvien'])) {
            $query->where('role', $request->role_filter);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('student_id_code', 'like', "%{$search}%");
            });
        }
        $users = $query->orderBy('name')->paginate(15);
        return view('admin.users.index', compact('users')); // Placeholder view
    }

    public function create()
    {
        return view('admin.users.create'); // Placeholder view
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => ['required', Rule::in(['admin', 'giangvien', 'sinhvien'])],
            'student_id_code' => [
                Rule::requiredIf(fn () => $request->input('role') === 'sinhvien'),
                'nullable',
                'string',
                'max:50',
                Rule::unique('users', 'student_id_code')->where(function ($query) {
                    return $query->where('role', 'sinhvien');
                })->ignore($request->user ? $request->user->id : null) // Ignore current user on update
            ],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'student_id_code' => $validated['role'] === 'sinhvien' ? $validated['student_id_code'] : null,
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Tạo người dùng thành công.');
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user')); // Placeholder view
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'role' => ['required', Rule::in(['admin', 'giangvien', 'sinhvien'])],
            'student_id_code' => [
                Rule::requiredIf(fn () => $request->input('role') === 'sinhvien'),
                'nullable',
                'string',
                'max:50',
                 Rule::unique('users', 'student_id_code')->where(function ($query) {
                    return $query->where('role', 'sinhvien');
                })->ignore($user->id)
            ],
        ]);

        $dataToUpdate = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'student_id_code' => $validated['role'] === 'sinhvien' ? $validated['student_id_code'] : null,
        ];

        if (!empty($validated['password'])) {
            $dataToUpdate['password'] = Hash::make($validated['password']);
        }

        $user->update($dataToUpdate);

        return redirect()->route('admin.users.index')->with('success', 'Cập nhật người dùng thành công.');
    }

    public function destroy(User $user)
    {
        // Cân nhắc: không nên xóa hẳn user nếu đã có dữ liệu liên quan (lớp học, điểm danh)
        // Có thể thêm soft delete hoặc chỉ cho phép vô hiệu hóa
        if ($user->id === Auth::id()) {
             return redirect()->route('admin.users.index')->with('error', 'Không thể tự xóa chính mình.');
        }
        // Kiểm tra ràng buộc nếu cần
        // Ví dụ: if ($user->teachingCourses()->exists() || $user->enrolledCourses()->count() > 0) { ... }
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'Xóa người dùng thành công.');
    }
}