<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') | {{ config('app.name', 'Laravel') }}</title>
{{--    <link rel="icon" type="image/x-icon" href="{{ asset('img/favicon.ico') }}">--}}
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="{{ asset('themes/backend/plugins/fontawesome-free/css/all.min.css') }}">
    <!-- iCheck for checkboxes and radio inputs -->
    <link rel="stylesheet" href="{{ asset('themes/backend/plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="{{ asset('themes/backend/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}">
    <!-- Toastr -->
    <link rel="stylesheet" href="{{ asset('themes/backend/plugins/toastr/toastr.min.css') }}">
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('themes/backend/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('themes/backend/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('themes/backend/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
    <!-- iCheck for checkboxes and radio inputs -->
    <link rel="stylesheet" href="{{ asset('themes/backend/plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
    <!-- Bootstrap Color Picker -->
    <link rel="stylesheet" href="{{ asset('themes/backend/plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.min.css') }}">
    <!-- Tempusdominus Bootstrap 4 -->
    <link rel="stylesheet" href="{{ asset('themes/backend/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('themes/backend/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('themes/backend/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">

    <!-- bootstrap datepicker -->
    <link rel="stylesheet" href="{{ asset('themes/backend/plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">

    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('themes/backend/dist/css/adminlte.min.css') }}">
    <style>
        @media (min-width: 768px){
            .col-form-label{
                text-align: right;
            }
        }

        .form-group.has-error label {
            color: #dd4b39;
        }
        .form-group.has-error .form-control, .form-group.has-error .input-group-addon {
            border-color: #dd4b39;
            box-shadow: none;
        }
        .form-group.has-error .help-block {
            color: #dd4b39;
        }
        .help-block {
            display: block;
            margin-top: 5px;
            margin-bottom: 10px;
        }
        .toast{
            min-width: 300px;
        }
        .select2{
            width: 100% !important;
        }
        .form-group.has-error .select2-container span.selection span.select2-selection.select2-selection--single {
            border-color: #dd4b39;
            box-shadow: none;
        }
        .input-group.date-time.has-error .form-control {
            border-color: #dd4b39;
            box-shadow: none;
        }

        .input-group.date-time.has-error > .help-block {
            color: #dd4b39;
        }
        .content-header h1 {
            font-size: 1.5rem;
        }
        .content-header {
            padding: 5px .5rem;
        }
        .brand-link {
            line-height: 1.9;
        }

        .card-primary.card-outline {
            border-top: 3px solid #E1694C;
        }
        .btn-primary {
            background-color: #E1694C;
            border-color: #E1694C;
        }
        .btn-primary:hover{
            background-color: #E1694C;
            border-color: #E1694C;
        }

        .sidebar-dark-primary .nav-sidebar>.nav-item>.nav-link.active, .sidebar-light-primary .nav-sidebar>.nav-item>.nav-link.active {
            background-color: #E1694C;
        }
        a {
            color: #E1694C;
        }
        .brand-link {
            line-height: 1.5;
        }
        .btn-primary:not(:disabled):not(.disabled).active, .btn-primary:not(:disabled):not(.disabled):active, .show>.btn-primary.dropdown-toggle {
            background-color: #E1694C;
            border-color: #E1694C;
        }
        .navbar-light .navbar-nav .nav-link {
            color: rgb(0 159 75);
        }
        .dropdown-item.active, .dropdown-item:active {
            background-color: #E1694C;
        }
        .navbar-light .navbar-nav .nav-link:focus, .navbar-light .navbar-nav .nav-link:hover {
            color: rgb(0 159 75);
        }

        .bg-gradient-primary {
            background: #E1694C linear-gradient(180deg,#E1694C,#E1694C) repeat-x!important;
            color: #fff;
        }
        .bg-gradient-primary.btn.active, .bg-gradient-primary.btn:active, .bg-gradient-primary.btn:not(:disabled):not(.disabled).active, .bg-gradient-primary.btn:not(:disabled):not(.disabled):active {
            background: #E1694C linear-gradient(180deg,#E1694C,#E1694C) repeat-x!important;
            border-color: #E1694C;
        }
        .bg-gradient-primary.btn:hover {
            background: #E1694C linear-gradient(180deg,#E1694C,#E1694C) repeat-x!important;
            border-color: #E1694C;
        }
        .btn-primary.focus, .btn-primary:focus {
            background-color: #E1694C;
            border-color: #E1694C;
            box-shadow: 0 0 0 0 rgb(0 159 75);
        }
        .select2-container--default .select2-results__option--highlighted[aria-selected], .select2-container--default .select2-results__option--highlighted[aria-selected]:hover {
            background-color: #E1694C;
        }
        .datepicker table tr td.active, .datepicker table tr td.active.disabled, .datepicker table tr td.active.disabled:hover, .datepicker table tr td.active:hover {
            background-color: #E1694C;
            background-image: -moz-linear-gradient(to bottom,#E1694C,#E1694C);
            background-image: -ms-linear-gradient(to bottom,#E1694C,#E1694C);
            background-image: -webkit-gradient(linear,0 0,0 100%,from(#E1694C),to(#E1694C));
            background-image: -webkit-linear-gradient(to bottom,#E1694C,#E1694C);
            background-image: -o-linear-gradient(to bottom,#E1694C,#E1694C);
            background-image: linear-gradient(to bottom,#E1694C,#E1694C);
            background-repeat: repeat-x;
            filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#08c', endColorstr='#0044cc', GradientType=0);
            border-color: #E1694C #E1694C #E1694C;
            border-color: rgb(0 159 75) rgb(2, 160, 76) rgb(2, 160, 76);
            filter: progid:DXImageTransform.Microsoft.gradient(enabled=false);
            color: #fff;
            text-shadow: 0 -1px 0 rgb(2, 160, 76);
        }

        .datepicker table tr td.active.active, .datepicker table tr td.active.disabled, .datepicker table tr td.active.disabled.active, .datepicker table tr td.active.disabled.disabled, .datepicker table tr td.active.disabled:active, .datepicker table tr td.active.disabled:hover, .datepicker table tr td.active.disabled:hover.active, .datepicker table tr td.active.disabled:hover.disabled, .datepicker table tr td.active.disabled:hover:active, .datepicker table tr td.active.disabled:hover:hover, .datepicker table tr td.active.disabled:hover[disabled], .datepicker table tr td.active.disabled[disabled], .datepicker table tr td.active:active, .datepicker table tr td.active:hover, .datepicker table tr td.active:hover.active, .datepicker table tr td.active:hover.disabled, .datepicker table tr td.active:hover:active, .datepicker table tr td.active:hover:hover, .datepicker table tr td.active:hover[disabled], .datepicker table tr td.active[disabled] {
            background-color: #E1694C;
        }

        fieldset {
            display: block;
            margin-inline-start: 2px;
            margin-inline-end: 2px;
            padding-block-start: 0.35em;
            padding-inline-start: 0.75em;
            padding-inline-end: 0.75em;
            padding-block-end: 0.625em;
            min-inline-size: min-content;
            border-width: 2px;
            border-style: groove;
            border-color: threedface;
            border-image: initial;
            padding-bottom: 0;
        }
        legend {
            width: auto;
            margin-bottom: 0;
        }
        table.dataTable.dtr-inline.collapsed>tbody>tr>td.dtr-control:before, table.dataTable.dtr-inline.collapsed>tbody>tr>th.dtr-control:before {
            background-color: #E1694C;
        }
        [class*=sidebar-dark] .brand-link, [class*=sidebar-dark] .brand-link .pushmenu {
            color: rgb(0 159 75);
            background: #fff;
        }
        [class*=sidebar-dark] .brand-link .pushmenu:hover, [class*=sidebar-dark] .brand-link:hover {
            color: #E1694C;
        }
        .nav-link {
            padding: .5rem .5rem;
        }
        .layout-navbar-fixed .wrapper .sidebar-dark-primary .brand-link:not([class*=navbar]) {
            background-color: #ffffff;
        }
        body{
            overflow-x: hidden;
        }
        .img-circle {
            border-radius: 0%;
        }
        .navbar-light .navbar-nav .nav-link {
            color: #f58655;
        }
        .navbar-light .navbar-nav .nav-link:focus, .navbar-light .navbar-nav .nav-link:hover {
            color: #f58655;
        }
        [class*=sidebar-dark] .brand-link, [class*=sidebar-dark] .brand-link .pushmenu {
            color: #fd7e14;
            background: #fff;
        }
        [class*=sidebar-dark] .brand-link .pushmenu:hover, [class*=sidebar-dark] .brand-link:hover {
            color: #E1694C;
        }
        .sidebar-dark-primary .nav-sidebar>.nav-item>.nav-link.active, .sidebar-light-primary .nav-sidebar>.nav-item>.nav-link.active {
            background-color: #E1694C;
        }
        .sidebar-dark-primary .nav-sidebar>.nav-item>.nav-link.active, .sidebar-light-primary .nav-sidebar>.nav-item>.nav-link.active {
            background-color: #E1694C;
        }
        .bg-gradient-primary {
            background: #E1694C linear-gradient(
                180deg
                ,#E1694C,#E1694C) repeat-x!important;
            color: #fff;
        }
        .btn-primary {
            background-color: #E1694C;
            border-color: #E1694C;
        }
        .bg-gradient-primary.btn:hover {
            background: #E1694C linear-gradient(
                180deg
                ,#E1694C,#E1694C) repeat-x!important;
            border-color: #E1694C;
        }
        .bg-gradient-primary.btn.active, .bg-gradient-primary.btn:active, .bg-gradient-primary.btn:not(:disabled):not(.disabled).active, .bg-gradient-primary.btn:not(:disabled):not(.disabled):active {
            background: #E1694C linear-gradient(
                180deg
                ,#E1694C,#E1694C) repeat-x!important;
            border-color: #E1694C;
        }
        .btn-primary:hover {
            background-color: #E1694C;
            border-color: #E1694C;
        }
        .card-primary.card-outline {
            border-top: 3px solid #E1694C;
        }
        .btn-success {
            color: #fff;
            background-color: #E1694C;
            border-color: #E1694C;
            box-shadow: none;
        }
        .btn-success:hover {
            color: #fff;
            background-color: #E1694C;
            border-color: #E1694C;
        }
        .page-item.active .page-link {
            background-color: #E1694C!important;
            border-color: #E1694C!important;
        }
        a {
            color: #E1694C;
        }
        .bg-success {
            background-color: #E1694C!important;
        }
        .dropdown-item.active, .dropdown-item:active {
            background-color: #E1694C;
        }
        .btn-primary:not(:disabled):not(.disabled).active, .btn-primary:not(:disabled):not(.disabled):active, .show>.btn-primary.dropdown-toggle {
            background-color: #E1694C!important;
            border-color: #E1694C!important;
        }
        .brand-text{
            margin-left: 25px;
        }

    </style>
    @yield('style')
</head>
<body class="sidebar-mini">
<div class="wrapper">

    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <!-- Left navbar links -->
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>
            <li class="nav-item d-none d-sm-inline-block">
                <h3  class="nav-link font-weight-bold active" style="color: #E1694C;font-size: 22px;margin: 0">
                    {{ config('app.name') }}
                </h3>
            </li>
        </ul>

        <!-- Right navbar links -->
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                    <i class="fas fa-expand-arrows-alt"></i>
                </a>
            </li>
            <li class="nav-item dropdown">
                @php
                    $product = \App\Models\Booking::where('remake_request',1)->count();
                @endphp

                <a class="nav-link" data-toggle="dropdown" href="#">
                    <i class="far fa-bell" style="color: green"></i>
                    <span class="badge badge-success navbar-badge">{{$product}}</span>
                </a>

                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                    <span class="dropdown-item dropdown-header">Remake Request</span>
                    @foreach(\App\Models\Booking::where('remake_request',1)->limit(30)->get() as $notification)
                            <a href="{{route('assign_order_process',['booking'=>$notification->id])}}" class="dropdown-item">
                                <p class="text-danger"><b>Booking No : {{$notification->order_no ?? ''}}</b>-{{$notification->customer->name}}</p></a>
                            <div class="dropdown-divider"></div>
                    @endforeach
                </div>
            </li>
            <li class="nav-item dropdown">
                @php
                    $count=0;
                @endphp
                @foreach(\App\Models\Inventory::groupBy('product_id')->select(DB::raw('sum(`quantity`) as quantity, product_id'))->inRandomOrder()->limit(30)->get() as $notification)
                    @php
                        $product = \App\Models\Product::where('id',$notification->product_id)->first();
                    @endphp
                    @if($notification->quantity<$product->warning_quantity)
                        @php
                            $count++;
                        @endphp
                    @endif
                @endforeach

                @php
                    $count1=0;
                @endphp
                @foreach(\App\Models\Booking::where('warning_date','<', Carbon\Carbon::now())->where('delivery_date','>=', Carbon\Carbon::now())->get() as $warning)
                    @php
                        $count1++;
                    @endphp
                @endforeach

                <a class="nav-link" data-toggle="dropdown" href="#">
                    <i class="far fa-bell"></i>
                    <span class="badge badge-warning navbar-badge">{{$count+$count1}}</span>
                </a>

                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                    <span class="dropdown-item dropdown-header"> {{$count+$count1}} Notifications</span>
                @foreach(\App\Models\Inventory::groupBy('product_id')->select(DB::raw('sum(`quantity`) as quantity, product_id'))->inRandomOrder()->limit(30)->get() as $notification)
                        @php
                            $product = \App\Models\Product::where('id',$notification->product_id)->first();
                        @endphp
                      @if($notification->quantity<$product->warning_quantity)
                        <a href="#" class="dropdown-item">
                        <p class="text-danger"><b>{{$product->name ?? ''}}</b> only <b>{{$notification->quantity ?? ''}}</b> left in stock</p></a>
                        <div class="dropdown-divider"></div>
                      @endif
                @endforeach
                @foreach(\App\Models\Booking::where('warning_date','<=', Carbon\Carbon::now())->where('delivery_date','>=', Carbon\Carbon::now())->get() as $booking)
                    <a href="{{route('booking_details',['booking'=>$booking->id])}}" target="_blank" class="dropdown-item">
                        <p class="text-warning">Booking order <b>{{$booking->order_no}}</b> Delivery date <b>{{\Carbon\Carbon::parse($booking->delivery_date)->format('d-m-Y')}}</b></p></a>
                    <div class="dropdown-divider"></div>
                @endforeach
                </div>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link" data-toggle="dropdown" href="#" aria-expanded="false">
                     {{ Auth::user()->name }}
                </a>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right" style="left: inherit; right: 0px;">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <a href="{{ route('logout') }}" class="dropdown-item"  onclick="event.preventDefault();
                                                this.closest('form').submit();">
                            <i class="fas fa-sign-out-alt mr-2"></i> Sign Out
                        </a>
                    </form>
                </div>
            </li>
        </ul>
    </nav>
    <!-- /.navbar -->

    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <!-- Brand Logo -->
        <a href="{{ route('dashboard') }}" class="brand-link">
{{--            <img src="{{ asset('img/logo.png') }}" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">--}}
{{--            <br>--}}
            <span class="brand-text font-weight-light"><b>SAFETY</b>MARK</span>
        </a>

        <!-- Sidebar -->
        <div class="sidebar os-host os-theme-light os-host-overflow os-host-overflow-y os-host-resize-disabled os-host-scrollbar-horizontal-hidden os-host-transition">

            <!-- Sidebar Menu -->
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                    <li class="nav-item">
                        <a href="{{ route('dashboard') }}" class="nav-link {{ Route::currentRouteName() == 'dashboard' ? 'active' : '' }}">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('product_status') }}" class="nav-link {{ Route::currentRouteName() == 'product_status' ? 'active' : '' }}">
                            <i class="nav-icon fas fa-th"></i>
                            <p>Product Status</p>
                        </a>
                    </li>
                    <?php
                    $subMenu = [
                        'zone', 'zone.add', 'zone.edit',
                        'dealer', 'dealer.add', 'dealer.edit',
                        'distributor', 'distributor.add', 'distributor.edit',
                        'discount_rate', 'discount_rate.add', 'discount_rate.edit',
                        'product_type', 'product_type.add', 'product_type.edit',
                        'commission_type', 'commission_type.add', 'commission_type.edit',
                        'user.all', 'user.add', 'user.edit',
                    ];
                    ?>
                    @if(Auth::user()->role == 2)
                        <?php
                        $menu = [
                            'assign_receipt.all','assign_receipt.details','booking_details','assign_order_process'
                        ];
                        ?>
                        <li class="nav-item {{ in_array(Route::currentRouteName(), $menu) ? 'menu-open' : '' }}">
                            <a href="#"
                               class="nav-link {{ in_array(Route::currentRouteName(), $menu) ? 'active' : '' }}">
                                <i class="nav-icon fas fa-list"></i>
                                <p>
                                    Assign Order
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <?php
                                $subMenu = ['assign_receipt.all','assign_receipt.details','booking_details','assign_order_process'];
                                ?>
                                <li class="nav-item">
                                    <a href="{{ route('assign_receipt.all') }}"
                                       class="nav-link {{ in_array(Route::currentRouteName(), $subMenu)  ? 'active' : '' }}">
                                        <i class="far  {{  in_array(Route::currentRouteName(), $subMenu)  ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                        <p>Order</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    @endif
                    @if(Auth::user()->role == 1)

                    <li class="nav-item {{ in_array(Route::currentRouteName(), $subMenu) ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link {{ in_array(Route::currentRouteName(), $subMenu) ? 'active' : '' }}">
                            <i class="nav-icon fas fa-list"></i>
                            <p>
                                Administrator
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <?php
                            $subSubMenu = [
                                'user.all', 'user.add', 'user.edit',
                            ];
                            ?>
                            <li class="nav-item">
                                <a href="{{ route('user.all') }}" class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'active' : '' }}">
                                    <i class="far  {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                    <p>User</p>
                                </a>
                            </li>

                        </ul>

                        <?php
                        $secoundSubMenu = [
                            'employee_type', 'employee_type.add', 'employee_type.edit',
                            'employee', 'employee.add', 'employee.edit',
                        ];
                        ?>

                    </li>

                        <?php
                        $subMenu = [
                            'account_head', 'account_head.add', 'account_head.edit','section_number_of_tds','section_number_of_tds.add','section_number_of_tds.edit',
                            'balance_transfer', 'balance_transfer.add', 'balance_transfer.edit', 'balance_transfer_voucher_details', 'balance_transfer_receipt_details',
                            'bank_voucher', 'bank_voucher.create', 'bank_voucher.edit', 'bank_voucher_details','cash_voucher', 'cash_voucher.create', 'cash_voucher.edit', 'cash_voucher_details',
                            'cheque_receipt', 'cheque_receipt.create', 'cheque_receipt.edit', 'cheque_receipt_details','cash_receipt', 'cash_receipt.create','cash_receipt.edit', 'cash_receipt_details',
                            'journal_voucher', 'journal_voucher.create', 'journal_voucher.edit', 'journal_voucher_details','report.ledger','report.trail_balance','report.receive_and_payment'
                        ];
                        ?>

                    <li class="nav-item {{ in_array(Route::currentRouteName(), $subMenu) ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link {{ in_array(Route::currentRouteName(), $subMenu) ? 'active' : '' }}">
                            <i class="nav-icon fa fa-columns"></i>
                            <p>
                                Accounts
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <?php
                            $subSubMenu = [
                                'account_head', 'account_head.add', 'account_head.edit'
                            ];
                            ?>
                            <li class="nav-item">
                                <a href="{{ route('account_head') }}" class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'active' : '' }}">
                                    <i class="far {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                    <p>Account Heads</p>
                                </a>
                            </li>
{{--                                <?php--}}
{{--                                $subMenu = ['section_number_of_tds','section_number_of_tds.add','section_number_of_tds.edit'];--}}
{{--                                ?>--}}
{{--                                <li class="nav-item">--}}
{{--                                    <a href="{{ route('section_number_of_tds') }}"--}}
{{--                                       class="nav-link {{ in_array(Route::currentRouteName(), $subMenu)  ? 'active' : '' }}">--}}
{{--                                        <i class="far  {{  in_array(Route::currentRouteName(), $subMenu)  ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>--}}
{{--                                        <p>Section Number of TDS</p>--}}
{{--                                    </a>--}}
{{--                                </li>--}}
                                <?php
                                $subMenu = ['balance_transfer', 'balance_transfer.add', 'balance_transfer.edit', 'balance_transfer_voucher_details', 'balance_transfer_receipt_details'];
                                ?>
                                <li class="nav-item {{ in_array(Route::currentRouteName(), $subMenu) ? 'menu-open' : '' }}">
                                    <a href="#"
                                       class="nav-link {{ in_array(Route::currentRouteName(), $subMenu) ? 'active' : '' }}">
                                        <i class="nav-icon fas fa-receipt"></i>
                                        <p>
                                            Balance Transfer
                                            <i class="right fas fa-angle-left"></i>
                                        </p>
                                    </a>
                                    <ul class="nav nav-treeview">
                                        <?php
                                        $subSubMenu = ['balance_transfer.add'];
                                        ?>
                                        <li class="nav-item">
                                            <a href="{{ route('balance_transfer.add') }}"
                                               class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'active' : '' }}">
                                                <i class="far  {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                                <p>Add Balance Transfer</p>
                                            </a>
                                        </li>
                                        <?php
                                        $subSubMenu = ['balance_transfer', 'balance_transfer.edit'];
                                        ?>
                                        <li class="nav-item">
                                            <a href="{{ route('balance_transfer') }}"
                                               class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'active' : '' }}">
                                                <i class="far {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                                <p>Balance Transfer List</p>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                <?php
                                $subMenu = ['bank_voucher', 'bank_voucher.create', 'bank_voucher.edit', 'bank_voucher_details'];
                                ?>
                                <li class="nav-item {{ in_array(Route::currentRouteName(), $subMenu) ? 'menu-open' : '' }}">
                                    <a href="#"
                                       class="nav-link {{ in_array(Route::currentRouteName(), $subMenu) ? 'active' : '' }}">
                                        <i class="nav-icon fas fa-receipt"></i>
                                        <p>
                                            Bank Voucher(BV)
                                            <i class="right fas fa-angle-left"></i>
                                        </p>
                                    </a>
                                    <ul class="nav nav-treeview">
                                        <?php
                                        $subSubMenu = ['bank_voucher.create'];
                                        ?>
                                        <li class="nav-item">
                                            <a href="{{ route('bank_voucher.create') }}"
                                               class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'active' : '' }}">
                                                <i class="far  {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                                <p>Create Bank Voucher(BV)</p>
                                            </a>
                                        </li>
                                        <?php
                                        $subSubMenu = ['bank_voucher', 'bank_voucher.edit', 'bank_voucher_details'];
                                        ?>
                                        <li class="nav-item">
                                            <a href="{{ route('bank_voucher') }}"
                                               class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'active' : '' }}">
                                                <i class="far {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                                <p>Bank Voucher(BV) Lists</p>
                                            </a>
                                        </li>


                                    </ul>
                                </li>
                                <?php
                                $subMenu = ['cash_voucher', 'cash_voucher.create', 'cash_voucher.edit', 'cash_voucher_details'];
                                ?>
                                <li class="nav-item {{ in_array(Route::currentRouteName(), $subMenu) ? 'menu-open' : '' }}">
                                    <a href="#"
                                       class="nav-link {{ in_array(Route::currentRouteName(), $subMenu) ? 'active' : '' }}">
                                        <i class="nav-icon fas fa-receipt"></i>
                                        <p>
                                            Cash Voucher(CV)
                                            <i class="right fas fa-angle-left"></i>
                                        </p>
                                    </a>
                                    <ul class="nav nav-treeview">
                                        <?php
                                        $subSubMenu = ['cash_voucher.create'];
                                        ?>
                                        <li class="nav-item">
                                            <a href="{{ route('cash_voucher.create') }}"
                                               class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'active' : '' }}">
                                                <i class="far  {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                                <p>Create Cash Voucher(CV)</p>
                                            </a>
                                        </li>
                                        <?php
                                        $subSubMenu = ['cash_voucher', 'cash_voucher.edit', 'cash_voucher_details'];
                                        ?>
                                        <li class="nav-item">
                                            <a href="{{ route('cash_voucher') }}"
                                               class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'active' : '' }}">
                                                <i class="far {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                                <p>Cash Voucher(CV) Lists</p>
                                            </a>
                                        </li>
                                    </ul>
                                </li>

                                <?php
                                $subMenu = ['cheque_receipt', 'cheque_receipt.create', 'cheque_receipt.edit', 'cheque_receipt_details'];
                                ?>
                                <li class="nav-item {{ in_array(Route::currentRouteName(), $subMenu) ? 'menu-open' : '' }}">
                                    <a href="#"
                                       class="nav-link {{ in_array(Route::currentRouteName(), $subMenu) ? 'active' : '' }}">
                                        <i class="nav-icon fas fa-receipt"></i>
                                        <p>
                                            Cheque Receipt(CR)
                                            <i class="right fas fa-angle-left"></i>
                                        </p>
                                    </a>
                                    <ul class="nav nav-treeview">
                                        <?php
                                        $subSubMenu = ['cheque_receipt.create'];
                                        ?>
                                        <li class="nav-item">
                                            <a href="{{ route('cheque_receipt.create') }}"
                                               class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'active' : '' }}">
                                                <i class="far  {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                                <p>Create Cheque Receipt(CR)</p>
                                            </a>
                                        </li>
                                        <?php
                                        $subSubMenu = ['cheque_receipt', 'cheque_receipt.edit', 'cheque_receipt_details'];
                                        ?>
                                        <li class="nav-item">
                                            <a href="{{ route('cheque_receipt') }}"
                                               class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'active' : '' }}">
                                                <i class="far {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                                <p>Cheque Receipt(CR) Lists</p>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                <?php
                                $subMenu = ['cash_receipt', 'cash_receipt.create', 'cash_receipt.edit', 'cash_receipt_details'];
                                ?>
                                <li class="nav-item {{ in_array(Route::currentRouteName(), $subMenu) ? 'menu-open' : '' }}">
                                    <a href="#"
                                       class="nav-link {{ in_array(Route::currentRouteName(), $subMenu) ? 'active' : '' }}">
                                        <i class="nav-icon fas fa-receipt"></i>
                                        <p>
                                            Cash Receipt(CR)
                                            <i class="right fas fa-angle-left"></i>
                                        </p>
                                    </a>
                                    <ul class="nav nav-treeview">
                                        <?php
                                        $subSubMenu = ['cash_receipt.create', 'cash_receipt_details'];
                                        ?>
                                        <li class="nav-item">
                                            <a href="{{ route('cash_receipt.create') }}"
                                               class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'active' : '' }}">
                                                <i class="far  {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                                <p>Create Cash Receipt(CR)</p>
                                            </a>
                                        </li>
                                        <?php
                                        $subSubMenu = ['cash_receipt', 'cash_receipt.edit'];
                                        ?>
                                        <li class="nav-item">
                                            <a href="{{ route('cash_receipt') }}"
                                               class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'active' : '' }}">
                                                <i class="far {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                                <p>Cash Receipt(CR) Lists</p>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                <?php
                                $subMenu = ['journal_voucher', 'journal_voucher.create', 'journal_voucher.edit', 'journal_voucher_details'];
                                ?>
                                <li class="nav-item {{ in_array(Route::currentRouteName(), $subMenu) ? 'menu-open' : '' }}">
                                    <a href="#"
                                       class="nav-link {{ in_array(Route::currentRouteName(), $subMenu) ? 'active' : '' }}">
                                        <i class="nav-icon fas fa-receipt"></i>
                                        <p>
                                            Journal Voucher (JV)
                                            <i class="right fas fa-angle-left"></i>
                                        </p>
                                    </a>
                                    <ul class="nav nav-treeview">
                                        <?php
                                        $subSubMenu = ['journal_voucher.create'];
                                        ?>
                                        <li class="nav-item">
                                            <a href="{{ route('journal_voucher.create') }}"
                                               class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'active' : '' }}">
                                                <i class="far  {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                                <p>Create journal Voucher (JV)</p>
                                            </a>
                                        </li>

                                        <?php
                                        $subSubMenu = ['journal_voucher','journal_voucher', 'journal_voucher.edit', 'journal_voucher_details',];
                                        ?>
                                        <li class="nav-item">
                                            <a href="{{ route('journal_voucher') }}"
                                               class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'active' : '' }}">
                                                <i class="far {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                                <p>Journal Voucher(JV) Lists</p>
                                            </a>
                                        </li>
                                    </ul>
                                </li>

                                <?php
                                $subSubMenu = [
                                    'report.ledger'
                                ];
                                ?>
                                <li class="nav-item">
                                    <a href="{{ route('report.ledger') }}" class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'active' : '' }}">
                                        <i class="far {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                        <p>Ledger</p>
                                    </a>
                                </li>

                                <?php
                                $subSubMenu = [
                                    'report.trail_balance'
                                ];
                                ?>
                                <li class="nav-item">
                                    <a href="{{ route('report.trail_balance') }}" class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'active' : '' }}">
                                        <i class="far {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                        <p>Trial Balance</p>
                                    </a>
                                </li>

                                <?php
                                $subSubMenu = [
                                    'report.receive_and_payment'
                                ];
                                ?>
                                <li class="nav-item">
                                    <a href="{{ route('report.receive_and_payment') }}" class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'active' : '' }}">
                                        <i class="far {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                        <p>Receive & Payment</p>
                                    </a>
                                </li>


                        </ul>
                    </li>

                    <?php
                    $subMenu = [
                        'product_category', 'product_category.add', 'product_category.edit',
                        'all_product','product_add','product.edit','unit', 'unit.add', 'unit.edit',
                        'product_category', 'product_category.add', 'product_category.edit',
                        'all_product','product_add','product.edit','unit', 'unit.add', 'unit.edit',
                    ];
                    ?>
                    <li class="nav-item {{ in_array(Route::currentRouteName(), $subMenu) ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link {{ in_array(Route::currentRouteName(), $subMenu) ? 'active' : '' }}">
                            <i class="nav-icon fas fa-list"></i>
                            <p>
                                Product Manage
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <?php
                            $subSubMenu = [
                                'unit', 'unit.add', 'unit.edit',
                            ];
                            ?>
                            <li class="nav-item">
                                <a href="{{ route('unit') }}" class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'active' : '' }}">
                                    <i class="far  {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                    <p>Unit</p>
                                </a>
                            </li>
                            <?php
                            $subSubMenu = [
                                'product_category', 'product_category.add', 'product_category.edit',
                            ];
                            ?>
                            <li class="nav-item">
                                <a href="{{ route('product_category') }}" class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'active' : '' }}">
                                    <i class="far  {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                    <p>Category</p>
                                </a>
                            </li>

                            <?php
                                $subSubMenu = [
                                    'all_product','product_add','product.edit'
                                ];
                            ?>
                            <li class="nav-item">
                                <a href="{{ route('all_product') }}" class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'active' : '' }}">
                                    <i class="far  {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                    <p>Product</p>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <?php
                        $subMenu = [
                            'warning_product',
                        ];
                    ?>
                    <li class="nav-item {{ in_array(Route::currentRouteName(), $subMenu) ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link {{ in_array(Route::currentRouteName(), $subMenu) ? 'active' : '' }}">
                            <i class="nav-icon fas fa-list"></i>
                            <p>
                               Market List
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <?php
                                $subSubMenu = [
                                    'warning_product',
                                ];
                            ?>
                            <li class="nav-item">
                                <a href="{{ route('warning_product') }}" class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'active' : '' }}">
                                    <i class="far  {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                    <p> Market List Product </p>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <?php
                        $subMenu = [
                            'supplier', 'supplier.add', 'supplier.edit',
                            'purchase_order.create','purchase_receipt.all','purchase_receipt.details',
                            'purchase_receipt.print','purchase_receipt.payment_details','purchase_journal_voucher.details',
                            'purchase_receipt.payment_print','inventory.all','inventory.details','purchase.return','purchase_return_receipt.all','purchase_return_receipt.details',
                            'supplier_payment.all','purchase_receipt.payment_details','purchase_receipt.edit','die_no', 'die_no.add', 'die_no.edit','purchase_return_payment_all_details',
                            'supplier_payment.order_details'
                        ];
                    ?>
                    <li class="nav-item {{ in_array(Route::currentRouteName(), $subMenu) ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link {{ in_array(Route::currentRouteName(), $subMenu) ? 'active' : '' }}">
                            <i class="nav-icon fas fa-list"></i>
                            <p>
                                Purchase
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <?php
                            $subSubMenu = [
                                'supplier', 'supplier.add', 'supplier.edit',
                            ];
                            ?>
                            <li class="nav-item">
                                <a href="{{ route('supplier') }}" class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'active' : '' }}">
                                    <i class="far  {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                    <p>Supplier</p>
                                </a>
                            </li>

                            <?php
                                $subSubMenu = [
                                    'purchase_order.create',
                                ];
                            ?>
                            <li class="nav-item">
                                <a href="{{ route('purchase_order.create') }}" class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'active' : '' }}">
                                    <i class="far  {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                    <p>Purchase Order</p>
                                </a>
                            </li>

                            <?php
                                $subSubMenu = [
                                    'purchase_receipt.all','purchase_receipt.details',
                                    'purchase_receipt.print','purchase_receipt.payment_details',
                                    'purchase_receipt.payment_print','purchase_receipt.edit','purchase_journal_voucher.details'
                                ];
                            ?>
                            <li class="nav-item">
                                <a href="{{ route('purchase_receipt.all') }}" class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'active' : '' }}">
                                    <i class="far  {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                    <p>Receipt</p>
                                </a>
                            </li>
                                <?php
                                $subSubMenu = [
                                    'purchase_return_receipt.all','purchase.return','purchase_return_receipt.details','purchase_return_payment_all_details','supplier_payment.order_details'
                                ];
                                ?>
                                <li class="nav-item">
                                    <a href="{{ route('purchase_return_receipt.all') }}" class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'active' : '' }}">
                                        <i class="far  {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                        <p>Purchase Return</p>
                                    </a>
                                </li>
                                <?php
                                $subSubMenu = ['supplier_payment.all'];
                                ?>
                                <li
                                    class="nav-item {{ Route::currentRouteName() == 'supplier_payment.all' ? 'active' : '' }}">
                                    <a href="{{ route('supplier_payment.all') }}"
                                       class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'active' : '' }}">
                                        <i
                                            class="far {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                        <p>Supplier Payment</p>
                                    </a>
                                </li>

                            <?php
                                $subSubMenu = [
                                    'inventory.all','inventory.details',
                                ];
                            ?>
                            <li class="nav-item">
                                <a href="{{ route('inventory.all') }}" class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'active' : '' }}">
                                    <i class="far  {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                    <p>Row Product Stock</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    @endif
                    @if(Auth::user()->role == 1)
                        <?php
                        $menu = [
                            'manufacture_template','manufacture_template.create','manufacture_template.edit',
                            'finished_goods', 'manufacture.create', 'manufacture.edit', 'consumption', 'finished_goods_details','finish_inventory.all','finish_inventory.details',
                            'all_stock.all','stock.details','add_serial','finish_stock.details'

                        ];
                        ?>
                        <li class="nav-item {{ in_array(Route::currentRouteName(), $menu) ? 'menu-open' : '' }}">
                            <a href="#"
                               class="nav-link {{ in_array(Route::currentRouteName(), $menu) ? 'active' : '' }}">
                                <i class="nav-icon fas fa-list"></i>
                                <p>
                                    Manufacturing
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <?php
                                $subMenu = ['manufacture_template','manufacture_template.create','manufacture_template.edit'];
                                ?>
                                <li class="nav-item">
                                    <a href="{{ route('manufacture_template') }}"
                                       class="nav-link {{ in_array(Route::currentRouteName(), $subMenu)  ? 'active' : '' }}">
                                        <i class="far  {{  in_array(Route::currentRouteName(), $subMenu)  ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                        <p>Manufacture Template</p>
                                    </a>
                                </li>

                                <?php
                                $subMenu = ['manufacture.create'];
                                ?>
                                <li class="nav-item">
                                    <a href="{{ route('manufacture.create') }}"
                                       class="nav-link {{ in_array(Route::currentRouteName(), $subMenu)  ? 'active' : '' }}">
                                        <i class="far  {{  in_array(Route::currentRouteName(), $subMenu)  ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                        <p>Manufacture</p>
                                    </a>
                                </li>

                                <?php
                                $subMenu = [
                                    'finished_goods','manufacture.edit', 'finished_goods_details','add_serial'
                                ];
                                ?>
                                <li class="nav-item">
                                    <a href="{{ route('finished_goods') }}"
                                       class="nav-link {{ in_array(Route::currentRouteName(), $subMenu)  ? 'active' : '' }}">
                                        <i class="far  {{  in_array(Route::currentRouteName(), $subMenu)  ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                        <p>Finished Goods</p>
                                    </a>
                                </li>
                                <?php
                                $subSubMenu = [
                                    'finish_inventory.all','finish_inventory.details','finish_stock.details',
                                ];
                                ?>
                                <li class="nav-item">
                                    <a href="{{ route('finish_inventory.all') }}" class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'active' : '' }}">
                                        <i class="far  {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                        <p>Finish Inventory</p>
                                    </a>
                                </li>
                                <?php
                                $subSubMenu = [
                                    'all_stock.all',
                                ];
                                ?>
                                <li class="nav-item">
                                    <a href="{{ route('all_stock.all') }}" class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'active' : '' }}">
                                        <i class="far  {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                        <p>All Finish Stock</p>
                                    </a>
                                </li>
                                <?php
                                $subMenu = [
                                    'consumption',
                                ];
                                ?>
                                <li class="nav-item">
                                    <a href="{{ route('consumption') }}"
                                       class="nav-link {{ in_array(Route::currentRouteName(), $subMenu)  ? 'active' : '' }}">
                                        <i class="far  {{  in_array(Route::currentRouteName(), $subMenu)  ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                        <p>Consumption</p>
                                    </a>
                                </li>

                            </ul>
                        </li>
                    @endif
                    @if(Auth::user()->role == 1)
                    <?php
                    $subMenu = [
                        'customer', 'customer.add', 'customer.edit','sales_order.create',
                        'sale_receipt.all', 'sale_receipt.details','sale_receipt.payment_details',
                        'client_payment.all_pending_check','manually_chequeIn','cheque_status',
                        'agm_receipt.all','finance_receipt.all','customer_payment.all',
                        'agm_receipt.edit','finance_receipt.edit','dealer_payment.all',
                        'distributor_payment.all','customer', 'customer.add', 'customer.edit','customer_payment.order_details',
                        'booking', 'booking.add', 'booking.edit','booking_details','assign_order_process',
                        'sale_return_receipt.all','sale.return','sale_return_receipt.details','sale_return_payment_all_details'
                    ];
                    ?>
                    <li class="nav-item {{ in_array(Route::currentRouteName(), $subMenu) ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link {{ in_array(Route::currentRouteName(), $subMenu) ? 'active' : '' }}">
                            <i class="nav-icon fas fa-list"></i>
                            <p>
                                Sale
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <?php
                            $subSubMenu = [
                                'customer', 'customer.add', 'customer.edit',

                            ];
                            ?>
                            <li class="nav-item">
                                <a href="{{ route('customer') }}" class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'active' : '' }}">
                                    <i class="far  {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                    <p>Customer</p>
                                </a>
                            </li>
                            <?php
                            $subSubMenu = [
                                'booking', 'booking.add', 'booking.edit','booking_details','assign_order_process'
                            ];
                            ?>
                            <li class="nav-item">
                                <a href="{{ route('booking') }}" class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'active' : '' }}">
                                    <i class="far  {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                    <p>Booking</p>
                                </a>
                            </li>

                            <?php
                            $subSubMenu = [
                            'sales_order.create',
                            ];
                            ?>
                            <li class="nav-item">
                                <a href="{{ route('sales_order.create') }}" class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'active' : '' }}">
                                    <i class="far  {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                    <p>Sale Order</p>
                                </a>
                            </li>
                            <?php
                            $subSubMenu = [
                                'sale_receipt.all','sale_receipt.details','sale_receipt.print'
                            ];
                            ?>
                            <li class="nav-item">
                                <a href="{{ route('sale_receipt.all') }}" class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'active' : '' }}">
                                    <i class="far {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                    <p>Sale Receipt</p>
                                </a>
                            </li>
                                <?php
                                $subSubMenu = [
                                    'sale_return_receipt.all','sale.return','sale_return_receipt.details','sale_return_payment_all_details'
                                ];
                                ?>
                                <li class="nav-item">
                                    <a href="{{ route('sale_return_receipt.all') }}" class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'active' : '' }}">
                                        <i class="far  {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                        <p>Sales Return</p>
                                    </a>
                                </li>
                                <?php
                                $subSubMenu = ['customer_payment.all','customer_payment.order_details'];
                                ?>
                                <li
                                    class="nav-item {{ Route::currentRouteName() == 'customer_payment.all' ? 'active' : '' }}">
                                    <a href="{{ route('customer_payment.all') }}"
                                       class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'active' : '' }}">
                                        <i
                                            class="far {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                        <p>Customer Payment</p>
                                    </a>
                                </li>

                        </ul>
                    </li>

                    <?php
                        $subMenu = [
                            'service_order.create','service_order.receipt','service_receipt.details',
                        ];
                    ?>
                    <li class="nav-item {{ in_array(Route::currentRouteName(), $subMenu) ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link {{ in_array(Route::currentRouteName(), $subMenu) ? 'active' : '' }}">
                            <i class="nav-icon fas fa-list"></i>
                            <p>
                                Service
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <?php
                                $subSubMenu = [
                                    'service_order.create',
                                ];
                            ?>
                            <li class="nav-item">
                                    <a href="{{ route('service_order.create') }}" class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'active' : '' }}">
                                    <i class="far  {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                    <p>Service Order</p>
                                </a>
                            </li>

                            <?php
                                $subSubMenu = [
                                    'service_order.receipt','service_receipt.details',
                                ];
                            ?>
                            <li class="nav-item">
                                    <a href="{{ route('service_order.receipt') }}" class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'active' : '' }}">
                                    <i class="far  {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                    <p>Service Receipt</p>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <?php
                    $subMenu = [
                        'supplier.ledger', 'purchase.report', 'stakeholder.report','customer.report','sales.report','service.report',
                        'progress.report','report.transaction','report.receive_and_payment','report.bank_statement',
                        'report.cash_statement','product_serial.report','sales.transaction_report',
                    ];
                    ?>

                    <li
                        class="nav-item {{ in_array(Route::currentRouteName(), $subMenu) ? 'menu-open' : '' }}">
                        <a href="#"
                           class="nav-link {{ in_array(Route::currentRouteName(), $subMenu) ? 'active' : '' }}">
                            <i class="nav-icon fas fa-list"></i>
                            <p>
                                Reports
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <?php
                            $subSubMenu = ['supplier.ledger'];
                            ?>
                            <li
                                class="nav-item {{ Route::currentRouteName() == 'supplier.ledger' ? 'active' : '' }}">
                                <a href="{{ route('supplier.ledger') }}"
                                   class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'active' : '' }}">
                                    <i
                                        class="far {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                    <p>Supplier Report</p>
                                </a>
                            </li>

                            <?php
                            $subSubMenu = ['purchase.report'];
                            ?>

                            <li
                                class="nav-item {{ Route::currentRouteName() == 'purchase.report' ? 'active' : '' }}">
                                <a href="{{ route('purchase.report') }}"
                                   class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'active' : '' }}">
                                    <i
                                        class="far {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                    <p>Purchase Report</p>
                                </a>
                            </li>

                            <?php
                            $subSubMenu = ['customer.report'];
                            ?>
                            <li
                                class="nav-item {{ Route::currentRouteName() == 'customer.report' ? 'active' : '' }}">
                                <a href="{{ route('customer.report') }}"
                                   class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'active' : '' }}">
                                    <i
                                        class="far {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                    <p>Customer Report</p>
                                </a>
                            </li>

                           <?php
                            $subSubMenu = ['sales.report'];
                            ?>
                            <li
                                class="nav-item {{ Route::currentRouteName() == 'sales.report' ? 'active' : '' }}">
                                <a href="{{ route('sales.report') }}"
                                   class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'active' : '' }}">
                                    <i
                                        class="far {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                    <p>Sales Report</p>
                                </a>
                            </li>
                            <?php
                            $subSubMenu = ['sales.transaction_report'];
                            ?>
                            <li
                                class="nav-item {{ Route::currentRouteName() == 'sales.report' ? 'active' : '' }}">
                                <a href="{{ route('sales.transaction_report') }}"
                                   class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'active' : '' }}">
                                    <i
                                        class="far {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                    <p>Sale Transaction Report</p>
                                </a>
                            </li>
                            <?php
                            $subSubMenu = ['service.report'];
                            ?>
                            <li
                                class="nav-item {{ Route::currentRouteName() == 'service.report' ? 'active' : '' }}">
                                <a href="{{ route('service.report') }}"
                                   class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'active' : '' }}">
                                    <i
                                        class="far {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                    <p>Service Report</p>
                                </a>
                            </li>

                            <?php
                            $subSubMenu = ['product_serial.report'];
                            ?>

                            <li
                                class="nav-item {{ Route::currentRouteName() == 'customer.report' ? 'active' : '' }}">
                                <a href="{{ route('product_serial.report') }}"
                                   class="nav-link {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'active' : '' }}">
                                    <i
                                        class="far {{ in_array(Route::currentRouteName(), $subSubMenu) ? 'fa-check-circle' : 'fa-circle' }} nav-icon"></i>
                                    <p>Product Serial Report</p>
                                </a>
                            </li>
                            @endif

                        </ul>

                        </li>
                </ul>
            </nav>
            <!-- /.sidebar-menu -->
        </div>
        <!-- /.sidebar -->
    </aside>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0"> @yield('title') </h1>
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->

        <!-- Main content -->
        <div class="content">
            <div class="container-fluid">
                @yield('content')
            </div>
            <!-- /.container-fluid -->
        </div>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

    <!-- Main Footer -->
    <footer class="main-footer">
        <!-- To the right -->
        <div class="float-right d-none d-sm-inline">
            Design & Developed By <a target="_blank" href="https://2aitbd.com">2A IT LIMITED</a>
        </div>
        <!-- Default to the left -->
        <strong>Copyright &copy; 2022-{{ date('Y') }} <a href="{{ route('dashboard') }}">{{ config('app.name') }}</a>.</strong> All rights reserved.
    </footer>
</div>
<!-- ./wrapper -->

<!-- REQUIRED SCRIPTS -->

<!-- jQuery -->
<script src="{{ asset('themes/backend/plugins/jquery/jquery.min.js') }}"></script>
<!-- Bootstrap 4 -->
<script src="{{ asset('themes/backend/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

<!-- Select2 -->
<script src="{{ asset('themes/backend/plugins/select2/js/select2.full.min.js') }}"></script>
<!-- Bootstrap4 Duallistbox -->
<script src="{{ asset('themes/backend/plugins/bootstrap4-duallistbox/jquery.bootstrap-duallistbox.min.js') }}"></script>
<!-- InputMask -->
<script src="{{ asset('themes/backend/plugins/moment/moment.min.js') }}"></script>
<script src="{{ asset('themes/backend/plugins/inputmask/jquery.inputmask.min.js') }}"></script>
<!-- date-range-picker -->
<script src="{{ asset('themes/backend/plugins/daterangepicker/daterangepicker.js') }}"></script>
<!-- bootstrap color picker -->
<script src="{{ asset('themes/backend/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.min.js') }}"></script>
<!-- Tempusdominus Bootstrap 4 -->
<script src="{{ asset('themes/backend/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') }}"></script>
<!-- SweetAlert2 -->
<script src="{{ asset('themes/backend/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
<!-- Toastr -->
<script src="{{ asset('themes/backend/plugins/toastr/toastr.min.js') }}"></script>
<!-- DataTables  & Plugins -->
<script src="{{ asset('themes/backend/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('themes/backend/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('themes/backend/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('themes/backend/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
<script src="{{ asset('themes/backend/plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('themes/backend/plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
<script src="{{ asset('themes/backend/plugins/jszip/jszip.min.js') }}"></script>
<script src="{{ asset('themes/backend/plugins/pdfmake/pdfmake.min.js') }}"></script>
<script src="{{ asset('themes/backend/plugins/pdfmake/vfs_fonts.js') }}"></script>
<script src="{{ asset('themes/backend/plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>
<script src="{{ asset('themes/backend/plugins/datatables-buttons/js/buttons.print.min.js') }}"></script>
<script src="{{ asset('themes/backend/plugins/datatables-buttons/js/buttons.colVis.min.js') }}"></script>

<!-- bootstrap datepicker -->
<script src="{{ asset('themes/backend/plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>


<script src="{{ asset('themes/backend/dist/js/sweetalert2@9.js') }}"></script>
<script>
    $(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        var message = '{{ session('message') }}';
        var error = '{{ session('error') }}';

        if (!window.performance || window.performance.navigation.type != window.performance.navigation.TYPE_BACK_FORWARD) {
            if (message != '')
                $(document).Toasts('create', {
                    icon: 'fas fa-envelope fa-lg',
                    class: 'bg-success',
                    title: 'Success',
                    autohide: true,
                    delay: 2000,
                    body: message
                })

            if (error != '')
                $(document).Toasts('create', {
                    icon: 'fas fa-envelope fa-lg',
                    class: 'bg-danger',
                    title: 'Error',
                    autohide: true,
                    delay: 2000,
                    body: error
                })
        }
    });
    function jsNumberFormat(yourNumber) {
        //Seperates the components of the number
        var n= yourNumber.toString().split(".");
        //Comma-fies the first part
        n[0] = n[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        //Combines the two sections
        return n.join(".");
    }
</script>

<script>
    $(function () {

        //Date picker
        $('.date-picker').datepicker({
            autoclose: true,
            format: 'dd-mm-yyyy'
        });

        //Initialize Select2 Elements
        $('.select2').select2()

        //Initialize Select2 Elements
        $('.select2bs4').select2({
            theme: 'bootstrap4'
        })

        //Datemask dd/mm/yyyy
        $('#datemask').inputmask('dd-mm-yyyy', { 'placeholder': 'dd-mm-yyyy' })
        //Datemask2 mm/dd/yyyy
        $('#datemask2').inputmask('mm-dd-yyyy', { 'placeholder': 'mm-dd-yyyy' })
        //Money Euro
        $('[data-mask]').inputmask()

        //Date picker
        $('#reservationdate').datetimepicker({
            format: 'L'
        });

        //Date and time picker
        $('.date-time').datetimepicker({
            format: 'DD-MM-YYYY hh:mm A',
            icons: { time: 'far fa-clock' }
        });
        //Date and time picker
        $('.date,.start_date,.end_date').datetimepicker({
            format: 'DD-MM-YYYY',
        });
        //Date range picker
        $('#reservation').daterangepicker()
        //Date range picker with time picker
        $('#reservationtime').daterangepicker({
            timePicker: true,
            timePickerIncrement: 30,
            locale: {
                format: 'MM-DD-YYYY hh:mm A'
            }
        })
        //Date range as a button
        $('#daterange-btn').daterangepicker(
            {
                ranges   : {
                    'Today'       : [moment(), moment()],
                    'Yesterday'   : [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days' : [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month'  : [moment().startOf('month'), moment().endOf('month')],
                    'Last Month'  : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                },
                startDate: moment().subtract(29, 'days'),
                endDate  : moment()
            },
            function (start, end) {
                $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'))
            }
        )

        //Timepicker
        $('#timepicker').datetimepicker({
            format: 'LT'
        })

        //Bootstrap Duallistbox
        $('.duallistbox').bootstrapDualListbox()

        //Colorpicker
        $('.my-colorpicker1').colorpicker()
        //color picker with addon
        $('.my-colorpicker2').colorpicker()

        $('.my-colorpicker2').on('colorpickerChange', function(event) {
            $('.my-colorpicker2 .fa-square').css('color', event.color.toString());
        })

        $("input[data-bootstrap-switch]").each(function(){
            $(this).bootstrapSwitch('state', $(this).prop('checked'));
        })

    })
</script>
<script>
    $(function () {

        //Date picker
        $( ".date-picker" ).datepicker({
            dateFormat: 'dd-mm-yy',
            changeMonth: true,
            changeYear: true,
        });
        //Date picker

        // $('.month-picker').MonthPicker({
        //     Button: false,
        // });

        $("#financial_year").change(function(){
            let FYear = $(this).val();
            if (FYear !== ''){
                fiscalYearDateRange(FYear)
                $('.date-picker-fiscal-year').prop('readonly', false);
                $('.date-picker-fiscal-year').attr("placeholder", "Enter Date");
            }else{
                $('.date-picker-fiscal-year').prop('readonly', true);
                $('.date-picker-fiscal-year').val(" ");
                $('.date-picker-fiscal-year').attr("placeholder", "Enter Date");
            }
        })
        $("#financial_year").trigger('change');
        //Initialize Select2 Elements
        $('.select2').select2()
        //Initialize Select2 Elements
        $('.select2bs4').select2({
            theme: 'bootstrap4'
        })


        //Bootstrap Duallistbox
        $('.duallistbox').bootstrapDualListbox()
        //Colorpicker
        $('.my-colorpicker1').colorpicker()
        //color picker with addon
        $('.my-colorpicker2').colorpicker()
        $('.my-colorpicker2').on('colorpickerChange', function (event) {
            $('.my-colorpicker2 .fa-square').css('color', event.color.toString());
        })
        $("input[data-bootstrap-switch]").each(function () {
            $(this).bootstrapSwitch('state', $(this).prop('checked'));
        })

    })
    function fiscalYearDateRange(year){

        $( ".date-picker-fiscal-year" ).datepicker({
            dateFormat: 'dd-mm-yy',
            changeMonth: true,
            changeYear: true,
            minDate: '01-07-'+year,
            maxDate: '30-06-'+(parseFloat(year) + 1)
        });
    }
    function jsNumberFormat(yourNumber) {
        //Seperates the components of the number
        var n= yourNumber.toString().split(".");
        //Comma-fies the first part
        n[0] = n[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        //Combines the two sections
        return n.join(".");
    }
    function formSubmitConfirm(btnIdName){
        $('body').on('click', '#'+btnIdName, function (e) {
            e.preventDefault();
            Swal.fire({
                title: 'Are you sure to save?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#343a40',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Save It!'

            }).then((result) => {
                if (result.isConfirmed) {
                    $('form').submit();
                }
            })

        });
    }
    function customDateInit(){
        $( ".date-picker" ).datepicker({
            dateFormat: 'dd-mm-yy',
            changeMonth: true,
            changeYear: true,
        });
    }
    function customSelect2Init(){
        $('.select2').select2()
    }
</script>
@yield('script')
<!-- AdminLTE App -->
<script src="{{ asset('themes/backend/dist/js/adminlte.min.js') }}"></script>
</body>
</html>
