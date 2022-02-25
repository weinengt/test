<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Http\Requests\RegisteredUserStoreRequest;
use App\Services\UserService;

class RegisteredUserController extends Controller
{

    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Display the registration view.
     */
    public function create()
    {
        return Inertia::render('Auth/Register.screen');
    }

    /**
     * Store a new user.
     */
    public function store(RegisteredUserStoreRequest $request)
    {

        $authErrors = $this->userService->register($request);

        if (isset($authErrors)) {
            return redirect()->back()->withErrors($authErrors);
        } else {
            return redirect('/register-complete');
        }
    }

    /**
     * Display the registration complete view
     */
    public function show()
    {
        return Inertia::render('Auth/RegisterComplete.screen');
    }
}