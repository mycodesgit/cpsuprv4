<!-- Item Grid -->
<div class="row g-3" id="itemsGridContainer">
    @forelse($items as $item)
        <div class="col-12 col-md-6 col-xl-4 item-node">
            <div class="card card-animate item-card p-3 h-100 d-flex flex-column justify-content-between" id="card-{{ $item->id }}">
                <div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="badge bg-secondary-subtle text-dark fw-normal rounded-1 px-2 py-1">
                            {{ $item->category->category_name ?? 'N/A' }}
                        </span>
                        <span class="text-muted small">{{ $item->unit->unit_name ?? 'Unit' }}</span>
                    </div>
                    <h3 class="h6 fw-bold text-dark mb-1">{{ $item->item_description }}</h3>
                </div>
                <div class="mt-3">
                    <div class="fw-bold text-dark fs-6 mb-3">₱{{ number_format($item->estimated_cost, 2) }}</div>
                    <button class="btn btn-sm btn-shadcn-dark w-100 btn-add-to-cart" 
                            data-id="{{ $item->id }}" 
                            data-name="{{ $item->item_description }}" 
                            data-cost="{{ $item->estimated_cost }}" 
                            data-unit="{{ $item->unit->unit_name ?? 'Unit' }}">
                        <i class="fas fa-plus me-1"></i> Add to Request
                    </button>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="alert alert-light text-center border py-4">
                No items found matching your criteria.
            </div>
        </div>
    @endforelse
</div>

<!-- Pagination Links -->
<div class="d-flex justify-content-center mt-4" id="paginationContainer">
    {{ $items->links('pagination::bootstrap-5') }}
</div>