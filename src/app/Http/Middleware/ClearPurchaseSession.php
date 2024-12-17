<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ClearPurchaseSession
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // 購入関連ページの場合はセッションをクリアしない
        if ($request->is('purchase/*')) {
            return $next($request);
        }

        // 上記以外のページにアクセスした場合はセッションをクリア
        Session::forget('selected_address');

        return $next($request);
    }
}
