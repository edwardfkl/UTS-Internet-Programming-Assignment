<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\AdminListRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $q = AdminListRequest::search($request);
        [$sort, $dir] = AdminListRequest::sort(
            $request,
            ['id', 'name', 'email', 'is_admin', 'created_at', 'updated_at'],
            'id',
            'desc',
        );

        $query = User::query();
        if ($q !== null) {
            $like = '%'.$q.'%';
            $query->where(function ($sub) use ($like) {
                $sub->where('name', 'like', $like)
                    ->orWhere('email', 'like', $like);
            });
        }
        $query->orderBy($sort, $dir);

        $users = $query->paginate(20)->withQueryString();

        return view('admin.users.index', compact('users', 'sort', 'dir', 'q'));
    }

    public function edit(User $user): View
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        if ($request->input('avatar_url') === '') {
            $request->merge(['avatar_url' => null]);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'is_admin' => ['sometimes', 'boolean'],
            'avatar_url' => ['nullable', 'string', 'max:2048', 'url'],
            'phone' => ['nullable', 'string', 'max:64'],
            'shipping_recipient_name' => ['nullable', 'string', 'max:255'],
            'shipping_line1' => ['nullable', 'string', 'max:255'],
            'shipping_line2' => ['nullable', 'string', 'max:255'],
            'shipping_city' => ['nullable', 'string', 'max:120'],
            'shipping_state' => ['nullable', 'string', 'max:80'],
            'shipping_postcode' => ['nullable', 'string', 'max:32'],
            'shipping_country' => ['nullable', 'string', 'max:120'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        foreach (
            [
                'phone',
                'shipping_recipient_name',
                'shipping_line1',
                'shipping_line2',
                'shipping_city',
                'shipping_state',
                'shipping_postcode',
                'shipping_country',
            ] as $key
        ) {
            if (($data[$key] ?? '') === '') {
                $data[$key] = null;
            }
        }

        $wasAdmin = $user->is_admin;
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->is_admin = $request->boolean('is_admin');
        $user->avatar_url = $data['avatar_url'] ?? null;
        $user->phone = $data['phone'];
        $user->shipping_recipient_name = $data['shipping_recipient_name'];
        $user->shipping_line1 = $data['shipping_line1'];
        $user->shipping_line2 = $data['shipping_line2'];
        $user->shipping_city = $data['shipping_city'];
        $user->shipping_state = $data['shipping_state'];
        $user->shipping_postcode = $data['shipping_postcode'];
        $user->shipping_country = $data['shipping_country'];

        if (! empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        if ($wasAdmin && ! $user->is_admin && User::query()->where('is_admin', true)->whereKeyNot($user->id)->doesntExist()) {
            return back()->withErrors(['is_admin' => 'Cannot remove the last admin user.'])->withInput();
        }

        $user->save();

        return redirect()->route('admin.users.index')->with('success', 'User updated.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($request->user()->is($user)) {
            return back()->withErrors(['delete' => 'You cannot delete your own account while logged in.']);
        }

        if ($user->is_admin && User::query()->where('is_admin', true)->count() <= 1) {
            return back()->withErrors(['delete' => 'Cannot delete the last admin user.']);
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User deleted.');
    }
}
