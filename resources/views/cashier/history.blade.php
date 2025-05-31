@extends('layouts.app')

@section('title', 'Order History')
@section('page-title', 'Order History')

@section('content')
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <div class="p-4 border-b flex flex-col gap-3">
        <h2 class="text-lg font-bold">Transaction History</h2>
        <div class="flex flex-col md:flex-row md:items-center md:gap-4 gap-2">
            <!-- From - To in one row -->
            <div class="flex items-center gap-2">
                <div>
                    <label for="filterDateFrom" class="text-xs text-gray-500">From</label>
                    <input type="date" id="filterDateFrom" class="border border-gray-300 rounded px-2 py-1 text-xs focus:ring-2 focus:ring-primary-200 w-auto">
                </div>
                <span class="text-gray-400 text-xs">-</span>
                <div>
                    <label for="filterDateTo" class="text-xs text-gray-500">To</label>
                    <input type="date" id="filterDateTo" class="border border-gray-300 rounded px-2 py-1 text-xs focus:ring-2 focus:ring-primary-200 w-auto">
                </div>
            </div>
            <!-- Search full width on mobile, auto on desktop -->
            <div class="flex-1">
                <label for="orderSearch" class="sr-only">Search</label>
                <input type="text" id="orderSearch" class="border border-gray-300 rounded px-2 py-1 text-xs w-full focus:ring-2 focus:ring-primary-200" placeholder="🔍 Search order/cashier...">
            </div>
        </div>
    </div>
    <div class="p-0 md:p-4">
        <!-- Mobile List -->
        <div id="mobileOrderList" class="md:hidden divide-y"></div>
        <!-- Desktop Table -->
        <div class="hidden md:block overflow-x-auto">
            <table id="orderTable" class="min-w-full divide-y divide-gray-200 text-sm">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-4 py-2 text-left font-semibold text-gray-500 uppercase">Order #</th>
                        <th class="px-4 py-2 text-left font-semibold text-gray-500 uppercase">Date</th>
                        <th class="px-4 py-2 text-left font-semibold text-gray-500 uppercase">Total</th>
                        <th class="px-4 py-2 text-left font-semibold text-gray-500 uppercase">Payment</th>
                        <th class="px-4 py-2 text-left font-semibold text-gray-500 uppercase">Cashier</th>
                        <th class="px-4 py-2 text-left font-semibold text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100" id="desktopOrderTableBody"></tbody>
            </table>
            <div class="pt-6 flex justify-between items-center" id="desktopPaginationContainer">
                <div id="desktopPaginationInfo" class="text-xs text-gray-500"></div>
                <div id="desktopPagination" class="flex gap-1"></div>
            </div>
        </div>
    </div>
    <!-- Mobile Pagination -->
    <div class="md:hidden px-3 pt-4 pb-3 flex justify-between items-center" id="mobilePaginationContainer">
        <div id="mobilePaginationInfo" class="text-xs text-gray-500"></div>
        <div id="mobilePagination" class="flex gap-1"></div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Prepare orders data from backend for JS pagination & filtering
const ordersData = [
    @foreach($orders as $order)
    {
        id: "{{ $order->id }}",
        order_number: "{{ $order->order_number }}",
        date: "{{ $order->created_at->format('Y-m-d') }}",
        date_display: "{{ $order->created_at->format('d M Y') }}",
        time_display: "{{ $order->created_at->format('H:i') }}",
        total: "{{ 'Rp ' . number_format($order->final_amount,0,',','.') }}",
        payment_method: "{{ ucfirst($order->payment_method) }}",
        payment_class: "{{ $order->payment_method === 'cash' ? 'bg-green-100 text-green-700' : ($order->payment_method === 'card' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700') }}",
        cashier: "{{ $order->user->name }}",
        cashier_lc: "{{ strtolower($order->user->name) }}",
        order_number_lc: "{{ strtolower($order->order_number) }}",
        view_url: "{{ route('cashier.view-order', $order->id) }}"
    },
    @endforeach
];
const PAGE_SIZE = 5;
let currentPage = 1, filteredOrders = ordersData;

function renderMobileOrders() {
    const container = document.getElementById('mobileOrderList');
    if (!container) return;
    container.innerHTML = '';
    const start = (currentPage-1)*PAGE_SIZE, end = start+PAGE_SIZE;
    const pageOrders = filteredOrders.slice(start, end);

    if(pageOrders.length === 0){
        container.innerHTML = '<div class="text-center text-gray-400 italic py-8">No orders found</div>';
        renderMobilePagination(1,1,0,0);
        return;
    }
    for(const [i, order] of pageOrders.entries()) {
        container.innerHTML += `
        <div class="flex items-center justify-between px-4 py-3">
            <div>
                <div class="font-bold text-blue-700 text-sm">${order.order_number}</div>
                <div class="text-xs text-gray-500">${order.date_display} ${order.time_display}</div>
                <div class="text-xs mt-1 text-gray-700">
                    <span class="font-semibold">${order.total}</span>
                    <span class="mx-1">•</span>
                    <span class="text-gray-500">${order.cashier}</span>
                </div>
            </div>
            <div class="flex flex-col items-end">
                <a href="${order.view_url}" class="text-blue-600 hover:underline text-xs font-semibold flex items-center gap-1">
                    <i class="fas fa-eye"></i> View
                </a>
                <span class="mt-2 px-2 py-0.5 rounded-full text-xs font-semibold ${order.payment_class}">
                    ${order.payment_method}
                </span>
            </div>
        </div>`;
    }
    renderMobilePagination(Math.ceil(filteredOrders.length/PAGE_SIZE), currentPage, start+1, Math.min(end, filteredOrders.length));
}
function renderMobilePagination(totalPages, activePage, startItem, endItem) {
    let html = '';
    // Info
    let info = '';
    if(filteredOrders.length > 0)
        info = `Showing <b>${startItem}</b> - <b>${endItem}</b> of <b>${filteredOrders.length}</b>`;
    document.getElementById('mobilePaginationInfo').innerHTML = info;
    // Pagination
    if(totalPages > 1) {
        html += `<button ${activePage===1?'disabled':''} onclick="window.goToMobilePage(${activePage-1})" class="rounded px-2 py-1 ${activePage===1?'bg-gray-200 text-gray-400':'bg-white text-gray-600 border'}">&lt;</button>`;
        // Show max 5 page numbers
        let min = Math.max(1, activePage-2), max = Math.min(totalPages, min+4);
        min = Math.max(1, max-4);
        for(let i=min; i<=max; i++) {
            html += `<button class="rounded px-2 py-1 ${i==activePage ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700'}" onclick="window.goToMobilePage(${i})">${i}</button>`;
        }
        html += `<button ${activePage===totalPages?'disabled':''} onclick="window.goToMobilePage(${activePage+1})" class="rounded px-2 py-1 ${activePage===totalPages?'bg-gray-200 text-gray-400':'bg-white text-gray-600 border'}">&gt;</button>`;
    }
    document.getElementById('mobilePagination').innerHTML = html;
}
window.goToMobilePage = function(page){
    if(page < 1) return;
    currentPage = page;
    renderMobileOrders();
}

function renderDesktopTable() {
    const tbody = document.getElementById('desktopOrderTableBody');
    const start = (currentPage-1)*PAGE_SIZE, end = start+PAGE_SIZE;
    const pageOrders = filteredOrders.slice(start, end);
    tbody.innerHTML = '';
    if(pageOrders.length === 0){
        tbody.innerHTML = `<tr><td colspan="6" class="px-4 py-8 text-center text-gray-400 italic">No orders found</td></tr>`;
        renderDesktopPagination(1,1,0,0);
        return;
    }
    for(const order of pageOrders) {
        tbody.innerHTML += `
        <tr>
            <td class="px-4 py-2 font-bold text-blue-700">${order.order_number}</td>
            <td class="px-4 py-2 text-gray-700">${order.date_display} ${order.time_display}</td>
            <td class="px-4 py-2 font-semibold text-gray-900">${order.total}</td>
            <td class="px-4 py-2"><span class="px-2 py-0.5 rounded-full text-xs font-semibold ${order.payment_class}">${order.payment_method}</span></td>
            <td class="px-4 py-2 text-gray-700">${order.cashier}</td>
            <td class="px-4 py-2"><a href="${order.view_url}" class="text-blue-600 hover:underline flex items-center gap-1"><i class="fas fa-eye"></i> View</a></td>
        </tr>`;
    }
    renderDesktopPagination(Math.ceil(filteredOrders.length/PAGE_SIZE), currentPage, start+1, Math.min(end, filteredOrders.length));
}
function renderDesktopPagination(totalPages, activePage, startItem, endItem) {
    // Info
    let info = '';
    if(filteredOrders.length > 0)
        info = `Showing <b>${startItem}</b> - <b>${endItem}</b> of <b>${filteredOrders.length}</b>`;
    document.getElementById('desktopPaginationInfo').innerHTML = info;
    // Pagination
    let html = '';
    if(totalPages > 1) {
        html += `<button ${activePage===1?'disabled':''} onclick="window.goToDesktopPage(${activePage-1})" class="rounded px-2 py-1 ${activePage===1?'bg-gray-200 text-gray-400':'bg-white text-gray-600 border'}">&lt;</button>`;
        // Show max 5 page numbers
        let min = Math.max(1, activePage-2), max = Math.min(totalPages, min+4);
        min = Math.max(1, max-4);
        for(let i=min; i<=max; i++) {
            html += `<button class="rounded px-2 py-1 ${i==activePage ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700'}" onclick="window.goToDesktopPage(${i})">${i}</button>`;
        }
        html += `<button ${activePage===totalPages?'disabled':''} onclick="window.goToDesktopPage(${activePage+1})" class="rounded px-2 py-1 ${activePage===totalPages?'bg-gray-200 text-gray-400':'bg-white text-gray-600 border'}">&gt;</button>`;
    }
    document.getElementById('desktopPagination').innerHTML = html;
}
window.goToDesktopPage = function(page){
    if(page < 1) return;
    currentPage = page;
    renderDesktopTable();
}

// Filtering
function applyFilter() {
    const search = (document.getElementById('orderSearch').value || '').trim().toLowerCase();
    const from = document.getElementById('filterDateFrom').value;
    const to = document.getElementById('filterDateTo').value;
    filteredOrders = ordersData.filter(order => {
        const matchSearch = !search || order.order_number_lc.includes(search) || order.cashier_lc.includes(search);
        const matchFrom = !from || order.date >= from;
        const matchTo = !to || order.date <= to;
        return matchSearch && matchFrom && matchTo;
    });
    currentPage = 1;
    renderMobileOrders();
    renderDesktopTable();
}

document.addEventListener('DOMContentLoaded', function() {
    renderMobileOrders();
    renderDesktopTable();
    document.getElementById('orderSearch').addEventListener('input', applyFilter);
    document.getElementById('filterDateFrom').addEventListener('change', applyFilter);
    document.getElementById('filterDateTo').addEventListener('change', applyFilter);
});
</script>
@endpush