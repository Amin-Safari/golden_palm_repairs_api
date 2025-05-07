<?php

namespace App\Http\Controllers;

use App\Models\CategoryDescription;
use App\Models\DescriptionUsage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DescriptionStatisticsController extends Controller
{
    public function monthlyStatistics(Request $request)
    {
        $year = $request->input('year', date('Y'));
        $month = $request->input('month', date('m'));

        $statistics = DescriptionUsage::select(
            'category_descriptions.description',
            'device_categories.name as category_name',
            DB::raw('COUNT(*) as usage_count'),
            DB::raw('SUM(description_usages.price_at_time) as total_revenue')
        )
            ->join('category_descriptions', 'description_usages.category_description_id', '=', 'category_descriptions.id')
            ->join('device_categories', 'category_descriptions.device_category_id', '=', 'device_categories.id')
            ->whereYear('description_usages.created_at', $year)
            ->whereMonth('description_usages.created_at', $month)
            ->groupBy('category_descriptions.id', 'category_descriptions.description', 'device_categories.name')
            ->orderBy('usage_count', 'desc')
            ->get();

        return response()->json([
            'year' => $year,
            'month' => $month,
            'statistics' => $statistics
        ]);
    }

    public function yearlyStatistics(Request $request)
    {
        $year = $request->input('year', date('Y'));

        $statistics = DescriptionUsage::select(
            'category_descriptions.description',
            'device_categories.name as category_name',
            DB::raw('COUNT(*) as usage_count'),
            DB::raw('SUM(description_usages.price_at_time) as total_revenue')
        )
            ->join('category_descriptions', 'description_usages.category_description_id', '=', 'category_descriptions.id')
            ->join('device_categories', 'category_descriptions.device_category_id', '=', 'device_categories.id')
            ->whereYear('description_usages.created_at', $year)
            ->groupBy('category_descriptions.id', 'category_descriptions.description', 'device_categories.name')
            ->orderBy('usage_count', 'desc')
            ->get();

        return response()->json([
            'year' => $year,
            'statistics' => $statistics
        ]);
    }
}
