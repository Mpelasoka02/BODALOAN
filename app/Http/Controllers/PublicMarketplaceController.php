<?php

namespace App\Http\Controllers;

use App\Models\Motorcycle;
use Illuminate\Http\Request;

class PublicMarketplaceController extends Controller
{
    public function index(Request $request)
    {
        $query = Motorcycle::with('owner')
            ->where('verification_status', 'verified');

        if ($min = $request->min_price) {
            $query->where('loan_amount', '>=', $min);
        }
        if ($max = $request->max_price) {
            $query->where('loan_amount', '<=', $max);
        }

        $sort = $request->get('sort', 'newest');
        if ($sort === 'price_low') $query->orderBy('loan_amount', 'asc');
        elseif ($sort === 'price_high') $query->orderBy('loan_amount', 'desc');
        else $query->latest();

        $motorcycles = $query->paginate(12)->withQueryString();

        return view('marketplace.index', compact('motorcycles'));
    }

    public function show(Motorcycle $motorcycle)
    {
        $motorcycle->load('owner');

        return view('marketplace.show', compact('motorcycle'));
    }
}
