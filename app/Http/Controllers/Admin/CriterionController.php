<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CriterionRequest;
use App\Models\Criterion;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CriterionController extends Controller
{
    /**
     * Display a listing of criteria for a category.
     * 
     * Note: Authorization is handled by route middleware (role:admin)
     */
    public function index(Category $category): View
    {
        $criteria = $category->criteria()->orderBy('id')->get();

        return view('admin.criteria.index', compact('category', 'criteria'));
    }

    /**
     * Show the form for creating a new criterion.
     */
    public function create(Category $category): View
    {
        // Prevent adding criteria to active/locked/finalized categories
        if ($category->isActive() || $category->isLocked() || $category->isFinalized()) {
            return redirect()
                ->route('admin.criteria.index', $category)
                ->with('error', 'Cannot add criteria to an active, locked, or finalized category.');
        }

        return view('admin.criteria.create', compact('category'));
    }

    /**
     * Store a newly created criterion in storage.
     */
    public function store(CriterionRequest $request, Category $category): RedirectResponse
    {
        // Prevent adding criteria to active/locked/finalized categories
        if ($category->isActive() || $category->isLocked() || $category->isFinalized()) {
            return redirect()
                ->route('admin.criteria.index', $category)
                ->with('error', 'Cannot add criteria to an active, locked, or finalized category.');
        }

        $data = $request->validated();
        $data['category_id'] = $category->id;

        Criterion::create($data);

        return redirect()
            ->route('admin.criteria.index', $category)
            ->with('success', 'Criterion created successfully.');
    }

    /**
     * Show the form for editing the specified criterion.
     */
    public function edit(Category $category, Criterion $criterion): View
    {
        // Prevent editing criteria in active/locked/finalized categories
        if ($category->isActive() || $category->isLocked() || $category->isFinalized()) {
            return redirect()
                ->route('admin.criteria.index', $category)
                ->with('error', 'Cannot edit criteria in an active, locked, or finalized category.');
        }

        return view('admin.criteria.edit', compact('category', 'criterion'));
    }

    /**
     * Update the specified criterion in storage.
     */
    public function update(CriterionRequest $request, Category $category, Criterion $criterion): RedirectResponse
    {
        // Prevent editing criteria in active/locked/finalized categories
        if ($category->isActive() || $category->isLocked() || $category->isFinalized()) {
            return redirect()
                ->route('admin.criteria.index', $category)
                ->with('error', 'Cannot edit criteria in an active, locked, or finalized category.');
        }

        $criterion->update($request->validated());

        return redirect()
            ->route('admin.criteria.index', $category)
            ->with('success', 'Criterion updated successfully.');
    }

    /**
     * Remove the specified criterion from storage.
     */
    public function destroy(Category $category, Criterion $criterion): RedirectResponse
    {
        // Prevent deleting criteria from active/locked/finalized categories
        if ($category->isActive() || $category->isLocked() || $category->isFinalized()) {
            return redirect()
                ->route('admin.criteria.index', $category)
                ->with('error', 'Cannot delete criteria from an active, locked, or finalized category.');
        }

        $criterion->delete();

        return redirect()
            ->route('admin.criteria.index', $category)
            ->with('success', 'Criterion deleted successfully.');
    }
}
