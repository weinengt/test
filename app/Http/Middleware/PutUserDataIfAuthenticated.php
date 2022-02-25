<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PutUserDataIfAuthenticated
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $matched_user_doc = NULL;

        if ($request->session()->has('idToken')) {
            try {
                $auth = app('firebase.auth');
                $verified_id_token = $auth->verifyIdToken($request->session()->get('idToken'), false, 10000000);

                // get user document
                $uid = $verified_id_token->claims()->get('sub');
                $user_docs = app('firebase.firestore')->database()->collection('users')->where('_id', '=', $uid)->limit(1)->documents();
                $matched_user_doc = $user_docs?->rows()[0]?->data() ?? NULL;
            } catch (FailedVerifyToken $e) {
                echo 'The token is invalid: ' . $e->getMessage();
                $matched_user_doc = NULL;
            } 
        }

        Inertia::share([
            'user_data' => $matched_user_doc
        ]);

        return $next($request);
    }
}
