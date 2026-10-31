<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class SettingController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'currency' => Setting::currency()['code'],
            'currencies' => config('inventory.currencies'),
            'account' => ['name' => $user->name, 'email' => $user->email],
        ]);
    }

    public function updateCurrency(Request $request)
    {
        $data = $request->validate([
            'currency' => ['required', Rule::in(array_keys(config('inventory.currencies')))],
        ]);

        Setting::set('currency', $data['currency']);

        return response()->json(Setting::currency());
    }

    public function updateAccount(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'current_password' => ['required', 'current_password'],
            'password' => ['nullable', 'confirmed', Password::min(8)],
        ]);

        $user->name = $data['name'];
        $user->email = $data['email'];

        if (! empty($data['password'])) {
            $user->password = $data['password']; // 'hashed' cast hash pannum
        }

        $user->save();

        return response()->json(['name' => $user->name, 'email' => $user->email]);
    }
}