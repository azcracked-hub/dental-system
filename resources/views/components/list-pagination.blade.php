@props(['paginator'])

@if ($paginator->hasPages())
    <nav {{ $attributes->merge(['class' => 'mt-6 flex flex-col gap-3 border-t border-gray-100 pt-4 sm:flex-row sm:items-center sm:justify-between']) }}>
        <p class="text-sm text-gray-500">
            Showing {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} of {{ $paginator->total() }}
        </p>
        <div>{{ $paginator->links() }}</div>
    </nav>
@endif
