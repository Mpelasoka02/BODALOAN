<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\Motorcycle;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('q', '');
        $results = [];
        $user = Auth::user();

        if (strlen(trim($query)) >= 2) {
            $q = trim($query);

            $motorcycleQuery = Motorcycle::where('plate_number', 'like', "%{$q}%")
                ->orWhere('make', 'like', "%{$q}%")
                ->orWhere('model', 'like', "%{$q}%");

            if ($user->isOwner()) {
                $motorcycleQuery->where('owner_id', $user->id);
            }

            $results['motorcycles'] = $motorcycleQuery->get();

            if ($user->isAdmin()) {
                $results['users'] = User::where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('phone', 'like', "%{$q}%")
                    ->get();
            } else {
                $results['users'] = collect();
            }

            $loanQuery = Loan::with('motorcycle')
                ->whereHas('motorcycle', function ($mq) use ($q) {
                    $mq->where('plate_number', 'like', "%{$q}%");
                })
                ->orWhere('id', 'like', "%{$q}%");

            if ($user->isOwner()) {
                $loanQuery->where('owner_id', $user->id);
            } elseif ($user->isDriver()) {
                $loanQuery->where('driver_id', $user->id);
            }

            $results['loans'] = $loanQuery->get();
        }

        return view('search.results', compact('query', 'results'));
    }
}
