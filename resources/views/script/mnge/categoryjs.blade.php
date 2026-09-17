<script>
    toastr.options = {
        "closeButton": true,
        "progressBar": true,
        "positionClass": "toast-top-right"
    };
    $(document).ready(function() {
        $('#adCategory').submit(function(event) {
            event.preventDefault();
            var formData = $(this).serialize();

            $.ajax({
                url: categoryCreateRoute,
                type: "POST",
                data: formData,
                success: function(response) {
                    if(response.success) {
                        toastr.success(response.message);
                        console.log(response);
                        $(document).trigger('categoryAdded');
                        $('#adCategory')[0].reset();
                    } else {
                        toastr.error(response.message);
                        console.log(response);
                    }
                },
                error: function(xhr, status, error, message) {
                    var errorMessage = xhr.responseText ? JSON.parse(xhr.responseText).message : 'An error occurred';
                    toastr.error(errorMessage);
                }
            });
        });

        var dataTable = $('#categoryTable').DataTable({
            "ajax": {
                "url": categoryReadRoute,
                "type": "GET",
            },
            destroy: true,
            info: true,
            responsive: true,
            lengthChange: true,
            searching: true,
            paging: true,
            "columns": [
                {data: 'category_name'},
                {data: 'isICT',
                    render: function(data, type, row) {
                        if (type === 'display') {
                            return data == 1 ? 'Yes' : 'No';
                        } else {
                            return data;
                        }
                    },
                },
                {
                    data: 'id',
                    render: function(data, type, row) {
                        if (type === 'display') {
                            var buttons = '<button type="button" class="btn btn-sm btn-success btn-categoryedit mr-1 text-light" data-id="' + row.id + '" data-categoryname="' + row.category_name + '" data-isict="' + row.isICT + '" data-toggle="tooltip" data-placement="top" title="Edit Category.">';
                            buttons += '<i class="ti ti-pencil"></i> </button>' +'&nbsp;';
                            if (isAdmin || isProcurementOfficer || isChecker) {
                                buttons += '<button type="button" value="' + data + '" class="btn btn-sm btn-danger category-delete" data-toggle="tooltip" data-placement="top" title="Delete Category."><i class="ti ti-trash"></i> </button>';
                            }
                            return buttons;
                        } else {
                            return data;
                        }
                    },
                },
            ],
            "createdRow": function (row, data, index) {
                $(row).attr('id', 'tr-' + data.id);
            }
        });
        $(document).on('categoryAdded', function() {
            dataTable.ajax.reload();
        });

        dataTable.on('draw', function () {
            $('[data-toggle="tooltip"]').tooltip();
        });
    });

    $(document).on('click', '.btn-categoryedit', function() {
        var id = $(this).data('id');
        var categoryName = $(this).data('categoryname');
        var isIct = $(this).data('isict');

        $('#editCategoryId').val(id);
        $('#editCategoryName').val(categoryName);
        $('#editIsIct').val(isIct);

        $('#editCategoryModal').modal('show');
    });

    $('#editCategoryForm').submit(function(event) {
        event.preventDefault();
        var formData = $(this).serialize();

        $.ajax({
            url: categoryUpdateRoute,
            type: "POST",
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if(response.success) {
                    toastr.success(response.message);
                    $('#editCategoryModal').modal('hide');
                    $(document).trigger('categoryAdded');
                } else {
                    toastr.error(response.message);
                }
            },
            error: function(xhr, status, error, message) {
                var errorMessage = xhr.responseText ? JSON.parse(xhr.responseText).message : 'An error occurred';
                toastr.error(errorMessage);
            }
        });
    });
</script>
