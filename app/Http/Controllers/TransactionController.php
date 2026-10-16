<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'month' => 'nullable|date_format:Y-m',
        ]);

        $query = Transaction::with('category')
            ->where('user_id', $request->user()->id);

        if ($request->filled('month')) {
            [$year, $month] = explode('-', $request->month);
            $query->whereYear('date', $year)->whereMonth('date', $month);
        }

        return $query->orderByDesc('date')->orderByDesc('id')->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category_id' => [
                'required',
                Rule::exists('categories', 'id')->where('user_id', $request->user()->id),
            ],
            'amount' => 'required|numeric|min:0.01',
            'note' => 'nullable|string|max:255',
            'date' => 'required|date',
        ]);

        $transaction = Transaction::create([
            'user_id' => $request->user()->id,
            'category_id' => $data['category_id'],
            'amount' => $data['amount'],
            'note' => $data['note'] ?? null,
            'date' => $data['date'],
        ]);

        return response()->json($transaction->load('category'), 201);
    }

    public function update(Request $request, $id)
    {
        $transaction = Transaction::where('user_id', $request->user()->id)
            ->findOrFail($id);

        $data = $request->validate([
            'category_id' => [
                'required',
                Rule::exists('categories', 'id')->where('user_id', $request->user()->id),
            ],
            'amount' => 'required|numeric|min:0.01',
            'note' => 'nullable|string|max:255',
            'date' => 'required|date',
        ]);

        $transaction->update($data);

        return $transaction->load('category');
    }

    public function destroy(Request $request, $id)
    {
        $transaction = Transaction::where('user_id', $request->user()->id)
            ->findOrFail($id);

        $transaction->delete();

        return response()->json(['message' => 'Transaction deleted']);
    }
}