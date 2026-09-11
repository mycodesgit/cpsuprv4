@extends('layout.app')

@section('title')
    CPSU PR V.4 | Category
@endsection

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
                        <button class="btn btn-sm btn-outline-secondary">
                            Export Report
                        </button>
                    </div>
                </div>

                <!-- Top Row: Metric Cards -->
                <div class="row g-3 mb-5">
                    <div class="col-md-4">
                        <div class="card card-animate">
                            <div class="card-header pt-3">
                                <h6 class="card-title">
                                    <i class="ti ti-plus"></i> Add New
                                </h6>
                            </div>
                            <div class="card-body">
                                <form method="POST" id="adCategory">
                                    @csrf

                                    <div class="form-group mb-3">
                                        <div class="row g-3">
                                            <div class="col-md-12">
                                                <label class="form-label fw-semibold">Category Name: <span class="text-danger">*</span></label>
                                                <input type="text" name="category_name" class="form-control form-control-sm" placeholder="Enter category name" required>
                                            </div>
                                            
                                            <div class="col-md-12">
                                                <label class="form-label fw-semibold">Is ICT?: <span class="text-danger">*</span></label>
                                                <select name="isICT" class="form-control form-control-sm" required>
                                                    <option value="">Select</option>
                                                    <option value="0" selected>No</option>
                                                    <option value="1">Yes</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="form-row">
                                            <div class="col-md-12">
                                                <button type="submit" class="btn btn-outline-success">
                                                    <i class="fas fa-save"></i> Save
                                                </button>
                                            </div>
                                        </div>
                                    </div>   
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="card card-animate">
                            <div class="card-header pt-3">
                                <h6 class="card-title">
                                    <i class="fas fa-list"></i> List
                                </h6>
                            </div>
                            <div class="card-body">
                                <table id="categoryTable" class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Category</th>
                                            <th>Is ICT</th>
                                            <th width="10%">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editCategoryModal" tabindex="-1" role="dialog" aria-labelledby="editCategoryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="editCategoryModalLabel">
                        <i class="fas fa-edit"></i> Edit Category Name
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editCategoryForm">
                    <div class="modal-body">
                        <input type="hidden" name="id" id="editCategoryId">
                        <div class="col-md-12 mb-3">
                            <label for="editCategoryName" class="form-label fw-semibold">Category Name: <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="editCategoryName" name="category_name" required>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="editIsIct" class="form-label fw-semibold">Is ICT?: <span class="text-danger">*</span></label>
                            <select name="isICT" id="editIsIct" class="form-control" required>
                                <option value="">Select</option>
                                <option value="0">No</option>
                                <option value="1">Yes</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer d-flex justify-content-between">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        var categoryReadRoute   = "{{ route('category.show') }}";
        var categoryCreateRoute = "{{ route('category.create') }}";
        var categoryUpdateRoute = "{{ route('category.update', ['id' => ':id']) }}";

        var isAdmin = '{{ Auth::guard("web")->user()->role == "Administrator" ? true : false }}';
        var isProcurementOfficer = '{{ Auth::guard("web")->user()->role == "Procurement Officer" ? true : false }}';
        var isChecker = '{{ Auth::guard("web")->user()->role == "Checker" ? true : false }}';
    </script>
@endsection