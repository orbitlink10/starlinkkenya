<?php

namespace App\Http\Controllers;

use App\Support\SeoData;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(Request $request): View|RedirectResponse
    {
        if (Auth::check()) {
            return $this->authenticatedRedirect($request);
        }

        return view('auth.login', [
            'seo' => [
                'title' => 'Login | '.SeoData::siteName(),
                'description' => 'Secure admin login for Starlink Kenya Installers.',
                'canonical' => route('login'),
                'robots' => 'noindex,follow',
            ],
        ]);
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors([
                'email' => 'Invalid login credentials.',
            ])->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->intended($this->authenticatedUrl($request));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->to($request->getSchemeAndHttpHost().'/login.php');
    }

    private function authenticatedRedirect(Request $request): RedirectResponse
    {
        return redirect()->to($this->authenticatedUrl($request));
    }

    private function authenticatedUrl(Request $request): string
    {
        if (Auth::user()?->isAdmin()) {
            return $request->getSchemeAndHttpHost().'/dashboard.php';
        }

        return route('account.dashboard');
    }
}
