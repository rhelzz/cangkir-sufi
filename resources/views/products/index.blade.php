@extends('layouts.app')

@section('title', 'Products')
@section('page-title', 'Products')

@section('content')
<div class="bg-white rounded-xl shadow-lg overflow-hidden">
    <!-- Header & Add Button -->
    <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-3 p-4 md:p-6 border-b bg-gradient-to-r from-blue-50 to-white">
        <h2 class="text-lg md:text-xl font-bold flex items-center gap-2">
            <i class="fas fa-box text-blue-400"></i>
            Product List
        </h2>
        <a href="{{ route('products.create') }}" class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md shadow transition-colors duration-200 font-semibold">
            <i class="fas fa-plus mr-2"></i> Add Product
        </a>
    </div>

    <div class="p-2 md:p-6">
        <!-- Listing view, js paginated -->
        <div id="productList" class="flex flex-col gap-3"></div>
        <div class="mt-6 flex justify-between items-center" id="productPaginationContainer">
            <div id="productPaginationInfo" class="text-xs text-gray-500"></div>
            <div id="productPagination" class="flex gap-1"></div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const productsData = [
    @foreach($products as $product)
    {
        id: "{{ $product->id }}",
        name: @json($product->name),
        image: @json($product->image ? asset('storage/' . $product->image) : null),
        category: @json($product->category->name),
        price: "{{ 'Rp ' . number_format($product->selling_price, 0, ',', '.') }}",
        stock: {{ $product->stock }},
        show_url: "{{ route('products.show', $product->id) }}",
        edit_url: "{{ route('products.edit', $product->id) }}",
        delete_url: "{{ route('products.destroy', $product->id) }}"
    },
    @endforeach
];
const PAGE_SIZE = 5;
let currentPage = 1;

function renderProductList() {
    const container = document.getElementById('productList');
    container.innerHTML = '';
    const start = (currentPage-1)*PAGE_SIZE, end = start+PAGE_SIZE;
    const pageProducts = productsData.slice(start, end);

    if(pageProducts.length === 0){
        container.innerHTML = '<div class="text-center text-gray-400 italic py-8">No products found</div>';
        renderProductPagination(1,1,0,0);
        return;
    }
    for(const product of pageProducts) {
        let stockBadge = '';
        if(product.stock > 10) {
            stockBadge = `<span class="inline-flex items-center font-medium bg-green-100 text-green-800 rounded-full px-2 py-0.5 ml-2">Stock: ${product.stock}</span>`;
        } else if(product.stock > 0) {
            stockBadge = `<span class="inline-flex items-center font-medium bg-yellow-100 text-yellow-800 rounded-full px-2 py-0.5 ml-2">Stock: ${product.stock}</span>`;
        } else {
            stockBadge = `<span class="inline-flex items-center font-medium bg-red-100 text-red-800 rounded-full px-2 py-0.5 ml-2">Out of stock</span>`;
        }
        container.innerHTML += `
        <div class="group flex items-center justify-between gap-4 p-4 md:p-5 rounded-lg border shadow-sm hover:shadow-md transition bg-gray-50 md:bg-white cursor-pointer hover:bg-blue-50"
            onclick="window.location='${product.show_url}'">
            <div class="flex items-center gap-3 flex-1 min-w-0">
                ${
                    product.image ?
                    `<img class="h-12 w-12 md:h-14 md:w-14 rounded-lg border object-cover bg-white flex-shrink-0" src="${product.image}" alt="${product.name}">`
                    :
                    `<div class="h-12 w-12 md:h-14 md:w-14 rounded-lg bg-gray-200 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-box text-gray-400 text-xl"></i>
                    </div>`
                }
                <div class="flex flex-col min-w-0">
                    <div class="font-semibold text-gray-900 text-base md:text-lg truncate">${product.name}</div>
                    <div class="flex flex-wrap gap-2 text-xs md:text-sm text-gray-500">
                        <span class="inline-flex items-center px-2 py-0.5 rounded bg-blue-50 text-blue-700 font-medium">
                            <i class="fas fa-tag mr-1"></i> ${product.category}
                        </span>
                    </div>
                    <div class="flex gap-3 mt-1 text-xs md:text-sm text-gray-500">
                        <span>Price: <span class="font-bold text-green-700">${product.price}</span></span>
                        ${stockBadge}
                    </div>
                </div>
            </div>
            <div class="flex gap-2 ml-3"
                 onclick="event.stopPropagation();">
                <a href="${product.edit_url}" class="text-yellow-600 hover:bg-yellow-100 hover:text-yellow-800 p-2 rounded" title="Edit">
                    <i class="fas fa-edit"></i>
                </a>
                <form action="${product.delete_url}" method="POST" class="inline js-delete-form" data-id="${product.id}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-600 hover:bg-red-100 hover:text-red-800 p-2 rounded" title="Delete"
                        onclick="event.preventDefault(); if(confirm('Are you sure you want to delete this product?')) this.closest('form').submit();">
                        <i class="fas fa-trash"></i>
                    </button>
                </form>
            </div>
        </div>`;
    }
    renderProductPagination(Math.ceil(productsData.length/PAGE_SIZE), currentPage, start+1, Math.min(end, productsData.length));
}
function renderProductPagination(totalPages, activePage, startItem, endItem) {
    let info = '';
    if(productsData.length > 0)
        info = `Showing <b>${startItem}</b> - <b>${endItem}</b> of <b>${productsData.length}</b>`;
    document.getElementById('productPaginationInfo').innerHTML = info;
    // Pagination
    let html = '';
    if(totalPages > 1) {
        html += `<button ${activePage===1?'disabled':''} onclick="window.goToProductPage(${activePage-1})" class="rounded px-2 py-1 ${activePage===1?'bg-gray-200 text-gray-400':'bg-white text-gray-600 border'}">&lt;</button>`;
        let min = Math.max(1, activePage-2), max = Math.min(totalPages, min+4);
        min = Math.max(1, max-4);
        for(let i=min; i<=max; i++) {
            html += `<button class="rounded px-2 py-1 ${i==activePage ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700'}" onclick="window.goToProductPage(${i})">${i}</button>`;
        }
        html += `<button ${activePage===totalPages?'disabled':''} onclick="window.goToProductPage(${activePage+1})" class="rounded px-2 py-1 ${activePage===totalPages?'bg-gray-200 text-gray-400':'bg-white text-gray-600 border'}">&gt;</button>`;
    }
    document.getElementById('productPagination').innerHTML = html;
}
window.goToProductPage = function(page){
    if(page < 1) return;
    currentPage = page;
    renderProductList();
}

document.addEventListener('DOMContentLoaded', function() {
    renderProductList();
});
</script>
@endpush