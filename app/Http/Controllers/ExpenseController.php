<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $date = $request->date ? Carbon::parse($request->date) : Carbon::today();
        
        $expenses = Expense::whereDate('expense_date', $date)
            ->with('user')
            ->latest()
            ->paginate(10);
            
        $totalAmount = Expense::whereDate('expense_date', $date)->sum('amount');
        
        return view('expenses.index', compact('expenses', 'date', 'totalAmount'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('expenses.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date|before_or_equal:today',
        ]);
        
        Expense::create([
            'description' => $request->description,
            'amount' => $request->amount,
            'expense_date' => $request->expense_date,
            'user_id' => Auth::id(),
        ]);
        
        return redirect()->route('expenses.index')
            ->with('success', 'Expense recorded successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Expense $expense)
    {
        $expense->load('user');
        return view('expenses.show', compact('expense'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Expense $expense)
    {
        // Only owners can edit expenses
        if (Auth::user()->role !== 'owner') {
            return redirect()->route('expenses.index')
                ->with('error', 'You do not have permission to edit expenses');
        }
        
        return view('expenses.edit', compact('expense'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Expense $expense)
    {
        // Only owners can update expenses
        if (Auth::user()->role !== 'owner') {
            return redirect()->route('expenses.index')
                ->with('error', 'You do not have permission to update expenses');
        }
        
        $request->validate([
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date|before_or_equal:today',
        ]);
        
        $expense->update([
            'description' => $request->description,
            'amount' => $request->amount,
            'expense_date' => $request->expense_date,
        ]);
        
        return redirect()->route('expenses.index')
            ->with('success', 'Expense updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Expense $expense)
    {
        // Only owners can delete expenses
        if (Auth::user()->role !== 'owner') {
            return redirect()->route('expenses.index')
                ->with('error', 'You do not have permission to delete expenses');
        }
        
        $expense->delete();
        return redirect()->route('expenses.index')
            ->with('success', 'Expense deleted successfully');
    }
}
