<script>
    document.addEventListener("DOMContentLoaded", function () {
        var el = document.querySelector("#prSubmissionsChart");
        if (!el || typeof ApexCharts === "undefined") {
            return;
        }

        var months = window.prChartMonths || ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        var pendingData = window.prChartPending || [8, 12, 9, 14, 11, 16, 13, 18, 12, 15, 10, 9];
        var approvedData = window.prChartApproved || [22, 28, 25, 31, 29, 35, 33, 38, 30, 34, 27, 24];

        var options = {
            series: [
                { name: "Pending", data: pendingData },
                { name: "Approved", data: approvedData }
            ],
            chart: {
                type: "bar",
                height: 295,
                toolbar: { show: true },
                zoom: { enabled: false }
            },
            colors: ["#f59e0b", "#22c55e"],
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: "55%",
                    borderRadius: 4,
                    dataLabels: { position: "top" }
                }
            },
            dataLabels: {
                enabled: true,
                offsetY: -20,
                style: { fontSize: "11px", fontWeight: 600, colors: ["#304758"] }
            },
            stroke: { show: true, width: 2, colors: ["transparent"] },
            xaxis: {
                categories: months,
                title: { text: undefined }
            },
            yaxis: {
                title: { text: "No. of Requests" }
            },
            fill: { opacity: 1 },
            grid: { borderColor: "#e7e7e7", row: { colors: ["#f8fafc", "transparent"], opacity: 0.5 } },
            legend: { position: "top", horizontalAlign: "right" },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return val + " requests";
                    }
                }
            },
            responsive: [{
                breakpoint: 576,
                options: {
                    dataLabels: { enabled: false },
                    plotOptions: { bar: { columnWidth: "65%" } },
                    legend: { position: "bottom" }
                }
            }]
        };

        var chart = new ApexCharts(el, options);
        chart.render();
        window.prSubmissionsChart = chart;

        // Year filter: reload data without full page refresh.
        var yearSelect = document.getElementById("yearSelect");
        if (yearSelect) {
            yearSelect.addEventListener("change", function () {
                var year = this.value;
                // Keep URL in sync (?year=YYYY) so refresh keeps the selection.
                var url = new URL(window.location.href);
                url.searchParams.set("year", year);
                window.history.replaceState({}, "", url);

                fetch("{{ route('dashboard.chart-data') }}?year=" + encodeURIComponent(year), {
                    headers: { "X-Requested-With": "XMLHttpRequest", "Accept": "application/json" }
                })
                    .then(function (res) { return res.json(); })
                    .then(function (json) {
                        if (json.months) {
                            chart.updateOptions({ xaxis: { categories: json.months } }, false, false);
                        }
                        chart.updateSeries([
                            { name: "Pending", data: json.pending || [] },
                            { name: "Approved", data: json.approved || [] }
                        ]);
                    })
                    .catch(function () {
                        // Fallback: full reload with ?year= param handled server-side.
                        window.location.href = url.toString();
                    });
            });
        }
    });
</script>
