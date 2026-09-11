<script>
    toastr.options = {
        "closeButton": true,
        "progressBar": true,
        "positionClass": "toast-top-right"
    };

    // Native Vanilla JS Syntax Highlighter
    function syntaxHighlightJson(json) {
        if (typeof json !== 'string') {
            json = JSON.stringify(json, null, 4);
        } else {
            // Re-parse and format with 4-space indentation
            json = JSON.stringify(JSON.parse(json), null, 4);
        }

        // Escape special HTML characters to prevent XSS
        json = json.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');

        // Regex pattern to match JSON components
        return json.replace(/("(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*"(\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?)/g, function (match) {
            var cls = 'json-number';
            if (/^"/.test(match)) {
                if (/:$/.test(match)) {
                    cls = 'json-key';
                } else {
                    cls = 'json-string';
                }
            } else if (/true|false/.test(match)) {
                cls = 'json-boolean';
            } else if (/null/.test(match)) {
                cls = 'json-null';
            }
            return '<span class="' + cls + '">' + match + '</span>';
        });
    }

    $(document).ready(function() {
        // Initialize Audit DataTables
        var dataTable = $('#userauditTable').DataTable({
            "ajax": {
                "url": userAuditReadRoute,
                "type": "GET",
            },
            destroy: true,
            info: true,
            responsive: true,
            lengthChange: true,
            searching: true,
            paging: true,
            "columns": [
                {
                    data: 'username',
                    render: function(data) {
                        return '<span class="fw-semibold">' + (data || 'N/A') + '</span>';
                    }
                },
                {
                    data: 'action',
                    render: function(data) {
                        if (!data) {
                            return '<span class="badge bg-secondary">N/A</span>';
                        }
                        
                        // Formats "ADD_USER" to "ADD USER"
                        const formattedAction = data.replace(/_/g, ' ');
                        
                        return `<span class="badge bg-info-subtle text-info">${formattedAction}</span>`;
                    }
                },
                {
                    data: 'ip_address',
                    render: function(data) {
                        return '<code>' + (data || 'N/A') + '</code>';
                    }
                },
                {
                    data: 'user_agent',
                    render: function(data) {
                        return '<small class="text-muted">' + (data || 'N/A') + '</small>';
                    }
                },
                {
                    data: 'created_at',
                    render: function(data) {
                        if (!data) return 'N/A';
                        var date = new Date(data);
                        return '<small>' + date.toLocaleString('en-US', { 
                            month: 'short', 
                            day: '2-digit', 
                            year: 'numeric', 
                            hour: '2-digit', 
                            minute: '2-digit', 
                            hour12: true 
                        }) + '</small>';
                    }
                },
                {
                    data: 'actiondata',
                    render: function(data, type, row) {
                        var rawData = typeof data === 'object' ? JSON.stringify(data) : data;
                        return '<button type="button" class="btn btn-sm btn-success view-details-btn" ' +
                               'data-payload=\'' + (rawData || '{}') + '\' ' +
                               'data-bs-toggle="modal" data-bs-target="#auditUserDetailsModal">' +
                               '<i class="ti ti-code"></i> </button>';
                    }
                }
            ],
            "createdRow": function (row, data, index) {
                if (data.id) {
                    $(row).attr('id', 'tr-' + data.id); 
                }
            }
        });

        // Initialize Tooltips on Redraw
        dataTable.on('draw', function () {
            $('[data-toggle="tooltip"]').tooltip();
        });

        // Event Listener to Display Formatted JSON in Modal
        $(document).on('click', '.view-details-btn', function() {
            var rawData = $(this).attr('data-payload');
            try {
                // Highlight and inject styled HTML into the modal
                var highlightedJson = syntaxHighlightJson(rawData);
                $('#jsonPayloadDisplay').html(highlightedJson);
            } catch (e) {
                $('#jsonPayloadDisplay').text(rawData || 'No details available.');
            }
        });
    });
</script>