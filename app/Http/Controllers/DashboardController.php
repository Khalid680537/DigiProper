<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $totalCount = Property::count();
        $totalValue = (float) Property::sum('imputed_value_inr');
        $totalRent = (float) Property::sum('rent_yearly_inr');
        $averageYield = $totalValue > 0
            ? round(($totalRent / $totalValue) * 100, 2)
            : null;

        $recent = Property::latest('created_at')->take(5)->get();

        return view('dashboard', [
            'totalCount' => $totalCount,
            'totalValue' => $totalValue,
            'totalRent' => $totalRent,
            'averageYield' => $averageYield,
            'recent' => $recent,
        ]);
    }
}
