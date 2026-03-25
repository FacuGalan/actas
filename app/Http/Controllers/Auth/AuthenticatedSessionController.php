<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
   public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'dni' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $credentials = $request->only('dni', 'password');
        
        // ✅ CAMBIAR ESTA PARTE
        $inspector = \App\Models\Inspector::where('dni', $credentials['dni'])->first();
        
        if ($inspector && $inspector->password === $credentials['password']) {
            Auth::guard('inspector')->loginUsingId($inspector->id, $request->boolean('remember'));
            
            $request->session()->regenerate();

            return redirect()->intended(route('actas.dashboard'));
        }

        throw ValidationException::withMessages([
            'dni' => __('Las credenciales no coinciden con nuestros registros.'),
        ]);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('inspector')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
