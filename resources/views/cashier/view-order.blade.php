@extends('layouts.app')

@section('title', 'Order Details')
@section('page-title', 'Order Details')

@section('content')
<div class="max-w-2xl md:max-w-4xl mx-auto">
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 p-5 border-b bg-gradient-to-r from-primary-50 to-gray-100">
            <h2 class="text-lg md:text-2xl font-bold flex items-center gap-2">
                <span class="inline-flex items-center rounded-full bg-blue-100 text-blue-700 px-3 py-1 text-xs font-semibold uppercase tracking-wide">
                    Order #{{ $order->order_number }}
                </span>
            </h2>
            <a href="{{ route('cashier.orders') }}" class="inline-flex items-center bg-gray-400 hover:bg-gray-600 text-white px-4 py-2 rounded-md text-sm shadow transition-colors duration-200">
                <i class="fas fa-arrow-left mr-2"></i> Back to Orders
            </a>
        </div>
        
        <!-- Order Meta Info -->
        <div class="px-5 pt-5 pb-4 bg-gray-50 border-b">
            <div class="flex flex-col sm:flex-row gap-4 sm:gap-8">
                <div class="flex-1">
                    <span class="block text-xs text-gray-500 mb-1">Date & Time</span>
                    <span class="font-medium text-gray-800">{{ $order->created_at->format('d M Y H:i:s') }}</span>
                </div>
                <div class="flex-1">
                    <span class="block text-xs text-gray-500 mb-1">Cashier</span>
                    <span class="font-medium text-gray-800">{{ $order->user->name }}</span>
                </div>
                <div class="flex-1">
                    <span class="block text-xs text-gray-500 mb-1">Payment</span>
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                        {{ $order->payment_method === 'cash' ? 'bg-green-50 text-green-700 border border-green-200' : ($order->payment_method === 'card' ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'bg-gray-100 text-gray-700 border border-gray-200') }}">
                        <i class="fas {{ $order->payment_method === 'cash' ? 'fa-money-bill-wave' : ($order->payment_method === 'card' ? 'fa-credit-card' : 'fa-wallet') }} mr-1"></i>
                        {{ ucfirst($order->payment_method) }}
                    </span>
                </div>
            </div>
        </div>
        
        <!-- Order Items -->
        <div class="p-5">
            <h3 class="font-semibold text-lg mb-3 text-primary-700 flex items-center gap-2">
                <i class="fas fa-list text-primary-400"></i> Order Items
            </h3>
            <div class="overflow-x-auto border rounded-lg">
                <table class="min-w-full text-xs md:text-sm divide-y divide-gray-200">
                    <thead class="bg-primary-50/50">
                        <tr>
                            <th class="px-4 py-2 text-left font-semibold text-gray-600 uppercase">Product</th>
                            <th class="px-4 py-2 text-left font-semibold text-gray-600 uppercase">Price</th>
                            <th class="px-4 py-2 text-left font-semibold text-gray-600 uppercase">Qty</th>
                            <th class="px-4 py-2 text-left font-semibold text-gray-600 uppercase">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @foreach($order->orderItems as $item)
                        <tr>
                            <td class="px-4 py-3 whitespace-nowrap flex items-center gap-3">
                                @if($item->product->image)
                                    <img class="h-8 w-8 rounded border object-cover" src="{{ asset('storage/' . $item->product->image) }}" alt="{{ $item->product->name }}">
                                @endif
                                <span class="font-medium text-gray-800">{{ $item->product->name }}</span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-gray-700">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-gray-700">{{ $item->quantity }}</td>
                            <td class="px-4 py-3 whitespace-nowrap font-semibold text-gray-900">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Totals -->
        <div class="px-5 pb-5">
            <div class="bg-gradient-to-r from-primary-50 via-white to-primary-50 rounded-xl p-4 md:p-6 shadow-inner max-w-md ml-auto">
                <div class="flex justify-between pb-2 mb-2 border-b text-sm">
                    <span>Subtotal</span>
                    <span>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between pb-2 mb-2 border-b text-sm">
                    <span>Tax</span>
                    <span>Rp {{ number_format($order->tax, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between pb-2 mb-2 border-b text-sm">
                    <span>Discount</span>
                    <span>Rp {{ number_format($order->discount, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between pt-2 font-bold text-lg text-primary-700">
                    <span>Total</span>
                    <span>Rp {{ number_format($order->final_amount, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- Print Button -->
        <div class="pb-7 px-5">
            <div class="mt-6 flex justify-center">
                <button onclick="window.print()" class="bg-primary-600 hover:bg-primary-700 text-white px-6 py-2 rounded-md text-base font-semibold shadow transition-colors duration-200 print:hidden">
                    <i class="fas fa-print mr-2"></i> Print Receipt
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    @media print {
        body * {
            visibility: hidden;
        }
        #content, #content * {
            visibility: visible;
        }
        #sidebar, header, .print\:hidden, .mt-6, a, button:not(.force-print) {
            display: none !important;
        }
        .bg-gray-50, .bg-gray-100, .bg-white, .bg-gradient-to-r, .shadow, .shadow-lg, .shadow-inner {
            background: #fff !important;
            color: #000 !important;
            box-shadow: none !important;
        }
        #content {
            position: absolute !important;
            left: 0;
            top: 0;
            width: 100vw !important;
            margin: 0 !important;
            padding: 0 !important;
        }
        table {
            width: 100% !important;
            font-size: 12px !important;
        }
    }
</style>
@endpush