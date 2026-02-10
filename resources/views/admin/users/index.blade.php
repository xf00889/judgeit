@extends('layouts.app')

@section('content')
<div class="bg-white shadow-md rounded-lg overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-800">User Management</h2>
        <a href="{{ route('admin.users.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            Create New User
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Name
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Email
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Role
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Judging Gender
                    </th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($users as $user)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-500">{{ $user->email }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                @if($user->role->value === 'admin') bg-purple-100 text-purple-800
                                @elseif($user->role->value === 'judge') bg-blue-100 text-blue-800
                                @else bg-green-100 text-green-800
                                @endif">
                                {{ ucfirst($user->role->value) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($user->role->value === 'judge')
                                @if($user->judging_gender)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        @if($user->judging_gender === 'male') bg-blue-100 text-blue-800
                                        @elseif($user->judging_gender === 'female') bg-pink-100 text-pink-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        {{ ucfirst($user->judging_gender) }}
                                    </span>
                                @else
                                    <span class="text-sm text-gray-400 italic">Not set</span>
                                @endif
                            @else
                                <span class="text-sm text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('admin.users.edit', $user) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">
                                Edit
                            </a>
                            @if($user->role->value === 'judge')
                                <a href="{{ route('admin.users.assign-categories', $user) }}" class="text-green-600 hover:text-green-900 mr-3">
                                    Assign Categories
                                </a>
                                <button onclick="openGenderModal({{ $user->id }}, '{{ $user->name }}', '{{ $user->judging_gender ?? 'both' }}')" class="text-purple-600 hover:text-purple-900 mr-3">
                                    Assign Gender
                                </button>
                            @endif
                            @if($user->id !== auth()->id())
                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to delete this user?')">
                                        Delete
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                            No users found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="px-6 py-4 border-t border-gray-200">
        {{ $users->links() }}
    </div>
</div>

<!-- Gender Assignment Modal -->
<div id="genderModal" class="hidden fixed inset-0 z-[9999]" style="background-color: rgba(0, 0, 0, 0.6); backdrop-filter: blur(2px);">
    <div class="flex items-center justify-center min-h-screen p-4" onclick="event.target === this && closeGenderModal()">
        <div class="relative bg-white rounded-2xl shadow-2xl max-w-md w-full" onclick="event.stopPropagation()">
            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-purple-600 to-indigo-600 px-6 py-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-bold text-white">Assign Judging Gender</h3>
                    <button type="button" onclick="closeGenderModal()" 
                            class="text-white hover:text-gray-200 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Modal Body -->
            <div class="p-6">
                <p class="text-sm text-gray-600 mb-4">
                    Judge: <span id="judgeName" class="font-semibold text-gray-900"></span>
                </p>
                
                <form id="genderForm" method="POST">
                    @csrf
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-3">Select Gender to Judge:</label>
                        <div class="space-y-3">
                            <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors">
                                <input type="radio" name="judging_gender" value="male" class="mr-3 h-4 w-4 text-blue-600">
                                <span class="text-gray-700">Male Contestants Only</span>
                            </label>
                            <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors">
                                <input type="radio" name="judging_gender" value="female" class="mr-3 h-4 w-4 text-pink-600">
                                <span class="text-gray-700">Female Contestants Only</span>
                            </label>
                            <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors">
                                <input type="radio" name="judging_gender" value="both" class="mr-3 h-4 w-4 text-gray-600">
                                <span class="text-gray-700">Both Male and Female Contestants</span>
                            </label>
                        </div>
                    </div>
                    
                    <!-- Modal Footer -->
                    <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                        <button type="button" onclick="closeGenderModal()" 
                                class="px-6 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium rounded-lg transition-all duration-200">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="px-6 py-2.5 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-lg transition-all duration-200">
                            Save
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function openGenderModal(userId, userName, currentGender) {
    document.getElementById('judgeName').textContent = userName;
    document.getElementById('genderForm').action = `/admin/users/${userId}/judging-gender`;
    
    // Set the current gender selection
    const radioButtons = document.querySelectorAll('input[name="judging_gender"]');
    radioButtons.forEach(radio => {
        if (radio.value === currentGender) {
            radio.checked = true;
        }
    });
    
    const modal = document.getElementById('genderModal');
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeGenderModal() {
    const modal = document.getElementById('genderModal');
    modal.classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// Close modal when clicking outside or pressing Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeGenderModal();
    }
});
</script>
@endsection
