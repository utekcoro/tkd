<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetActiveBranch
{
    public function handle(Request $request, Closure $next)
    {
        // Jika session 'active_branch' sudah diset, bagikan ke seluruh view
        if (session()->has('active_branch')) {
            $branchId = session('active_branch');
            view()->share('activeBranchId', $branchId);
            session(['active_branch_id' => $branchId]); // pastikan ada juga active_branch_id
        }

        return $next($request);
    }
}
