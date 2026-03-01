<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Http\Requests\StoreUserRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::with('role');

        if ($request->has('search')) {
            $users->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('email', 'like', '%' . $request->search . '%');
        }

        $users = $users->paginate(15);
        return view('settings.users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('settings.users.create', compact('roles'));
    }

    public function store(StoreUserRequest $request)
    {
        try {
            $data = $request->validated();
            $data['password'] = Hash::make($data['password']);

            $user = User::create($data);
            return redirect()->route('settings.users.index')
                ->with('success', 'تم إضافة المستخدم بنجاح');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        return view('settings.users.edit', compact('user', 'roles'));
    }

    public function update(StoreUserRequest $request, User $user)
    {
        try {
            $data = $request->validated();

            // إذا لم يتغير كلمة المرور فلا نحددها
            if (!$request->has('password') || empty($request->password)) {
                unset($data['password']);
            } else {
                $data['password'] = Hash::make($data['password']);
            }

            $user->update($data);
            return redirect()->route('settings.users.index')
                ->with('success', 'تم تحديث المستخدم بنجاح');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function destroy(User $user)
    {
        try {
            // عدم السماح بحذف المستخدم الحالي
            if ($user->id === Auth::id()) {
                return back()->withErrors(['error' => 'لا يمكن حذف حسابك الشخصي']);
            }

            $user->delete();
            return redirect()->route('settings.users.index')
                ->with('success', 'تم حذف المستخدم بنجاح');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    // الملف الشخصي
    public function profile()
    {
        $user = Auth::user();
        return view('profile.show', compact('user'));
    }

    // عرض صفحة تغيير كلمة المرور
    public function changePasswordForm()
    {
        return view('profile.change-password');
    }

    // معالجة تغيير كلمة المرور
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'current_password.required' => 'كلمة المرور الحالية مطلوبة',
            'password.required' => 'كلمة المرور الجديدة مطلوبة',
            'password.min' => 'كلمة المرور يجب أن تكون 8 أحرف على الأقل',
            'password.confirmed' => 'كلمات المرور غير متطابقة',
        ]);

        $user = Auth::user();

        // التحقق من كلمة المرور الحالية
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'كلمة المرور الحالية غير صحيحة']);
        }

        // تحديث كلمة المرور
        $user->update(['password' => Hash::make($request->password)]);

        return redirect()->route('profile.show')
            ->with('success', 'تم تغيير كلمة المرور بنجاح');
    }
}
