<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $u = $request->user();

        return response()->json([
            'id' => $u->id,
            'name' => $u->name,
            'email' => $u->email,
            'avatar_url' => $u->avatar_url,
            'phone' => $u->phone,
            'shipping_recipient_name' => $u->shipping_recipient_name,
            'shipping_line1' => $u->shipping_line1,
            'shipping_line2' => $u->shipping_line2,
            'shipping_city' => $u->shipping_city,
            'shipping_state' => $u->shipping_state,
            'shipping_postcode' => $u->shipping_postcode,
            'shipping_country' => $u->shipping_country,
        ]);
    }

    public function update(Request $request): JsonResponse
    {
        if ($request->has('avatar_url')) {
            $raw = $request->input('avatar_url');
            if ($raw === null || $raw === '' || (is_string($raw) && trim($raw) === '')) {
                $request->merge(['avatar_url' => null]);
            }
        }

        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'avatar_url' => ['sometimes', 'nullable', 'string', 'max:2048', 'url'],
            'phone' => ['nullable', 'string', 'max:64'],
            'shipping_recipient_name' => ['nullable', 'string', 'max:255'],
            'shipping_line1' => ['nullable', 'string', 'max:255'],
            'shipping_line2' => ['nullable', 'string', 'max:255'],
            'shipping_city' => ['nullable', 'string', 'max:120'],
            'shipping_state' => ['nullable', 'string', 'max:80'],
            'shipping_postcode' => ['nullable', 'string', 'max:32'],
            'shipping_country' => ['nullable', 'string', 'max:120'],
        ]);

        $request->user()->fill($data);
        $request->user()->save();

        return $this->show($request);
    }

    public function updatePassword(Request $request): JsonResponse
    {
        $data = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = $request->user();
        if (! Hash::check($data['current_password'], $user->getAuthPassword())) {
            throw ValidationException::withMessages([
                'current_password' => [__('The current password is incorrect.')],
            ]);
        }

        $user->password = $data['password'];
        $user->save();

        return response()->json(['ok' => true]);
    }
}
