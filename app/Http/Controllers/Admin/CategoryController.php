<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use App\Services\CategoryServiceInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CategoryController extends Controller
{
    /**
     * Create a new controller instance.
     * 
     * Note: Authorization is handled by route middleware (role:admin)
     */
    public function __construct(
        private CategoryServiceInterface $categoryService,
        private \App\Services\AuditLogService $auditLogService
    ) {
    }

    /**
     * Display a listing of categories.
     */
    public function index(): View
    {
        $categories = Category::orderBy('order')->get();

        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new category.
     */
    public function create(): View
    {
        return view('admin.categories.create');
    }

    /**
     * Store a newly created category in storage.
     */
    public function store(CategoryRequest $request): RedirectResponse
    {
        try {
            $validated = $request->validated();
            \Log::info('Category store - validated data', ['data' => $validated]);
            
            $criteria = $validated['criteria'] ?? [];
            unset($validated['criteria']);

            $category = $this->categoryService->createCategory($validated);
            \Log::info('Category created', ['id' => $category->id, 'name' => $category->name]);

            // Create criteria for the category
            foreach ($criteria as $criterionData) {
                \Log::info('Creating criterion', ['data' => $criterionData]);
                $category->criteria()->create([
                    'name' => $criterionData['name'],
                    'percentage' => $criterionData['percentage'],
                    'max_score' => $criterionData['max_score'] ?? 10,
                ]);
            }
            
            // Log the creation
            $this->auditLogService->logCategoryCreated(
                $category->id,
                $category->name,
                auth()->id()
            );

            return redirect()
                ->route('admin.categories.index')
                ->with('success', 'Category created successfully.');
        } catch (\Exception $e) {
            \Log::error('Category store error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error creating category: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified category.
     */
    public function edit(Category $category): View
    {
        return view('admin.categories.edit', compact('category'));
    }

    /**
     * Update the specified category in storage.
     */
    public function update(CategoryRequest $request, Category $category): RedirectResponse
    {
        try {
            $validated = $request->validated();
            $criteria = $validated['criteria'];
            unset($validated['criteria']);
            
            // Track changes for audit log
            $changes = [];
            foreach ($validated as $key => $value) {
                if ($category->$key != $value) {
                    $changes[$key] = ['old' => $category->$key, 'new' => $value];
                }
            }
            
            $this->categoryService->updateCategory($category, $validated);
            
            // Update criteria
            $category->criteria()->delete();
            foreach ($criteria as $criterionData) {
                $category->criteria()->create([
                    'name' => $criterionData['name'],
                    'percentage' => $criterionData['percentage'],
                    'max_score' => $criterionData['max_score'] ?? 10,
                ]);
            }
            
            // Log the update if there were changes
            if (!empty($changes)) {
                $this->auditLogService->logCategoryUpdated(
                    $category->id,
                    $category->name,
                    $changes,
                    auth()->id()
                );
            }

            return redirect()
                ->route('admin.categories.index')
                ->with('success', 'Category updated successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Activate the specified category.
     */
    public function activate(Category $category): RedirectResponse
    {
        try {
            $this->categoryService->activateCategory($category);

            return redirect()
                ->route('admin.categories.index')
                ->with('success', 'Category activated successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.categories.index')
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Deactivate the specified category.
     */
    public function deactivate(Category $category): RedirectResponse
    {
        try {
            $this->categoryService->deactivateCategory($category);

            return redirect()
                ->route('admin.categories.index')
                ->with('success', 'Category deactivated successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.categories.index')
                ->with('error', $e->getMessage());
        }
    }
}
