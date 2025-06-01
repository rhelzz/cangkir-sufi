<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Expense;
use App\Models\ExpenseItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $dateFrom = $request->date_from ? Carbon::parse($request->date_from) : null;
        $dateTo = $request->date_to ? Carbon::parse($request->date_to) : null;
        $isRange = $dateFrom && $dateTo;

        if ($isRange) {
            $expenses = Expense::whereBetween('expense_date', [$dateFrom, $dateTo])
                ->with(['user', 'items'])
                ->orderBy('id', 'asc')
                ->paginate(5)
                ->withQueryString();

            $totalAmount = ExpenseItem::whereHas('expense', function($query) use ($dateFrom, $dateTo) {
                $query->whereBetween('expense_date', [$dateFrom, $dateTo]);
            })->sum('price');
            $date = null;
        } else {
            $date = $request->date ? Carbon::parse($request->date) : Carbon::today();
            $expenses = Expense::whereDate('expense_date', $date)
                ->with(['user', 'items'])
                ->orderBy('id', 'asc')
                ->paginate(5)
                ->withQueryString();

            $totalAmount = ExpenseItem::whereHas('expense', function($query) use ($date) {
                $query->whereDate('expense_date', $date);
            })->sum('price');
            $dateFrom = null;
            $dateTo = null;
        }

        return view('expenses.index', [
            'expenses'    => $expenses,
            'date'        => $date ?? null,
            'dateFrom'    => $dateFrom,
            'dateTo'      => $dateTo,
            'isRange'     => $isRange,
            'totalAmount' => $totalAmount
        ]);
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
            'expense_date' => 'required|date|before_or_equal:today',
            'items' => 'required|array|min:1',
            'items.*.item_name' => 'required|string|max:255',
            'items.*.price' => 'required|numeric|min:0',
        ]);
        
        DB::beginTransaction();
        try {
            $expense = Expense::create([
                'description' => $request->description,
                'expense_date' => $request->expense_date,
                'user_id' => Auth::id(),
            ]);
            
            foreach ($request->items as $item) {
                ExpenseItem::create([
                    'expense_id' => $expense->id,
                    'item_name' => $item['item_name'],
                    'price' => $item['price'],
                ]);
            }
            
            DB::commit();
            
            return redirect()->route('expenses.index')
                ->with('success', 'Expense recorded successfully');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Failed to record expense: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Expense $expense)
    {
        $expense->load(['user', 'items']);
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
        
        $expense->load('items');
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
            'expense_date' => 'required|date|before_or_equal:today',
            'items' => 'required|array|min:1',
            'items.*.id' => 'nullable|exists:expense_items,id',
            'items.*.item_name' => 'required|string|max:255',
            'items.*.price' => 'required|numeric|min:0',
        ]);
        
        DB::beginTransaction();
        try {
            $expense->update([
                'description' => $request->description,
                'expense_date' => $request->expense_date,
            ]);
            
            // Get current item IDs
            $currentItemIds = $expense->items->pluck('id')->toArray();
            $newItemIds = [];
            
            foreach ($request->items as $item) {
                if (isset($item['id'])) {
                    // Update existing item
                    $expenseItem = ExpenseItem::find($item['id']);
                    $expenseItem->update([
                        'item_name' => $item['item_name'],
                        'price' => $item['price'],
                    ]);
                    $newItemIds[] = $item['id'];
                } else {
                    // Create new item
                    $newItem = ExpenseItem::create([
                        'expense_id' => $expense->id,
                        'item_name' => $item['item_name'],
                        'price' => $item['price'],
                    ]);
                    $newItemIds[] = $newItem->id;
                }
            }
            
            // Delete items that are no longer present
            $itemsToDelete = array_diff($currentItemIds, $newItemIds);
            if (!empty($itemsToDelete)) {
                ExpenseItem::whereIn('id', $itemsToDelete)->delete();
            }
            
            DB::commit();
            
            return redirect()->route('expenses.index')
                ->with('success', 'Expense updated successfully');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Failed to update expense: ' . $e->getMessage())
                ->withInput();
        }
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
        
        // No need to delete items explicitly due to the cascade constraint
        $expense->delete();
        return redirect()->route('expenses.index')
            ->with('success', 'Expense deleted successfully');
    }
}
