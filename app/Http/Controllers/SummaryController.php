<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SummaryController extends Controller
{
    public function index(Request $request)
    {
        $data = $request->validate([
            'month' => 'nullable|date_format:Y-m',
        ]);

        $month = $data['month'] ?? now()->format('Y-m');
        [$year, $monthNumber] = explode('-', $month);

        $rows = Transaction::query()
            ->join('categories', 'categories.id', '=', 'transactions.category_id')
            ->where('transactions.user_id', $request->user()->id)
            ->whereYear('transactions.date', $year)
            ->whereMonth('transactions.date', $monthNumber)
            ->groupBy('categories.id', 'categories.name', 'categories.type')
            ->select(
                'categories.id as category_id',
                'categories.name',
                'categories.type',
                DB::raw('SUM(transactions.amount) as total')
            )
            ->orderByDesc('total')
            ->get();

        $income = (float) $rows->where('type', 'income')->sum('total');
        $expense = (float) $rows->where('type', 'expense')->sum('total');

        return response()->json([
            'month' => $month,
            'total_income' => round($income, 2),
            'total_expense' => round($expense, 2),
            'balance' => round($income - $expense, 2),
            'by_category' => $rows->map(fn ($r) => [
                'category_id' => $r->category_id,
                'name' => $r->name,
                'type' => $r->type,
                'total' => round((float) $r->total, 2),
            ])->values(),
        ]);
    }
}