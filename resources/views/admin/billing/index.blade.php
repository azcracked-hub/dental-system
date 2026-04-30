@extends('layouts.admin')

@section('content')

@if(session('success'))
    <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-xl text-sm">
        {{ session('success') }}
    </div>
@endif

<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-xl font-bold text-gray-900">Billing Records</h1>
        <p class="text-sm text-gray-400">{{ $billings->count() }} total records</p>
    </div>
</div>

{{-- Summary Cards --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 border-l-4 border-l-green-400">
        <p class="text-sm text-gray-500 mb-1">Total Collected</p>
        <p class="text-2xl font-bold text-gray-900">₱{{ number_format($billings->where('status','paid')->sum('amount'), 0) }}</p>
    </div>
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 border-l-4 border-l-yellow-400">
        <p class="text-sm text-gray-500 mb-1">Pending Payment</p>
        <p class="text-2xl font-bold text-gray-900">₱{{ number_format($billings->where('status','unpaid')->sum('amount'), 0) }}</p>
    </div>
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 border-l-4 border-l-blue-400">
        <p class="text-sm text-gray-500 mb-1">Total Records</p>
        <p class="text-2xl font-bold text-gray-900">{{ $billings->count() }}</p>
    </div>
</div>

{{-- Billing Cards --}}
<div class="space-y-4">
    @forelse($billings as $bill)
        <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-5">
            <div class="flex items-start justify-between">
                <div>
                    <h3 class="text-base font-semibold text-gray-900">{{ $bill->appointment->service->name ?? $bill->description ?? 'Billing' }}</h3>
                    <p class="text-sm text-gray-500">{{ $bill->patient->name ?? 'N/A' }}</p>
                    <div class="mt-2 space-y-0.5 text-xs text-gray-400">
                        <p>Date: {{ \Carbon\Carbon::parse($bill->created_at)->format('M d, Y') }}</p>
                        @if($bill->payment_method)
                            <p>Payment Method: {{ $bill->payment_method }}</p>
                        @endif
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-xl font-bold text-yellow-500">₱{{ number_format($bill->amount, 0) }}</p>
                    <span class="text-xs px-2.5 py-1 rounded-full font-medium mt-1 inline-block
                        {{ $bill->status === 'paid' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                        {{ $bill->status }}
                    </span>
                </div>
            </div>

            @if($bill->status === 'unpaid')
                <div class="mt-4 flex gap-3">
                    <button onclick="openMarkPaidModal({{ $bill->id }})"
                        class="flex-1 bg-yellow-400 hover:bg-yellow-500 text-white text-sm font-semibold py-2.5 rounded-xl transition">
                        Mark as Paid
                    </button>
                    <form action="{{ route('admin.billing.destroy', $bill->id) }}" method="POST">
                        @csrf @method('DELETE')
                        <button type="submit" onclick="return confirm('Delete this billing record?')"
                            class="border border-red-200 text-red-400 hover:bg-red-50 text-sm font-semibold px-4 py-2.5 rounded-xl transition">
                            Delete
                        </button>
                    </form>
                </div>
            @endif
        </div>
    @empty
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-12 text-center text-gray-400">
            <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <rect x="1" y="4" width="22" height="16" rx="2" stroke-width="1.5"/>
                <line x1="1" y1="10" x2="23" y2="10" stroke-width="1.5"/>
            </svg>
            <p class="text-sm">No billing records found</p>
        </div>
    @endforelse
</div>

{{-- Mark as Paid Modal --}}
<div id="markPaidModal" class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-base font-bold text-gray-900">Mark as Paid</h2>
            <button onclick="document.getElementById('markPaidModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">✕</button>
        </div>
        <form id="markPaidForm" method="POST" class="space-y-4">
            @csrf @method('PATCH')
            <div>
                <label class="text-sm font-medium text-gray-700 block mb-1">Payment Method</label>
                <select name="payment_method" class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-300">
                    <option value="cash">Cash</option>
                    <option value="GCash">GCash</option>
                    <option value="bank_transfer">Bank Transfer</option>
                </select>
            </div>
            <div class="flex gap-3">
                <button type="button" onclick="document.getElementById('markPaidModal').classList.add('hidden')"
                    class="flex-1 border border-gray-200 text-gray-600 text-sm font-semibold py-2.5 rounded-xl hover:bg-gray-50">Cancel</button>
                <button type="submit"
                    class="flex-1 bg-yellow-400 hover:bg-yellow-500 text-white text-sm font-semibold py-2.5 rounded-xl">Confirm Payment</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
function openMarkPaidModal(id) {
    let url = "{{ route('admin.billing.markPaid', ':id') }}";
    url = url.replace(':id', id);

    document.getElementById('markPaidForm').action = url;
    document.getElementById('markPaidModal').classList.remove('hidden');
}
</script>
@endpush
