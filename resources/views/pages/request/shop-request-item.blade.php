@extends('layout.app')

@section('body')
    <div class="row">
        <div class="col-12">
            <div class="mb-4">
                
                <!-- Dashboard Header -->
                <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                    <div>
                        <h1 class="h4 fw-bold mb-1">Category Management</h1>
                        <p class="text-muted small mb-0">Manage categories and their associated settings across the system.</p>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-outline-secondary">Export Report</button>
                    </div>
                </div>

                <div class="row g-3 mb-5">
                    <!-- Left Sidebar: Categories -->
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="text-uppercase fw-bold text-muted small tracking-wide mb-2" style="font-size: 0.7rem; letter-spacing: 0.05em;">Categories</label>
                                    <div class="nav flex-column nav-category gap-1" id="categoryNav">
                                        <a href="{{ route('shopitem-request.index', array_merge(request()->except('page'), ['category' => 'all'])) }}" 
                                        class="category-link nav-link {{ request('category', 'all') == 'all' ? 'active' : '' }} d-flex justify-content-between align-items-center">
                                            <span><i class="fas fa-th-large me-2 opacity-75"></i> All Items</span>
                                            <span class="badge bg-secondary-subtle text-dark rounded-pill px-2">{{ $totalItemsCount }}</span>
                                        </a>

                                        @foreach($categories as $category)
                                            <a href="{{ route('shopitem-request.index', array_merge(request()->except('page'), ['category' => $category->id])) }}" 
                                            class="category-link nav-link {{ request('category') == $category->id ? 'active' : '' }} d-flex justify-content-between align-items-center">
                                                <span><i class="fas fa-box me-2 opacity-75"></i> {{ $category->category_name }}</span>
                                                <span class="badge bg-secondary-subtle text-dark rounded-pill px-2">{{ $category->items_count }}</span>
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Side: Items Grid and Search -->
                    <div class="col-md-8">
                        <!-- Top Search Bar & Header -->
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-3">
                            <div>
                                <h2 class="h6 fw-bold text-dark mb-0" id="currentCategoryTitle">{{ $currentCategoryName }}</h2>
                                <span class="text-muted small" id="itemCountDisplay">
                                    Showing {{ $items->firstItem() ?? 0 }}-{{ $items->lastItem() ?? 0 }} of {{ $items->total() }} items
                                </span>
                            </div>
                    
                            <!-- Modern Search Input -->
                            <form id="searchForm" class="search-form-wrapper position-relative">
                                <div class="search-input-group position-relative d-flex align-items-center">
                                    <!-- Search Icon Inside Input -->
                                    <i class="fas fa-search search-icon text-muted position-absolute ms-3 pointer-events-none"></i>
                                    
                                    <!-- Input Field -->
                                    <input 
                                        type="text" 
                                        id="searchInput" 
                                        name="search" 
                                        class="form-control form-control-sm search-control ps-5 pe-5 rounded-3 border-subtle shadow-sm" 
                                        placeholder="Search items by name or category..." 
                                        value="{{ request('search') }}"
                                        autocomplete="off"
                                    >

                                    <!-- Inline Clear Button (Appears when typing/searching) -->
                                    <button 
                                        type="button" 
                                        id="clearSearchBtn" 
                                        class="btn btn-link text-muted p-0 border-0 position-absolute end-0 me-3 search-clear-btn {{ request()->filled('search') ? '' : 'd-none' }}" 
                                        title="Clear Search"
                                    >
                                        <i class="fas fa-times-circle"></i>
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Items Grid Container -->
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
                                            <h3 class="h6 fw-bold text-dark mb-1" 
                                                data-bs-toggle="tooltip" 
                                                data-bs-placement="top" 
                                                title="{{ $item->item_description }}">
                                                {{ \Illuminate\Support\Str::limit($item->item_description, 100, '...') }}
                                            </h3>
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

                        <!-- Pagination Container -->
                        <div class="d-flex justify-content-center mt-4" id="paginationContainer">
                            {{ $items->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            // Truncate function for JS
            function truncateString(str, num) {
                if (!str) return '';
                return str.length > num ? str.slice(0, num) + '...' : str;
            }

            function renderItems(items) {
                const container = document.getElementById('itemsGridContainer');
                if (!container) return;

                if (!items || items.length === 0) {
                    container.innerHTML = `
                        <div class="col-12">
                            <div class="alert alert-light text-center border py-4">
                                No items found matching your criteria.
                            </div>
                        </div>`;
                    return;
                }

                let html = '';
                items.forEach(item => {
                    const categoryName = item.category ? item.category.category_name : 'N/A';
                    const unitName = item.unit ? item.unit.unit_name : 'Unit';
                    const formattedCost = parseFloat(item.estimated_cost || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                    
                    const truncatedDesc = truncateString(item.item_description, 100);
                    const fullDesc = (item.item_description || '').replace(/"/g, '&quot;');

                    html += `
                        <div class="col-12 col-md-6 col-xl-4 item-node">
                            <div class="card p-3 h-100 d-flex flex-column justify-content-between" id="card-${item.id}">
                                <div>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="badge bg-secondary-subtle text-dark fw-normal rounded-1 px-2 py-1">
                                            ${categoryName}
                                        </span>
                                        <span class="text-muted small">${unitName}</span>
                                    </div>
                                    
                                    <!-- Native Tooltip (Works out-of-the-box without Bootstrap JS) -->
                                    <h3 class="h6 fw-bold text-dark mb-1 cursor-pointer" title="${fullDesc}">
                                        ${truncatedDesc}
                                    </h3>
                                </div>
                                <div class="mt-3">
                                    <div class="fw-bold text-dark fs-6 mb-3">₱${formattedCost}</div>
                                    <button class="btn btn-sm btn-shadcn-dark w-100 btn-add-to-cart" 
                                            data-id="${item.id}" 
                                            data-name="${fullDesc}" 
                                            data-cost="${item.estimated_cost}" 
                                            data-unit="${unitName}">
                                        <i class="fas fa-plus me-1"></i> Add to Request
                                    </button>
                                </div>
                            </div>
                        </div>`;
                });

                container.innerHTML = html;
            }

            function initTooltips() {
                // Check if Bootstrap JS object is available globally
                if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
                    // Remove orphan tooltips from screen
                    document.querySelectorAll('.tooltip').forEach(t => t.remove());

                    // Initialize tooltips
                    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                    tooltipTriggerList.map(function (tooltipTriggerEl) {
                        return new bootstrap.Tooltip(tooltipTriggerEl);
                    });
                }
            }

            // Run initial tooltip setup when page loads
            document.addEventListener('DOMContentLoaded', function () {
                initTooltips();
            });

            function fetchItems(url) {
                if (!url || url === '#' || url.startsWith('javascript:')) return;

                fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    if (!response.ok) throw new Error('HTTP status ' + response.status);
                    return response.json();
                })
                .then(data => {
                    if (data && data.success) {
                        renderItems(data.items);

                        const pagination = document.getElementById('paginationContainer');
                        if (pagination) pagination.innerHTML = data.pagination;

                        const title = document.getElementById('currentCategoryTitle');
                        if (title) title.innerText = data.categoryName;

                        const count = document.getElementById('itemCountDisplay');
                        if (count) count.innerText = data.itemCountText;

                        window.history.pushState({}, '', url);
                    }
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                });
            }

            // Category click handler
            document.addEventListener('click', function (e) {
                const categoryLink = e.target.closest('a.category-link');
                if (categoryLink) {
                    e.preventDefault();

                    document.querySelectorAll('a.category-link').forEach(el => el.classList.remove('active'));
                    categoryLink.classList.add('active');

                    const url = categoryLink.getAttribute('href');
                    fetchItems(url);
                    return;
                }

                const pageLink = e.target.closest('#paginationContainer .page-link, .pagination a');
                if (pageLink) {
                    e.preventDefault();
                    const url = pageLink.getAttribute('href');
                    fetchItems(url);
                }
            });

            // Search form handler
            const searchForm = document.getElementById('searchForm');
            if (searchForm) {
                searchForm.addEventListener('submit', function (e) {
                    e.preventDefault();
                    const searchValue = document.getElementById('searchInput').value.trim();
                    const url = new URL(window.location.href);

                    if (searchValue) {
                        url.searchParams.set('search', searchValue);
                    } else {
                        url.searchParams.delete('search');
                    }
                    url.searchParams.set('page', '1');

                    const clearBtn = document.getElementById('clearSearchBtn');
                    if (clearBtn) clearBtn.classList.toggle('d-none', !searchValue);

                    fetchItems(url.toString());
                });
            }

            // Clear search handler
            const clearBtn = document.getElementById('clearSearchBtn');
            if (clearBtn) {
                clearBtn.addEventListener('click', function () {
                    document.getElementById('searchInput').value = '';
                    const url = new URL(window.location.href);
                    url.searchParams.delete('search');
                    url.searchParams.set('page', '1');
                    this.classList.add('d-none');
                    fetchItems(url.toString());
                });
            }
        });
    </script>
@endsection