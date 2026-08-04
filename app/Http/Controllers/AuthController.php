<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Check if account is active (Customers are usually auto-active, Farmers need approval)
            if ($user->status !== 'active' && $user->role->name === 'farmer') {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Your supply node is currently pending administrative verification.',
                ]);
            }

            // Redirect based on role
            switch ($user->role->name) {
                case 'admin': return redirect()->route('admin.dashboard');
                case 'farmer': return redirect()->route('farmer.dashboard');
                default: return redirect()->route('marketplace');
            }
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function showRegister()
    {
        $roles = Role::whereIn('name', ['farmer', 'customer'])->get();
        return view('auth.register', compact('roles'));
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role_id' => ['required', 'integer', 'exists:roles,id'],
            'farmName' => ['nullable', 'string', 'max:255'],
            'latitude' => ['required', 'numeric'],
            'longitude' => ['required', 'numeric'],
            'address' => ['required', 'string', 'max:500'],
        ]);

        $role = Role::findOrFail($request->role_id);
        $isFarmer = $role->name === 'farmer';

        if ($isFarmer && empty($request->farmName)) {
            return redirect()->back()->withErrors(['farmName' => 'A farm designation is required for supplier nodes.'])->withInput();
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $role->id,
            'status' => $isFarmer ? 'pending' : 'active',
            'farm_name' => $isFarmer ? $request->farmName : null,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'address' => $request->address,
        ]);

        if ($user->status === 'active') {
            Auth::login($user);
            return redirect()->route('marketplace')->with('success', 'Node established. Welcome to the exchange.');
        }

        return redirect()->route('login')->with('success', 'Supply node registered. Pending administrative authorization.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    public function handleRecovery(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users,email']);

        // Generate a random 6-digit code for the demo
        $recoveryCode = rand(100000, 999999);

        // Store in session for validation
        session()->put('recovery_code', $recoveryCode);
        session()->put('recovery_email', $request->email);

        return redirect()->back()->with([
            'success' => 'Recovery protocol initiated. Check your terminal below.',
            'display_code' => $recoveryCode, // This is for the "Card view"
        ]);
    }

    public function resetWithCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|string',
            'password' => 'required|min:8|confirmed'
        ]);

        $sessionCode = session()->get('recovery_code');
        $sessionEmail = session()->get('recovery_email');

        if ($request->code != $sessionCode || $request->email != $sessionEmail) {
            return redirect()->back()->withErrors(['code' => 'The verification token provided is invalid or has expired.']);
        }

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return redirect()->back()->withErrors(['email' => 'Identity mismatch detected.']);
        }

        $user->update(['password' => \Illuminate\Support\Facades\Hash::make($request->password)]);

        // Clear session
        session()->forget(['recovery_code', 'recovery_email']);

        return redirect()->route('login')->with('success', 'Security key successfully re-initialized. Access granted.');
    }
}