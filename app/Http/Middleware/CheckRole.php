<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;

class CheckRole
{
  /**
   * Handle an incoming request.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
   * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
   */
  public function handle(Request $request, Closure $next, ...$roles)
  {
    foreach ($roles as $role) {
      if ($request->user()->hasRole($role)) {
        // abort(401, 'This action is unauthorized.');
        // return redirect('/welcome')->with('error', 'You are not authorized to access this page.');
        return $next($request);
      }
    }
    return Redirect::guest(URL::route('notAdmin.notice'));
  }
}
