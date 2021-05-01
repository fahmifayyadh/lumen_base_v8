<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;

class AuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
      if (empty($request->header)) {
        return response()->json(['error'=>'needed api_token'], 401);
      }
      try {
        $token = Crypt::decrypt($request->header);
      } catch (\Exception $e) {
        return response()->json(['error'=>'Unauthorized'], 401);
      }

      $data = explode('+',$token);
      $user = User::where('email', $data[0])->first();
      // $pswd = str_replace("\/", '', $data[1]);

      if (empty($user) && $data[1] != $user->password) {
        return response()->json(['error'=>'Unauthorized'], 401);
      }

        return $next($request);
    }
}
