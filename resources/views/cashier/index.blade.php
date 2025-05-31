@extends('layouts.app')

@section('title', 'Cashier')
@section('page-title', 'POS System')

@push('styles')
<style>
    html, body { overflow-x: hidden !important; }
    .cart-fab {
        position: fixed;
        right: 1.5rem;
        bottom: 5.5rem;
        z-index: 100;
        box-shadow: 0 4px 20px 0 rgba(59,130,246,.18);
        transition: transform 0.18s cubic-bezier(.4,2,.6,1);
        will-change: transform;
    }
    .cart-fab.show { animation: popcart .18s cubic-bezier(.4,2,.6,1); }
    @keyframes popcart { from { transform: scale(.7);} to {transform:scale(1);} }
    .cart-fab:hover { transform: scale(1.08); }
    .cart-badge {
        position: absolute;
        top: 0; right: 0;
        background: #ef4444;
        color: white;
        border-radius: 9999px;
        font-size: .8rem;
        padding: .14em .5em;
        transform: translate(30%, -30%);
        font-weight: bold;
        box-shadow: 0 0 0 2px #fff;
        min-width: 1.1em;
        text-align: center;
    }
    @media (min-width: 768px) {
        .cart-fab { bottom: 2.5rem; }
        .cart-badge { font-size: .9rem; }
    }
    input[type=number]::-webkit-inner-spin-button, input[type=number]::-webkit-outer-spin-button { -webkit-appearance: none; margin: 0; }
    input[type=number] { -moz-appearance:textfield; }
    .fade-modal { animation: fadeIn .16s; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(24px);} to { opacity: 1; transform: translateY(0);} }
    .loading-spinner {
        border: 3px solid #f3f3f3; border-top: 3px solid #3b82f6;
        border-radius: 50%; width: 1.3em; height: 1.3em;
        animation: spin .7s linear infinite;
        display: inline-block;
        vertical-align: middle;
    }
    @keyframes spin { to {transform: rotate(360deg);} }
</style>
@endpush

@section('content')
<div class="w-full max-w-3xl mx-auto pb-20 pt-4 md:pt-10">
    <div class="flex flex-col gap-4 md:gap-6">
        <!-- Search and Category Filter -->
        <div class="flex w-full gap-2 items-center">
            <select id="category"
                class="block w-24 md:w-44 px-2 md:px-4 py-2 md:py-3 border border-gray-300 rounded-lg md:rounded-xl text-sm md:text-base focus:ring-2 focus:ring-blue-400 bg-white flex-shrink-0">
                <option value="all">All</option>
                @foreach($categories as $category)
                <option value="{{ $category->id }}">
                    {{ $category->name }}
                </option>
                @endforeach
            </select>
            <input type="text" id="search"
                class="flex-1 block w-full px-3 md:px-5 py-2 md:py-3 border border-gray-300 rounded-lg md:rounded-xl text-base md:text-lg focus:ring-2 focus:ring-blue-400 min-w-0"
                placeholder="🔍 Search product...">
        </div>
        <!-- Product List -->
        <div id="products-container" class="flex flex-col mt-2 gap-2 md:gap-4">
            @forelse($products as $product)
            <div class="product-item group flex items-center gap-3 md:gap-6 p-2 md:p-5 bg-white shadow hover:shadow-xl border-2 border-gray-100 hover:border-blue-400 rounded-xl md:rounded-2xl cursor-pointer transition min-h-[68px] md:min-h-[96px] relative"
                data-id="{{ $product->id }}"
                data-name="{{ $product->name }}"
                data-price="{{ $product->selling_price }}"
                data-category="{{ $product->category_id }}">
                <div class="flex flex-col items-center w-16 md:w-28 flex-shrink-0">
                    <div class="w-12 h-12 md:w-16 md:h-16 flex items-center justify-center bg-gray-100 rounded-lg md:rounded-xl overflow-hidden border border-gray-200">
                        @if($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}"
                                class="object-cover w-full h-full">
                        @else
                            <i class="fas fa-box text-gray-300 text-xl md:text-2xl"></i>
                        @endif
                    </div>
                    <span class="text-[11px] md:text-xs mt-1 md:mt-2 rounded-full px-2 py-0.5 font-semibold 
                        {{ $product->stock == 0 ? 'bg-gray-300 text-gray-600' : ($product->stock <= 5 ? 'bg-rose-600 text-white' : 'bg-green-100 text-green-700') }}">
                        {{ $product->stock == 0 ? 'Out of stock' : ($product->stock <= 5 ? 'Low: '.$product->stock : 'Stock: '.$product->stock) }}
                    </span>
                </div>
                <div class="flex-1 flex flex-col min-w-0">
                    <div class="font-extrabold text-base md:text-xl text-gray-800 truncate">{{ $product->name }}</div>
                    <div class="text-xs md:text-sm text-gray-500 mb-1 md:mb-2">{{ $product->category->name ?? '-' }}</div>
                    <div class="flex items-center gap-2">
                        <span class="font-bold text-lg md:text-2xl text-blue-600">
                            {{ 'Rp ' . number_format($product->selling_price, 0, ',', '.') }}
                        </span>
                        @if($product->stock == 0)
                        <span class="ml-2 text-[10px] md:text-xs font-semibold text-white bg-gray-400 px-2 py-0.5 rounded-full">Out</span>
                        @endif
                    </div>
                </div>
                {{-- Divider --}}
                <div class="absolute left-3 right-3 md:left-6 md:right-6 bottom-0 h-px bg-gradient-to-r from-blue-200 via-gray-200 to-blue-200 opacity-70 group-hover:opacity-100"></div>
            </div>
            @empty
            <div class="text-center text-gray-400 py-10">
                <i class="fas fa-box-open text-2xl md:text-3xl"></i>
                <div>No products available</div>
            </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Floating Cart Icon -->
<button id="cart-fab" class="cart-fab bg-blue-600 hover:bg-blue-700 text-white rounded-full w-14 h-14 md:w-20 md:h-20 flex items-center justify-center text-3xl md:text-4xl transition focus:outline-none focus:ring-2 focus:ring-blue-400"
    aria-label="Open cart" style="display:none;">
    <i class="fas fa-shopping-cart"></i>
    <span id="cart-badge" class="cart-badge" style="display:none;">0</span>
</button>

<!-- Modal Cart/Receipt -->
<div id="cart-modal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-40 flex items-end md:items-center justify-center p-0 md:p-6">
    <div class="bg-white rounded-t-2xl md:rounded-2xl shadow-xl w-full max-w-lg mx-auto p-4 md:p-8 flex flex-col max-h-[90vh] overflow-y-auto relative fade-modal">
        <button id="close-cart" class="absolute top-2 right-2 md:top-3 md:right-3 text-gray-400 hover:text-gray-700 text-xl md:text-2xl focus:outline-none" tabindex="0">
            <i class="fas fa-times"></i>
        </button>
        <h3 class="text-xl md:text-2xl font-extrabold mb-2 md:mb-4 text-blue-700 flex items-center"><i class="fas fa-shopping-cart mr-2"></i> Cart</h3>
        <div id="cart-list" class="divide-y divide-gray-100 mb-4 md:mb-5">
            <!-- Cart items will be injected here -->
        </div>
        <div class="my-2 md:my-3 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="text-gray-600 text-sm md:text-base">Include Tax (10%)</span>
                <label class="inline-flex items-center cursor-pointer ml-1 relative">
                    <input type="checkbox" id="tax-switch" class="sr-only peer">
                    <div class="w-8 h-5 md:w-10 md:h-6 bg-gray-200 rounded-full peer peer-checked:bg-blue-500 transition-all"></div>
                    <div class="w-4 h-4 md:w-5 md:h-5 bg-white border border-gray-300 rounded-full absolute mt-0.5 ml-0.5 peer-checked:translate-x-4 transition-all"></div>
                </label>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-gray-600 text-sm md:text-base">Discount</span>
                <input id="discount" type="number" min="0" value="0"
                    class="w-16 md:w-20 px-2 md:px-3 py-1 md:py-2 border rounded text-sm md:text-base text-right focus:ring-2 focus:ring-blue-400">
            </div>
        </div>
        <div class="space-y-1 md:space-y-2 text-base md:text-lg mb-4 md:mb-5">
            <div class="flex justify-between">
                <span class="text-gray-600">Subtotal</span>
                <span id="subtotal" class="font-bold text-gray-800">0</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600">Tax</span>
                <span id="tax-amount" class="font-bold text-blue-500">0</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600">Discount</span>
                <span id="discount-amount" class="font-semibold text-rose-600">0</span>
            </div>
            <div class="flex justify-between border-t pt-2 md:pt-3 font-extrabold text-xl md:text-2xl">
                <span>Total</span>
                <span id="total" class="text-blue-600">0</span>
            </div>
        </div>
        <div>
            <label for="payment-method" class="block text-xs md:text-sm font-medium text-gray-600 mb-1 md:mb-2">Payment Method</label>
            <select id="payment-method"
                class="w-full border rounded-lg md:rounded-xl py-2 md:py-3 px-2 md:px-3 text-sm md:text-base focus:ring-2 focus:ring-blue-400 focus:outline-none bg-white">
                <option value="cash">Cash</option>
                <option value="card">Card</option>
                <option value="other">Other</option>
            </select>
        </div>
        <div class="mt-4 md:mt-6 grid grid-cols-2 gap-2 md:gap-3">
            <button id="clear-cart" type="button"
                class="bg-gray-400 hover:bg-gray-500 text-white py-2 md:py-3 rounded-lg md:rounded-xl font-semibold shadow transition disabled:opacity-50 disabled:cursor-not-allowed text-base flex items-center justify-center gap-2">
                <i class="fas fa-trash-alt"></i> Clear
            </button>
            <button id="process-order" type="button"
                class="bg-blue-600 hover:bg-blue-700 text-white py-2 md:py-3 rounded-lg md:rounded-xl font-semibold shadow transition disabled:opacity-50 disabled:cursor-not-allowed text-base flex items-center justify-center gap-2">
                <span id="process-text"><i class="fas fa-check"></i> Process</span>
                <span id="process-loading" class="loading-spinner hidden"></span>
            </button>
        </div>
    </div>
</div>

<!-- Success Modal -->
<div id="success-modal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-40 flex items-center justify-center p-2 md:p-4">
    <div class="bg-white rounded-2xl shadow-xl p-4 md:p-8 max-w-lg w-full flex flex-col items-center fade-modal">
        <div class="mb-3 md:mb-4 text-green-500">
            <i class="fas fa-check-circle text-4xl md:text-6xl"></i>
        </div>
        <h3 class="text-xl md:text-2xl font-bold mb-2 md:mb-3 text-center text-green-700">Order Completed!</h3>
        <p class="text-gray-600 mb-3 md:mb-4 text-center">Order has been processed successfully.</p>
        <div class="bg-gray-50 p-4 md:p-6 rounded-lg mb-4 md:mb-5 text-base md:text-lg w-full">
            <div class="flex justify-between mb-1 md:mb-2">
                <span>Order Number:</span>
                <span id="receipt-order-number" class="font-bold"></span>
            </div>
            <div class="flex justify-between mb-1 md:mb-2">
                <span>Total Amount:</span>
                <span id="receipt-total" class="font-bold text-blue-600"></span>
            </div>
            <div class="flex justify-between">
                <span>Payment Method:</span>
                <span id="receipt-payment-method" class="font-bold"></span>
            </div>
        </div>
        <div class="flex gap-2 w-full">
            <button id="new-order" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2 md:py-3 rounded-lg md:rounded-xl font-semibold shadow transition text-base flex items-center justify-center gap-2">
                <i class="fas fa-plus"></i> New Order
            </button>
            <button id="view-receipt" class="flex-1 bg-gray-400 hover:bg-gray-500 text-white py-2 md:py-3 rounded-lg md:rounded-xl font-semibold shadow transition text-base flex items-center justify-center gap-2">
                <i class="fas fa-receipt"></i> View Receipt
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let cart = [];
    let useTax = true;
    const taxRate = 0.1;
    let processing = false;

    // DOM Elements
    const productsContainer = document.getElementById('products-container');
    const cartFab = document.getElementById('cart-fab');
    const cartBadge = document.getElementById('cart-badge');
    const cartModal = document.getElementById('cart-modal');
    const cartList = document.getElementById('cart-list');
    const closeCartBtn = document.getElementById('close-cart');
    const subtotalEl = document.getElementById('subtotal');
    const taxAmountEl = document.getElementById('tax-amount');
    const discountInput = document.getElementById('discount');
    const discountAmountEl = document.getElementById('discount-amount');
    const totalEl = document.getElementById('total');
    const clearCartBtn = document.getElementById('clear-cart');
    const processOrderBtn = document.getElementById('process-order');
    const processText = document.getElementById('process-text');
    const processLoading = document.getElementById('process-loading');
    const paymentMethodSelect = document.getElementById('payment-method');
    const searchInput = document.getElementById('search');
    const categorySelect = document.getElementById('category');
    const taxSwitch = document.getElementById('tax-switch');
    // Success modal
    const successModal = document.getElementById('success-modal');
    const receiptOrderNumber = document.getElementById('receipt-order-number');
    const receiptTotal = document.getElementById('receipt-total');
    const receiptPaymentMethod = document.getElementById('receipt-payment-method');
    const newOrderBtn = document.getElementById('new-order');
    const viewReceiptBtn = document.getElementById('view-receipt');
    let currentOrderId = null;

    // Auto-focus search input
    setTimeout(() => { searchInput.focus(); }, 200);

    // Hide/show fab cart
    function hideFabCart() { cartFab.style.display = 'none'; }
    function showFabCartIfNeeded() {
        if (cart.length > 0 && cartModal.classList.contains('hidden') && successModal.classList.contains('hidden')) {
            cartFab.style.display = '';
        }
    }

    // Show/hide cart fab based on cart count
    function updateCartFab() {
        if (cart.length === 0) {
            cartFab.classList.remove('show');
            cartFab.style.display = 'none';
        } else if (cartModal.classList.contains('hidden') && successModal.classList.contains('hidden')) {
            cartFab.style.display = '';
            setTimeout(() => cartFab.classList.add('show'), 10);
            cartBadge.style.display = '';
            cartBadge.textContent = cart.reduce((sum, item) => sum + item.quantity, 0);
        }
    }
    // Add to cart
    productsContainer.addEventListener('click', function(e) {
        const productItem = e.target.closest('.product-item');
        // Prevent add if out of stock
        if (productItem && !productItem.querySelector('.bg-gray-300')) {
            addToCart(
                parseInt(productItem.dataset.id),
                productItem.dataset.name,
                parseFloat(productItem.dataset.price)
            );
        }
    });
    function addToCart(id, name, price) {
        const existingItem = cart.find(item => item.id === id);
        if (existingItem) {
            existingItem.quantity++;
            existingItem.total = existingItem.price * existingItem.quantity;
        } else {
            cart.push({ id, name, price, quantity: 1, total: price });
        }
        updateCartFab();
        cartBadge.classList.remove('animate-bounce');
        void cartBadge.offsetWidth;
        cartBadge.classList.add('animate-bounce');
    }
    // Show cart modal
    cartFab.addEventListener('click', function() {
        renderCartModal();
        cartModal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
        hideFabCart();
        discountInput.value = 0;
        taxSwitch.checked = true;
        useTax = true;
        updateTotals();
    });
    // Close cart modal (ESC or overlay)
    closeCartBtn.addEventListener('click', closeCartModal);
    document.addEventListener('keydown', function(e) {
        if (!cartModal.classList.contains('hidden') && e.key === 'Escape') closeCartModal();
        if (!successModal.classList.contains('hidden') && e.key === 'Escape') closeSuccessModal();
    });
    cartModal.addEventListener('mousedown', function(e) {
        if (e.target === cartModal) closeCartModal();
    });
    function closeCartModal() {
        cartModal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
        showFabCartIfNeeded();
        discountInput.value = 0;
        taxSwitch.checked = true;
        useTax = true;
        updateTotals();
    }

    // Render cart modal content
    function renderCartModal() {
        cartList.innerHTML = '';
        if (cart.length === 0) {
            cartList.innerHTML = `<div class="text-center text-gray-400 py-6"><i class="fas fa-cart-plus text-3xl mb-2"></i><div>Cart is empty</div></div>`;
        } else {
            cart.forEach((item, index) => {
                const div = document.createElement('div');
                div.className = 'flex items-center gap-3 md:gap-5 py-3 md:py-4 animate-fade-in';
                div.innerHTML = `
                    <div class="flex-1 min-w-0">
                        <div class="font-bold text-base md:text-lg text-gray-800 truncate">${item.name}</div>
                        <div class="text-sm md:text-base text-gray-500">${formatRupiah(item.price)} x ${item.quantity}</div>
                    </div>
                    <div class="flex items-center gap-2">
                        <button class="decrease-qty bg-gray-200 hover:bg-gray-300 px-2 md:px-3 py-1 md:py-2 rounded text-lg" data-index="${index}" tabindex="0"><i class="fas fa-minus"></i></button>
                        <span class="font-bold text-lg md:text-2xl">${item.quantity}</span>
                        <button class="increase-qty bg-gray-200 hover:bg-gray-300 px-2 md:px-3 py-1 md:py-2 rounded text-lg" data-index="${index}" tabindex="0"><i class="fas fa-plus"></i></button>
                        <button class="remove-item text-rose-500 hover:text-rose-700 ml-2 md:ml-3 text-xl" data-index="${index}" tabindex="0" title="Remove">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="font-extrabold w-20 md:w-28 text-right text-blue-700 text-lg md:text-xl">${formatRupiah(item.total)}</div>
                `;
                cartList.appendChild(div);
            });
        }
        updateTotals();
        updateCartFab();
        clearCartBtn.disabled = cart.length === 0;
        processOrderBtn.disabled = cart.length === 0;
    }
    // Qty buttons
    cartList.addEventListener('click', function(e) {
        if (e.target.closest('.decrease-qty')) {
            const idx = e.target.closest('.decrease-qty').dataset.index;
            decreaseQuantity(idx);
            renderCartModal();
        }
        if (e.target.closest('.increase-qty')) {
            const idx = e.target.closest('.increase-qty').dataset.index;
            increaseQuantity(idx);
            renderCartModal();
        }
        if (e.target.closest('.remove-item')) {
            const idx = e.target.closest('.remove-item').dataset.index;
            removeItem(idx);
            renderCartModal();
        }
    });

    function decreaseQuantity(index) {
        if (cart[index].quantity > 1) {
            cart[index].quantity--;
            cart[index].total = cart[index].price * cart[index].quantity;
        } else {
            removeItem(index);
        }
    }
    function increaseQuantity(index) {
        cart[index].quantity++;
        cart[index].total = cart[index].price * cart[index].quantity;
    }
    function removeItem(index) {
        cart.splice(index, 1);
    }

    // Tax switch
    taxSwitch.checked = true;
    taxSwitch.addEventListener('change', function() {
        useTax = taxSwitch.checked;
        updateTotals();
    });
    // Discount input
    discountInput.addEventListener('input', updateTotals);

    // Clear cart
    clearCartBtn.addEventListener('click', function() {
        cart = [];
        closeCartModal();
        updateCartFab();
    });

    // Process order
    processOrderBtn.addEventListener('click', function() {
        if (processing || cart.length === 0) return;
        processing = true;
        processOrderBtn.disabled = true;
        processText.classList.add('hidden');
        processLoading.classList.remove('hidden');

        const subtotal = cart.reduce((sum, item) => sum + item.total, 0);
        const tax = useTax ? subtotal * taxRate : 0;
        const discountValue = parseFloat(discountInput.value) || 0;
        const total = subtotal + tax - discountValue;
        const paymentMethod = paymentMethodSelect.value;
        const orderData = {
            items: cart.map(item => ({ product_id: item.id, quantity: item.quantity })),
            total_amount: subtotal,
            tax: tax,
            discount: discountValue,
            final_amount: total,
            payment_method: paymentMethod
        };
        fetch('{{ route('cashier.process-order') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(orderData)
        })
        .then(response => response.json())
        .then(data => {
            processing = false;
            processOrderBtn.disabled = false;
            processText.classList.remove('hidden');
            processLoading.classList.add('hidden');
            if (data.success && data.order && data.order.id) {
                closeCartModal();
                showSuccessModal();
                receiptOrderNumber.textContent = data.order.order_number;
                receiptTotal.textContent = formatRupiah(data.order.final_amount);
                receiptPaymentMethod.textContent = data.order.payment_method.charAt(0).toUpperCase() + data.order.payment_method.slice(1);
                currentOrderId = data.order.id;
                cart = [];
                updateCartFab();
            } else {
                alert('Error: ' + (data.message ?? 'Unknown error, cannot process order.'));
            }
        })
        .catch(error => {
            processing = false;
            processOrderBtn.disabled = false;
            processText.classList.remove('hidden');
            processLoading.classList.add('hidden');
            alert('An error occurred while processing the order');
        });
    });

    // Show/hide fab when modal receipt
    function showSuccessModal() {
        successModal.classList.remove('hidden');
        hideFabCart();
    }
    function closeSuccessModal() {
        successModal.classList.add('hidden');
        showFabCartIfNeeded();
    }

    // Success modal buttons
    newOrderBtn.addEventListener('click', closeSuccessModal);
    viewReceiptBtn.addEventListener('click', function() {
        closeSuccessModal();
        if (currentOrderId && !isNaN(currentOrderId)) {
            window.location.href = `/cashier/orders/${currentOrderId}`;
        } else {
            alert('Order receipt not available!');
        }
    });
    successModal.addEventListener('mousedown', function(e) {
        if (e.target === successModal) closeSuccessModal();
    });

    // Totals
    function updateTotals() {
        const subtotal = cart.reduce((sum, item) => sum + item.total, 0);
        const tax = useTax ? subtotal * taxRate : 0;
        const discountValue = parseFloat(discountInput.value) || 0;
        const total = subtotal + tax - discountValue;
        subtotalEl.textContent = formatRupiah(subtotal);
        taxAmountEl.textContent = formatRupiah(tax);
        discountAmountEl.textContent = formatRupiah(discountValue);
        totalEl.textContent = formatRupiah(total >= 0 ? total : 0);
        processOrderBtn.disabled = cart.length === 0 || processing;
        clearCartBtn.disabled = cart.length === 0;
    }

    // Product search & category filter
    searchInput.addEventListener('input', filterProducts);
    categorySelect.addEventListener('change', filterProducts);
    function filterProducts() {
        const searchTerm = searchInput.value.toLowerCase();
        const cat = categorySelect.value;
        const items = productsContainer.querySelectorAll('.product-item');
        items.forEach(item => {
            const name = item.dataset.name.toLowerCase();
            const catMatch = cat === 'all' || item.dataset.category === cat;
            item.style.display = (name.includes(searchTerm) && catMatch) ? '' : 'none';
        });
    }

    // Format number as Rupiah
    function formatRupiah(angka) {
        if (typeof angka === "string") angka = angka.replace(/[^\d]/g, "");
        return 'Rp ' + (parseInt(angka) || 0).toLocaleString('id-ID');
    }

    // Init
    updateCartFab();
});
</script>
@endpush