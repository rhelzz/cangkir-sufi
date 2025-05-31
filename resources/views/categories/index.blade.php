@extends('layouts.app')

@section('title', 'Categories')
@section('page-title', 'Categories')

@section('content')
<div class="bg-white rounded-xl shadow-lg overflow-hidden">
    <!-- Header & Add Button -->
    <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-3 p-4 md:p-6 border-b bg-gradient-to-r from-blue-50 to-white">
        <h2 class="text-lg md:text-xl font-bold flex items-center gap-2">
            <i class="fas fa-layer-group text-blue-400"></i>
            Category List
        </h2>
        <a href="{{ route('categories.create') }}" class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md shadow transition-colors duration-200 font-semibold">
            <i class="fas fa-plus mr-2"></i> Add Category
        </a>
    </div>

    <div class="p-2 md:p-6">
        <div id="categoryList" class="flex flex-col gap-3 md:gap-4"></div>
        <div class="mt-6 flex justify-between items-center" id="categoryPaginationContainer">
            <div id="categoryPaginationInfo" class="text-xs text-gray-500"></div>
            <div id="categoryPagination" class="flex gap-1"></div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const categoriesData = [
    @foreach($categories as $category)
    {
        id: "{{ $category->id }}",
        name: @json($category->name),
        type: "{{ $category->type }}",
        products_count: {{ $category->products_count }},
        show_url: "{{ route('categories.show', $category->id) }}",
        edit_url: "{{ route('categories.edit', $category->id) }}",
        delete_url: "{{ route('categories.destroy', $category->id) }}"
    },
    @endforeach
];
const PAGE_SIZE = 5;
let currentPage = 1;

function renderCategoryList() {
    const container = document.getElementById('categoryList');
    container.innerHTML = '';
    const start = (currentPage-1)*PAGE_SIZE, end = start+PAGE_SIZE;
    const pageCategories = categoriesData.slice(start, end);

    if(pageCategories.length === 0){
        container.innerHTML = '<div class="text-center text-gray-400 italic py-8">No categories found</div>';
        renderCategoryPagination(1,1,0,0);
        return;
    }
    for(const category of pageCategories) {
        let typeBadge = '';
        if(category.type === 'hot') {
            typeBadge = `<span class="inline-flex items-center px-2 py-0.5 rounded bg-red-100 text-red-800 font-medium gap-1"><i class="fas fa-mug-hot"></i> Hot</span>`;
        } else {
            typeBadge = `<span class="inline-flex items-center px-2 py-0.5 rounded bg-blue-100 text-blue-800 font-medium gap-1"><i class="fas fa-ice-cream"></i> Cold</span>`;
        }
        container.innerHTML += `
        <div class="group flex items-center justify-between gap-4 p-4 md:p-5 rounded-lg border shadow-sm hover:shadow-md transition bg-gray-50 md:bg-white">
            <div class="flex items-center gap-4 flex-1 min-w-0">
                <div class="flex flex-col min-w-0">
                    <div class="font-semibold text-gray-900 text-base md:text-lg truncate">${category.name}</div>
                    <div class="flex flex-wrap gap-2 text-xs md:text-sm mt-1">
                        ${typeBadge}
                        <span class="inline-flex items-center px-2 py-0.5 rounded bg-gray-100 text-gray-700 font-medium gap-1">
                            <i class="fas fa-boxes-stacked"></i>
                            ${category.products_count} Product${category.products_count == 1 ? '' : 's'}
                        </span>
                    </div>
                </div>
            </div>
            <div class="flex gap-2 ml-3"
                 onclick="event.stopPropagation();">
                <a href="${category.show_url}" class="text-blue-600 hover:bg-blue-100 hover:text-blue-800 p-2 rounded" title="View">
                    <i class="fas fa-eye"></i>
                </a>
                <a href="${category.edit_url}" class="text-yellow-600 hover:bg-yellow-100 hover:text-yellow-800 p-2 rounded" title="Edit">
                    <i class="fas fa-edit"></i>
                </a>
                <form action="${category.delete_url}" method="POST" class="inline js-delete-form" data-id="${category.id}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-600 hover:bg-red-100 hover:text-red-800 p-2 rounded" title="Delete"
                        onclick="event.preventDefault(); if(confirm('Are you sure you want to delete this category?')) this.closest('form').submit();">
                        <i class="fas fa-trash"></i>
                    </button>
                </form>
            </div>
        </div>`;
    }
    renderCategoryPagination(Math.ceil(categoriesData.length/PAGE_SIZE), currentPage, start+1, Math.min(end, categoriesData.length));
}
function renderCategoryPagination(totalPages, activePage, startItem, endItem) {
    let info = '';
    if(categoriesData.length > 0)
        info = `Showing <b>${startItem}</b> - <b>${endItem}</b> of <b>${categoriesData.length}</b>`;
    document.getElementById('categoryPaginationInfo').innerHTML = info;
    // Pagination
    let html = '';
    if(totalPages > 1) {
        html += `<button ${activePage===1?'disabled':''} onclick="window.goToCategoryPage(${activePage-1})" class="rounded px-2 py-1 ${activePage===1?'bg-gray-200 text-gray-400':'bg-white text-gray-600 border'}">&lt;</button>`;
        let min = Math.max(1, activePage-2), max = Math.min(totalPages, min+4);
        min = Math.max(1, max-4);
        for(let i=min; i<=max; i++) {
            html += `<button class="rounded px-2 py-1 ${i==activePage ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700'}" onclick="window.goToCategoryPage(${i})">${i}</button>`;
        }
        html += `<button ${activePage===totalPages?'disabled':''} onclick="window.goToCategoryPage(${activePage+1})" class="rounded px-2 py-1 ${activePage===totalPages?'bg-gray-200 text-gray-400':'bg-white text-gray-600 border'}">&gt;</button>`;
    }
    document.getElementById('categoryPagination').innerHTML = html;
}
window.goToCategoryPage = function(page){
    if(page < 1) return;
    currentPage = page;
    renderCategoryList();
}

document.addEventListener('DOMContentLoaded', function() {
    renderCategoryList();
});
</script>
@endpush