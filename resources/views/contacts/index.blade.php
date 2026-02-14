@extends('layouts.app')
@section('title', 'Contacts')

@section('content')
<div class="mb-6">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-2xl md:text-4xl font-black tracking-tight dark:text-white">Contacts</h1>
            <p class="text-sm md:text-base text-gray-500 dark:text-gray-400">Manage your people and organizations</p>
        </div>
        <a href="{{ route('contacts.create') }}"
            class="px-6 py-3 bg-primary text-white rounded-xl font-bold shadow-lg hover:opacity-90">
            <i class="ti ti-plus mr-2"></i>New Contact
        </a>
    </div>
</div>

<!-- Filters -->
<div class="bg-white dark:bg-[#25282c] p-4 rounded-xl shadow-sm border border-[#eaeff0] dark:border-gray-700 mb-6">
    <form method="GET" class="flex flex-col md:flex-row gap-3">
        <select name="type" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-800 dark:text-white">
            <option value="">All Types</option>
            <option value="person" {{ request('type') == 'person' ? 'selected' : '' }}>Person</option>
            <option value="organization" {{ request('type') == 'organization' ? 'selected' : '' }}>Organization</option>
        </select>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search contacts..."
            class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-800 dark:text-white" />
        <button type="submit" class="px-6 py-2 bg-primary text-white rounded-lg font-bold">Filter</button>
    </form>
</div>

<!-- Contacts List -->
<div class="bg-white dark:bg-[#25282c] rounded-xl shadow-sm border border-[#eaeff0] dark:border-gray-700">
    @if($contacts->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Contact Info</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-[#25282c] divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($contacts as $contact)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $contact->name }}</div>
                                @if($contact->company)
                                    <div class="text-xs text-gray-500">{{ $contact->company }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $contact->type == 'person' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400' : 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400' }}">
                                    {{ ucfirst($contact->type) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 dark:text-gray-300">
                                    @if($contact->email)
                                        <div><i class="ti ti-mail mr-1"></i>{{ $contact->email }}</div>
                                    @endif
                                    @if($contact->phone)
                                        <div><i class="ti ti-phone mr-1"></i>{{ $contact->phone }}</div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <a href="{{ route('contacts.show', $contact) }}" class="text-blue-600 dark:text-blue-400 hover:underline mr-3">View</a>
                                <a href="{{ route('contacts.edit', $contact) }}" class="text-gray-600 dark:text-gray-400 hover:underline mr-3">Edit</a>
                                <form action="{{ route('contacts.destroy', $contact) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('Delete this contact?')" class="text-red-600 dark:text-red-400 hover:underline">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-4">
            {{ $contacts->links() }}
        </div>
    @else
        <div class="p-12 text-center">
            <i class="ti ti-users text-6xl text-gray-300 dark:text-gray-600 mb-4"></i>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">No contacts yet</h3>
            <p class="text-gray-500 dark:text-gray-400 mb-4">Start by adding your first contact</p>
            <a href="{{ route('contacts.create') }}" class="inline-block px-6 py-3 bg-primary text-white rounded-xl font-bold">
                Add First Contact
            </a>
        </div>
    @endif
</div>
@endsection

