@extends('layout.app')

@section('title')
    CPSU PR V.4 | Year
@endsection

@section('body')
    <div class="row">
        <div class="col-12">
            <div class="mb-4">
                
                <!-- Dashboard Header -->
                <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                    <div>
                        <h1 class="h4 fw-bold mb-1">Years Management</h1>
                        <p class="text-muted small mb-0">Manage years and their associated settings across the system.</p>
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
                                <form method="POST" id="addYear">
                                    @csrf

                                    <div class="form-group mb-3">
                                        <div class="row g-3">
                                            <div class="col-md-12">
                                                <label class="form-label fw-semibold">Year Name: <span class="text-danger">*</span></label>
                                                <input type="text" name="pryear" class="form-control form-control-sm" oninput="this.value = this.value.toUpperCase()" placeholder="Enter office name" required>
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
                                <table id="yearTable" class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Year</th>
                                            <th>Status</th>
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

    <div class="modal fade" id="editYearModal" tabindex="-1" role="dialog" aria-labelledby="editYearModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="editYearModalLabel">Edit Year</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editYearForm">
                    <div class="modal-body">
                        <input type="hidden" name="id" id="editYearId">
                        <div class="col-md-12 mb-3">
                            <label for="editYearName" class="form-label fw-semibold">Year: <span class="text-danger">*</span></label>
                            <input type="number" class="form-control form-control-sm" id="editYearName" name="pryear" required>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="editYearStatus" class="form-label fw-semibold">Status: <span class="text-danger">*</span></label>
                            <select name="status" id="editYearStatus" class="form-control form-control-sm">
                                <option value="1">Enabled</option>
                                <option value="2">Disabled</option>
                                <option value="3">Upcoming</option>
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
        var yearReadRoute = "{{ route('year.show') }}";
        var yearCreateRoute = "{{ route('year.create') }}";
        var yearUpdateRoute = "{{ route('year.update', ['id' => ':id']) }}";

        var isAdmin = '{{ Auth::guard("web")->user()->role == "Administrator" ? true : false }}';
        var isProcurementOfficer = '{{ Auth::guard("web")->user()->role == "Procurement Officer" ? true : false }}';
        var isChecker = '{{ Auth::guard("web")->user()->role == "Checker" ? true : false }}';
    </script>
@endsection