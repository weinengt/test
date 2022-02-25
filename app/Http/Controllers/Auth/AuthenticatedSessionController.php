<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Http\Requests\LogInStoreRequest;
use App\Services\UserService;
use App\Interfaces\UserSessionScopeInterface;

class AuthenticatedSessionController extends Controller implements UserSessionScopeInterface
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Display the login view.
     */
    public function create()
    {
        return Inertia::render('Auth/Login.screen');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LogInStoreRequest $request)
    {
        $result = $this->userService->signIn($request);

        if (isset($result['authErrors'])) {
            return redirect()->back()->withErrors($result['authErrors']);
        } else {
            return back();
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request)
    {
        // Destroy user session
        $this->userService->manageUserSession(self::DESTROY_SESSION, $request, true, null);
        return redirect('/');
    }
}
