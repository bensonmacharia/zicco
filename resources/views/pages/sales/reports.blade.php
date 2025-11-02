@extends('adminlte::page')

@section('title', 'Sales Reports')

@section('content_header')
    <h1>Sales Reports</h1>
@stop

@section('content')
@php
$config = [
    "singleDatePicker" => true,
    "showDropdowns" => true,
    "startDate" => "js:moment()",
    "minYear" => 2020,
    "maxYear" => "js:parseInt(moment().format('YYYY'),10)",
    "timePicker" => false,
    "cancelButtonClasses" => "btn-danger",
    "locale" => ["format" => "YYYY-MM-DD"],
];
$months = [
    1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
    5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
    9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
];
$currentMonth = now()->month;
@endphp

<div class="row">
    <!-- DAILY REPORT -->
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-table mr-1"></i> {{ $displayDate }} Summary</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                </div>
            </div>

            <div class="card-body d-flex flex-column">
                @if ($errors->any())
                    <div class="alert alert-danger mb-3">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="form-group">
                    <label for="selected_date_picker" class="font-weight-bold">Select Date *</label>
                    <div>
                        {{-- adminlte date-range will render an input with id selected_date_picker --}}
                        <x-adminlte-date-range id="selected_date_picker" name="drSizeMd" igroup-size="md" :config="$config">
                            <x-slot name="appendSlot">
                                <div class="input-group-text"><i class="fas fa-calendar"></i></div>
                            </x-slot>
                        </x-adminlte-date-range>
                    </div>
                </div>

                <div class="table-responsive mt-3 mb-0">
                    <table id="daily-report-table" class="table table-hover table-sm text-nowrap mb-0">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Shop A</th>
                                <th>Shop B</th>
                                <th>Shop C</th>
                            </tr>
                        </thead>
                        <!-- tbody is injected by JS -->
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- MONTHLY REPORT -->
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-calendar mr-1"></i> {{ $displayMonth }} Summary</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                </div>
            </div>

            <div class="card-body d-flex flex-column">
                @if ($errors->any())
                    <div class="alert alert-danger mb-3">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="form-group">
                    <label for="selected_month" class="font-weight-bold">Select Month *</label>
                    <div>
                        <x-adminlte-select id="selected_month" name="month" igroup-size="md">
                            @foreach ($months as $num => $name)
                                <option value="{{ $num }}" {{ $num == $currentMonth ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </x-adminlte-select>
                    </div>
                </div>

                <div class="table-responsive mt-3 mb-0">
                    <table id="monthly-report-table" class="table table-hover table-sm text-nowrap mb-0">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Shop A</th>
                                <th>Shop B</th>
                                <th>Shop C</th>
                            </tr>
                        </thead>
                        <!-- tbody is injected by JS -->
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
    /* Keep cards equal height and align content */
    .card.h-100 {
        display: flex;
        flex-direction: column;
    }
    .card-body {
        flex: 1 1 auto;
        display: flex;
        flex-direction: column;
    }
    .form-group label { font-weight: 600; }
    .table th, .table td { vertical-align: middle !important; }
    /* Make the adminlte select and date input visually consistent height */
    .input-group .form-control, .input-group .custom-select, .input-group input {
        height: calc(2.25rem + 2px);
    }
    /* small spacing so tables align visually */
    .table-responsive { margin-top: 0.5rem; }
</style>
@stop

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Setup CSRF for AJAX
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

    // Prefill today's date in the date picker input
    let today = moment().format('YYYY-MM-DD');

    // adminlte date range component renders an input with id 'selected_date_picker'
    // Set its value if the input exists
    let $dateInput = $('#selected_date_picker');
    if ($dateInput.length) {
        // If the component renders an input inside, try to find the actual input element
        // x-adminlte-date-range often outputs an input with id same as component
        $dateInput.val(today);
    }

    // Trigger initial loads for both daily and monthly
    loadDailyForToday();
    loadMonthlyForCurrent();

    // -----------------------
    // Event listeners
    // -----------------------

    // Date picker: listen to apply.daterangepicker (commonly emitted by daterangepicker)
    // Fallback to 'change' if apply event doesn't exist.
    $(document).on('apply.daterangepicker', '#selected_date_picker', function(ev, picker) {
        // picker gives startDate; format as YYYY-MM-DD
        let date = picker.startDate.format('YYYY-MM-DD');
        $('#selected_date_picker').val(date);
        getSalesReportByDate(date);
    });

    // Some adminlte components might not fire apply.daterangepicker; add change listener too
    $(document).on('change', '#selected_date_picker', function() {
        let date = $(this).val();
        if (date) getSalesReportByDate(date);
    });

    // Month select: trigger when user selects a month
    $(document).on('change', '#selected_month', function() {
        let month = $(this).val();
        if (month) getSalesReportByMonth(month);
    });
});

// Helpers to load initial data
function loadDailyForToday() {
    let today = moment().format('YYYY-MM-DD');
    // set input if present
    if ($('#selected_date_picker').length) $('#selected_date_picker').val(today);
    getSalesReportByDate(today);
}

function loadMonthlyForCurrent() {
    let currentMonth = moment().month() + 1; // moment months 0..11
    if ($('#selected_month').length) $('#selected_month').val(currentMonth);
    getSalesReportByMonth(currentMonth);
}

// -----------------------
// AJAX functions
// -----------------------
function getSalesReportByDate(dateArg) {
    let selected_date = dateArg || $('#selected_date_picker').val();
    if (!selected_date) return;

    $.post("{{ route('sales.reports.by-date') }}", { drSizeMd: selected_date })
        .done(function(response) {
            if (response.error) {
                Swal.fire("Error!", response.error, "error");
                return;
            }

            // Build table tbody HTML (safe default to 0 if missing)
            let s = response.stats || { sales: [0,0,0], cost: [0,0,0], expenses: [0,0,0], profit: [0,0,0] };

            let tbody = `
                <tr>
                    <th>Total Sales</th>
                    <td>KES. ${s.sales[0] ?? '0'}</td>
                    <td>KES. ${s.sales[1] ?? '0'}</td>
                    <td>KES. ${s.sales[2] ?? '0'}</td>
                </tr>
                <tr>
                    <th>Total Cost</th>
                    <td>KES. ${s.cost[0] ?? '0'}</td>
                    <td>KES. ${s.cost[1] ?? '0'}</td>
                    <td>KES. ${s.cost[2] ?? '0'}</td>
                </tr>
                <tr>
                    <th>Total Expenses</th>
                    <td>KES. ${s.expenses[0] ?? '0'}</td>
                    <td>KES. ${s.expenses[1] ?? '0'}</td>
                    <td>KES. ${s.expenses[2] ?? '0'}</td>
                </tr>
                <tr>
                    <th>Total Profit</th>
                    <td>KES. ${s.profit[0] ?? '0'}</td>
                    <td>KES. ${s.profit[1] ?? '0'}</td>
                    <td>KES. ${s.profit[2] ?? '0'}</td>
                </tr>
            `;

            $('#daily-report-table tbody').remove();
            $('#daily-report-table').append(`<tbody>${tbody}</tbody>`);

            // update heading (first card)
            if (response.displayDate) {
                $('.card').first().find('.card-title').html('<i class="fas fa-table mr-1"></i> ' + response.displayDate + ' Summary');
            }
        })
        .fail(function() {
            Swal.fire("Error!", "Could not fetch daily report data", "error");
        });
}

function getSalesReportByMonth(monthArg) {
    let month = monthArg || $('#selected_month').val();
    if (!month) return;

    $.post("{{ route('sales.reports.by-month') }}", { month: month })
        .done(function(response) {
            if (response.error) {
                Swal.fire("Error!", response.error, "error");
                return;
            }

            // response.stats expected to be associative keyed by shop ids (1,2,3)
            let stats = response.stats || {};
            // helper to safely format integer -> locale string
            function fmt(x){ return (x || 0).toLocaleString ? (x || 0).toLocaleString() : x; }

            let tbody = `
                <tr>
                    <th>Total Sales</th>
                    <td>KES. ${fmt(stats[1]?.sales ?? 0)}</td>
                    <td>KES. ${fmt(stats[2]?.sales ?? 0)}</td>
                    <td>KES. ${fmt(stats[3]?.sales ?? 0)}</td>
                </tr>
                <tr>
                    <th>Total Cost</th>
                    <td>KES. ${fmt(stats[1]?.cost ?? 0)}</td>
                    <td>KES. ${fmt(stats[2]?.cost ?? 0)}</td>
                    <td>KES. ${fmt(stats[3]?.cost ?? 0)}</td>
                </tr>
                <tr>
                    <th>Total Expenses</th>
                    <td>KES. ${fmt(stats[1]?.expenses ?? 0)}</td>
                    <td>KES. ${fmt(stats[2]?.expenses ?? 0)}</td>
                    <td>KES. ${fmt(stats[3]?.expenses ?? 0)}</td>
                </tr>
                <tr>
                    <th>Total Profit</th>
                    <td>KES. ${fmt(stats[1]?.profit ?? 0)}</td>
                    <td>KES. ${fmt(stats[2]?.profit ?? 0)}</td>
                    <td>KES. ${fmt(stats[3]?.profit ?? 0)}</td>
                </tr>
            `;

            $('#monthly-report-table tbody').remove();
            $('#monthly-report-table').append(`<tbody>${tbody}</tbody>`);

            if (response.displayMonth) {
                // update second card title
                $('.card').eq(1).find('.card-title').html('<i class="fas fa-calendar mr-1"></i> ' + response.displayMonth + ' Summary');
            }
        })
        .fail(function() {
            Swal.fire("Error!", "Could not fetch monthly report data", "error");
        });
}
</script>
@stop