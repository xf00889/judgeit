<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContestantRequest;
use App\Models\Contestant;
use App\Repositories\ContestantRepositoryInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ContestantController extends Controller
{
    /**
     * Create a new controller instance.
     * 
     * Note: Authorization is handled by route middleware (role:admin)
     */
    public function __construct(
        private ContestantRepositoryInterface $contestantRepository,
        private \App\Services\AuditLogService $auditLogService
    ) {
    }

    /**
     * Display a listing of contestants.
     */
    public function index(): View
    {
        $contestants = Contestant::orderBy('contestant_number')->get();
        
        // Group contestants by gender (normalized to title case)
        $contestantsByGender = $contestants->groupBy(function ($contestant) {
            return $contestant->gender ? ucfirst(strtolower($contestant->gender)) : 'Not Specified';
        });

        return view('admin.contestants.index', compact('contestantsByGender'));
    }

    /**
     * Show the form for creating a new contestant.
     */
    public function create(): View
    {
        return view('admin.contestants.create');
    }

    /**
     * Store a newly created contestant in storage.
     */
    public function store(ContestantRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $request->has('is_active') ? $request->boolean('is_active') : true;

        $contestant = Contestant::create($data);
        
        // Log the creation
        $this->auditLogService->logContestantCreated(
            $contestant->id,
            $contestant->name,
            $contestant->contestant_number,
            auth()->id()
        );

        return redirect()
            ->route('admin.contestants.index')
            ->with('success', 'Contestant created successfully.');
    }

    /**
     * Show the form for editing the specified contestant.
     */
    public function edit(Contestant $contestant): View
    {
        return view('admin.contestants.edit', compact('contestant'));
    }

    /**
     * Update the specified contestant in storage.
     */
    public function update(ContestantRequest $request, Contestant $contestant): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $request->has('is_active') ? $request->boolean('is_active') : false;

        // Track changes for audit log
        $changes = [];
        foreach ($data as $key => $value) {
            if ($contestant->$key != $value) {
                $changes[$key] = ['old' => $contestant->$key, 'new' => $value];
            }
        }

        $contestant->update($data);
        
        // Log the update if there were changes
        if (!empty($changes)) {
            $this->auditLogService->logContestantUpdated(
                $contestant->id,
                $contestant->name,
                $changes,
                auth()->id()
            );
        }

        return redirect()
            ->route('admin.contestants.index')
            ->with('success', 'Contestant updated successfully.');
    }

    /**
     * Remove the specified contestant from storage.
     */
    public function destroy(Contestant $contestant): RedirectResponse
    {
        // Check if contestant can be deleted (no scores)
        if (!$this->contestantRepository->canDelete($contestant)) {
            return redirect()
                ->route('admin.contestants.index')
                ->with('error', 'Cannot delete contestant who has received scores.');
        }

        $contestantName = $contestant->name;
        $contestantId = $contestant->id;
        
        $contestant->delete();
        
        // Log the deletion
        $this->auditLogService->logContestantDeleted(
            $contestantId,
            $contestantName,
            auth()->id()
        );

        return redirect()
            ->route('admin.contestants.index')
            ->with('success', 'Contestant deleted successfully.');
    }
}
