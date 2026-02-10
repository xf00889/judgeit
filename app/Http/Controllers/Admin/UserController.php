<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\Category;
use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function __construct(
        private AuditLogService $auditLogService
    ) {
    }

    /**
     * Display a listing of users.
     * 
     * Note: Authorization is handled by route middleware (role:admin)
     */
    public function index(): View
    {
        $users = User::orderBy('name')->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create(): View
    {
        $roles = UserRole::cases();

        return view('admin.users.create', compact('roles'));
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(UserRequest $request): RedirectResponse
    {
        $user = User::create($request->validated());
        
        // Log the creation
        $this->auditLogService->logUserCreated(
            $user->id,
            $user->name,
            $user->email,
            $user->role->value,
            auth()->id()
        );

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User created successfully.');
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user): View
    {
        $roles = UserRole::cases();

        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(UserRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();

        // Track changes for audit log
        $changes = [];
        foreach ($data as $key => $value) {
            if ($key === 'password') {
                if (!empty($value)) {
                    $changes['password'] = ['old' => '[REDACTED]', 'new' => '[REDACTED]'];
                }
            } elseif ($user->$key != $value) {
                $changes[$key] = ['old' => $user->$key, 'new' => $value];
            }
        }

        // Remove password if not provided
        if (empty($data['password'])) {
            unset($data['password']);
        }

        $user->update($data);
        
        // Log the update if there were changes
        if (!empty($changes)) {
            $this->auditLogService->logUserUpdated(
                $user->id,
                $user->name,
                $changes,
                auth()->id()
            );
        }

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user): RedirectResponse
    {
        // Prevent deleting the currently authenticated user
        if ($user->id === auth()->id()) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'You cannot delete your own account.');
        }

        $userName = $user->name;
        $userId = $user->id;
        
        $user->delete();
        
        // Log the deletion
        $this->auditLogService->logUserDeleted(
            $userId,
            $userName,
            auth()->id()
        );

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }

    /**
     * Show the form for assigning categories to a judge.
     */
    public function assignCategories(User $user): View
    {
        // Only allow category assignment for judges
        if (!$user->isJudge()) {
            abort(403, 'Category assignment is only available for judges.');
        }

        $categories = Category::orderBy('order')->orderBy('name')->get();
        $assignedCategoryIds = $user->assignedCategories()->pluck('categories.id')->toArray();

        return view('admin.users.assign-categories', compact('user', 'categories', 'assignedCategoryIds'));
    }

    /**
     * Update the category assignments for a judge.
     */
    public function updateCategoryAssignments(Request $request, User $user): RedirectResponse
    {
        // Only allow category assignment for judges
        if (!$user->isJudge()) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'Category assignment is only available for judges.');
        }

        $request->validate([
            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id',
        ]);

        $categoryIds = $request->input('categories', []);
        
        // Get old assignments for audit log
        $oldCategories = $user->assignedCategories()->pluck('name')->toArray();
        
        // Sync the categories
        $user->assignedCategories()->sync($categoryIds);
        
        // Get new assignments for audit log
        $newCategories = Category::whereIn('id', $categoryIds)->pluck('name')->toArray();
        
        // Log the change
        $this->auditLogService->log(
            'judge_categories_updated',
            'User',
            $user->id,
            [
                'judge_name' => $user->name,
                'old_categories' => $oldCategories,
                'new_categories' => $newCategories,
            ],
            auth()->id()
        );

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Category assignments updated successfully.');
    }

    /**
     * Update the judging gender for a judge.
     */
    public function updateJudgingGender(Request $request, User $user): RedirectResponse
    {
        // Only allow gender assignment for judges
        if (!$user->isJudge()) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'Gender assignment is only available for judges.');
        }

        $request->validate([
            'judging_gender' => 'required|in:male,female,both',
        ]);

        $oldGender = $user->judging_gender;
        $newGender = $request->input('judging_gender');
        
        $user->update(['judging_gender' => $newGender]);
        
        // Log the change
        $this->auditLogService->log(
            'judge_gender_updated',
            'User',
            $user->id,
            [
                'judge_name' => $user->name,
                'old_gender' => $oldGender ?? 'not set',
                'new_gender' => $newGender,
            ],
            auth()->id()
        );

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Judging gender updated successfully.');
    }
}
