@extends('layouts.master')

@section('content')
    <div class="container mx-auto py-8">
        <h1 class="text-3xl font-bold mb-4">Latest News</h1>

        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Error!</strong>
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        <form action="{{ route('/') }}" method="GET" class="mb-4">
            <div class="flex items-center mb-2">
                <input type="text" name="search" placeholder="Search by keyword..." value="{{ request('search') }}"
                    class="rounded-l px-4 py-2 w-1/3 border-t border-b border-l text-gray-800 border-gray-200 bg-white focus:outline-none">
                <button type="submit"
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-r focus:outline-none">Search</button>
            </div>
        </form>
        <!-- Filter Form -->
        <form action="{{ route('news.index') }}" method="GET" class="mb-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-gray-700 font-bold mb-2">Source:</label>
                    <select name="source"
                        class="rounded px-4 py-2 w-full border border-gray-200 bg-white focus:outline-none">
                        <option value="">All Sources</option>
                        <!-- Populate options dynamically from sources -->
                        @foreach ($sources as $source)
                            <option value="{{ $source['id'] }}" {{ request('source') == $source['id'] ? 'selected' : '' }}>
                                {{ $source['name'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <button type="submit"
                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 mt-4 rounded focus:outline-none">Apply
                Filters</button>
        </form>

        <!-- Sorting Links -->
        <div class="mb-4">
            <span class="mr-2 font-bold">Sort By:</span>
            <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'publishedAt']) }}"
                class="mr-2 {{ request('sort_by') == 'publishedAt' ? 'text-blue-500' : '' }}">Published At</a>
            <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'title']) }}"
                class="mr-2 {{ request('sort_by') == 'title' ? 'text-blue-500' : '' }}">Title</a>
            <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'source']) }}"
                class="mr-2 {{ request('sort_by') == 'source' ? 'text-blue-500' : '' }}">Source</a>
            <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'author']) }}"
                class="{{ request('sort_by') == 'author' ? 'text-blue-500' : '' }}">Author</a>
        </div>

        <!-- Column Visibility Controls -->
        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">Visible Columns:</label>
            <div class="space-x-4">
                <label class="inline-flex items-center">
                    <input type="checkbox" class="form-checkbox h-5 w-5 text-blue-500" data-column="title" checked> <span
                        class="ml-2">Title</span>
                </label>
                <label class="inline-flex items-center">
                    <input type="checkbox" class="form-checkbox h-5 w-5 text-blue-500" data-column="source" checked> <span
                        class="ml-2">Source</span>
                </label>
                <label class="inline-flex items-center">
                    <input type="checkbox" class="form-checkbox h-5 w-5 text-blue-500" data-column="publishedAt" checked>
                    <span class="ml-2">Published At</span>
                </label>
                <label class="inline-flex items-center">
                    <input type="checkbox" class="form-checkbox h-5 w-5 text-blue-500" data-column="author" checked> <span
                        class="ml-2">Author</span>
                </label>
                <label class="inline-flex items-center">
                    <input type="checkbox" class="form-checkbox h-5 w-5 text-blue-500" data-column="description" checked>
                    <span class="ml-2">Description</span>
                </label>
            </div>
        </div>

        <table id="articlesTable" class="min-w-full divide-y divide-gray-200">
            <thead>
                <tr class="bg-gray-50">
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                        data-column="title">Title</th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                        data-column="source">Source</th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                        data-column="publishedAt">Published At</th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                        data-column="author">Author</th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                        data-column="description">Description</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($articles as $article)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap" data-column="title">{{ $article['title'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap" data-column="source">{{ $article['source']['name'] }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap" data-column="publishedAt">
                            {{ \Carbon\Carbon::parse($article['publishedAt'])->toFormattedDateString() }}</td>
                        <td class="px-6 py-4 whitespace-nowrap" data-column="author">{{ $article['author'] }}</td>
                        <td class="px-6 py-4 whitespace-wrap" data-column="description">{{ $article['description'] }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center">No articles found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{ $articles->appends(request()->input())->links() }}
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const checkboxes = document.querySelectorAll('.form-checkbox');

            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const column = this.dataset.column;
                    const cells = document.querySelectorAll(`[data-column="${column}"]`);

                    cells.forEach(cell => {
                        cell.style.display = this.checked ? '' : 'none';
                    });
                });
            });
        });
    </script>
@endsection
