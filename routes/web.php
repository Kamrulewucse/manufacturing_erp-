<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DesignationController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\CommonController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\ManufacturerConfigController;
use App\Http\Controllers\ManufacturerController;
use App\Http\Controllers\AccountHeadController;
use App\Http\Controllers\TaxSectionController;
use App\Http\Controllers\BalanceTransferController;
use App\Http\Controllers\BankVoucherController;
use App\Http\Controllers\CashVoucherController;
use App\Http\Controllers\ChequeReceiptController;
use App\Http\Controllers\CashReceiptController;
use App\Http\Controllers\JournalVoucherController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\PurchaseReturnController;
use App\Http\Controllers\SaleReturnController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth'])->group(function () {
// Dashboard
    Route::get('dashboard', [DashboardController::class,'index'])->name('dashboard');

// Product Status
    Route::get('product-status', [CommonController::class,'productStatus'])->name('product_status');
    Route::get('search-product-status-details', [CommonController::class,'productStatusDetails'])->name('search_product_status_details');

// User Management
    Route::get('user', [UserController::class,'index'])->name('user.all');
    Route::get('user/add',  [UserController::class,'add'])->name('user.add');
    Route::post('user/add',  [UserController::class,'addPost']);
    Route::get('user/edit/{user}',  [UserController::class,'edit'])->name('user.edit');
    Route::post('user/edit/{user}',  [UserController::class,'editPost']);


//Administrator Area

// Unit
    Route::get('unit', [UnitController::class,'index'])->name('unit');
    Route::get('unit/add', [UnitController::class,'add'])->name('unit.add');
    Route::post('unit/add', [UnitController::class,'addPost']);
    Route::get('unit/edit/{unit}', [UnitController::class,'edit'])->name('unit.edit');
    Route::post('unit/edit/{unit}', [UnitController::class,'editPost']);


//Accounts

    // Account Heads
    Route::get('account-head', [AccountHeadController::class,'accountHead'])->name('account_head');
    Route::get('account-head-datatable', [AccountHeadController::class,'datatable'])->name('account_head.datatable');
    Route::get('account-head/add', [AccountHeadController::class,'accountHeadAdd'])->name('account_head.add');
    Route::post('account-head/add', [AccountHeadController::class,'accountHeadAddPost']);
    Route::get('account-head/edit/{accountHead}', [AccountHeadController::class,'accountHeadEdit'])->name('account_head.edit');
    Route::post('account-head/edit/{accountHead}', [AccountHeadController::class,'accountHeadEditPost']);
    Route::post('account-head-delete', [AccountHeadController::class,'accountHeadDelete'])->name('account_head_delete');

    // section-number-of-tds
    Route::get('section-number-of-tds', [TaxSectionController::class,'index'])->name('section_number_of_tds');
    Route::get('section-number-of-tds-datatable', [TaxSectionController::class,'datatable'])->name('section_number_of_tds.datatable');
    Route::get('section-number-of-tds/add', [TaxSectionController::class,'add'])->name('section_number_of_tds.add');
    Route::post('section-number-of-tds/add', [TaxSectionController::class,'addPost']);
    Route::get('section-number-of-tds/edit/{taxSection}', [TaxSectionController::class,'edit'])->name('section_number_of_tds.edit');
    Route::post('section-number-of-tds/edit/{taxSection}', [TaxSectionController::class,'editPost']);

    // Balance Transfer
    Route::get('balance-transfer/add', [BalanceTransferController::class,'balanceTransferAdd'])->name('balance_transfer.add');
    Route::post('balance-transfer/add', [BalanceTransferController::class,'balanceTransferAddPost']);
    Route::get('balance-transfer/edit/{balanceTransfer}', [BalanceTransferController::class,'balanceTransferEdit'])->name('balance_transfer.edit');
    Route::post('balance-transfer/edit/{balanceTransfer}', [BalanceTransferController::class,'balanceTransferEditPost']);
    Route::get('balance-transfer', [BalanceTransferController::class,'balanceTransferIndex'])->name('balance_transfer');
    Route::get('balance-transfer-datatable', [BalanceTransferController::class,'balanceTransferDatatable'])->name('balance_transfer.datatable');
    Route::get('balance-transfer-voucher/details/{balanceTransfer}', [BalanceTransferController::class,'voucherDetails'])->name('balance_transfer_voucher_details');
    Route::get('balance-transfer-voucher/print/{balanceTransfer}', [BalanceTransferController::class,'voucherPrint'])->name('balance_transfer_voucher_print');
    Route::get('balance-transfer-receipt/details/{balanceTransfer}', [BalanceTransferController::class,'receiptDetails'])->name('balance_transfer_receipt_details');
    Route::get('balance-transfer-receipt/print/{balanceTransfer}', [BalanceTransferController::class,'receiptPrint'])->name('balance_transfer_receipt_print');

    //bank-voucher BankVoucherController
    Route::get('bank-voucher/create', [BankVoucherController::class,'create'])->name('bank_voucher.create');
    Route::post('bank-voucher/create', [BankVoucherController::class,'createPost']);
    Route::get('bank-voucher/edit/{receiptPayment}', [BankVoucherController::class,'edit'])->name('bank_voucher.edit');
    Route::post('bank-voucher/edit/{receiptPayment}', [BankVoucherController::class,'editPost']);
    Route::get('bank-voucher-datatable', [BankVoucherController::class,'datatable'])->name('bank_voucher.datatable');
    Route::get('bank-voucher', [BankVoucherController::class,'index'])->name('bank_voucher');
    Route::get('bank-voucher/details/{receiptPayment}', [BankVoucherController::class,'details'])->name('bank_voucher_details');
    Route::get('bank-voucher/print/{receiptPayment}', [BankVoucherController::class,'print'])->name('bank_voucher_print');
    Route::get('bank-voucher/range/print', [BankVoucherController::class,'rangePrint'])->name('bank_voucher_range_print');

    //Cash Voucher
    Route::get('cash-voucher/create', [CashVoucherController::class,'create'])->name('cash_voucher.create');
    Route::post('cash-voucher/create', [CashVoucherController::class,'createPost']);
    Route::get('cash-voucher/edit/{receiptPayment}', [CashVoucherController::class,'edit'])->name('cash_voucher.edit');
    Route::post('cash-voucher/edit/{receiptPayment}', [CashVoucherController::class,'editPost']);
    Route::get('cash-voucher-datatable', [CashVoucherController::class,'datatable'])->name('cash_voucher.datatable');
    Route::get('cash-voucher', [CashVoucherController::class,'index'])->name('cash_voucher');
    Route::get('cash-voucher/details/{receiptPayment}', [CashVoucherController::class,'details'])->name('cash_voucher_details');
    Route::get('cash-voucher/print/{receiptPayment}', [CashVoucherController::class,'print'])->name('cash_voucher_print');
    Route::get('cash-voucher/range/print', [CashVoucherController::class,'rangePrint'])->name('cash_voucher_range_print');

    //Cheque Receipt CHR
    Route::get('cheque-receipt/create', [ChequeReceiptController::class,'create'])->name('cheque_receipt.create');
    Route::post('cheque-receipt/create', [ChequeReceiptController::class,'createPost']);
    Route::get('cheque-receipt/edit/{receiptPayment}', [ChequeReceiptController::class,'edit'])->name('cheque_receipt.edit');
    Route::post('cheque-receipt/edit/{receiptPayment}', [ChequeReceiptController::class,'editPost']);
    Route::get('cheque-receipt-datatable', [ChequeReceiptController::class,'datatable'])->name('cheque_receipt.datatable');
    Route::get('cheque-receipt', [ChequeReceiptController::class,'index'])->name('cheque_receipt');
    Route::get('cheque-receipt/details/{receiptPayment}', [ChequeReceiptController::class,'details'])->name('cheque_receipt_details');
    Route::get('cheque-receipt/print/{receiptPayment}', [ChequeReceiptController::class,'print'])->name('cheque_receipt_print');
    Route::get('cheque-receipt/range/print', [ChequeReceiptController::class,'rangePrint'])->name('cheque_receipt_range_print');

    //Cash Receipt CR
    Route::get('cash-receipt/create', [CashReceiptController::class,'create'])->name('cash_receipt.create');
    Route::post('cash-receipt/create', [CashReceiptController::class,'createPost']);
    Route::get('cash-receipt/edit/{receiptPayment}', [CashReceiptController::class,'edit'])->name('cash_receipt.edit');
    Route::post('cash-receipt/edit/{receiptPayment}', [CashReceiptController::class,'editPost']);
    Route::get('cash-receipt-datatable', [CashReceiptController::class,'datatable'])->name('cash_receipt.datatable');
    Route::get('cash-receipt', [CashReceiptController::class,'index'])->name('cash_receipt');
    Route::get('cash-receipt/details/{receiptPayment}', [CashReceiptController::class,'details'])->name('cash_receipt_details');
    Route::get('cash-receipt/print/{receiptPayment}', [CashReceiptController::class,'print'])->name('cash_receipt_print');
    Route::get('cash-receipt/range/print', [CashReceiptController::class,'rangePrint'])->name('cash_receipt_range_print');


    //Journal
    Route::get('journal-voucher/create', [JournalVoucherController::class,'create'])->name('journal_voucher.create');
    Route::post('journal-voucher/create', [JournalVoucherController::class,'createPost']);
    Route::get('journal-voucher/edit/{journalVoucher}', [JournalVoucherController::class,'edit'])->name('journal_voucher.edit');
    Route::post('journal-voucher/edit/{journalVoucher}', [JournalVoucherController::class,'editPost']);
    Route::get('journal-voucher-datatable', [JournalVoucherController::class,'datatable'])->name('journal_voucher.datatable');
    Route::get('journal-voucher', [JournalVoucherController::class,'index'])->name('journal_voucher');
    Route::get('journal-voucher/details/{journalVoucher}', [JournalVoucherController::class,'journalDetails'])->name('journal_voucher_details');
    Route::get('journal-voucher/print/{journalVoucher}', [JournalVoucherController::class,'print'])->name('journal_voucher_print');

    Route::get('journal-voucher/range/print', [JournalVoucherController::class,'rangePrint'])->name('journal_voucher_range_print');

    //Report
    Route::get('product-serial-report', [ReportController::class,'productSerial'])->name('product_serial.report');

    Route::get('supplier-report', [ReportController::class,'supplierReport'])->name('supplier.ledger');
    Route::get('purchase-report', [ReportController::class,'purchaseReport'])->name('purchase.report');
    Route::get('customer-report', [ReportController::class,'customerReport'])->name('customer.report');
    Route::get('sales-report', [ReportController::class,'salesReport'])->name('sales.report');
    Route::get('sale-transaction-report', [ReportController::class,'saleTransactionReport'])->name('sales.transaction_report');
    Route::get('service-report', [ReportController::class,'serviceReport'])->name('service.report');
    Route::get('project-wise-report', [ReportController::class,'projectReport'])->name('project.report');

    Route::get('report/cheque-register', [ReportController::class,'chequeRegister'])->name('report.cheque_register');

    Route::get('report/ledger', [ReportController::class,'ledger'])->name('report.ledger');
    Route::get('report/trial-balance', [ReportController::class,'trailBalance'])->name('report.trail_balance');
    Route::get('receive/payment',[ReportController::class,'receivePayment'])->name('report.receive_and_payment');


//    Route::get('project/report/receive/payment', [AccountsController::class,'projectReportReceivePayment'])->name('project.report.receive_and_payment');



    Route::get('stakeholder-report', [ReportController::class,'stakeholderReport'])->name('stakeholder.report');
    Route::get('stake-holder-report', [ReportController::class,'stakeholderReport'])->name('stake_holder.report');
//    Route::get('project-report', [ReportController::class,'projectReport'])->name('project.report');
    Route::get('progress-report', [ReportController::class,'progressReport'])->name('progress.report');
    Route::get('report/transaction', [AccountsController::class,'reportTransaction'])->name('report.transaction');
    Route::get('project/report/transaction', [AccountsController::class,'projectReportTransaction'])->name('project.report.transaction');
//    Route::get('report/receive/payment', [AccountsController::class,'reportReceivePayment'])->name('report.receive_and_payment');
    Route::get('report/bank/statement', [ReportController::class,'bankStatement'])->name('report.bank_statement');
    Route::get('report/cash/statement', [ReportController::class,'cashStatement'])->name('report.cash_statement');
    Route::get('report/all-receive-payment',[ReportController::class,'reportReceivePayment'])->name('report.all_receive_payment');



//Department
    Route::get('department', [DepartmentController::class,'department'])->name('department');
    Route::get('department-datatable', [DepartmentController::class,'datatable'])->name('department.datatable');
    Route::get('department/add', [DepartmentController::class,'departmentAdd'])->name('department.add');
    Route::post('department/add', [DepartmentController::class,'departmentAddPost']);
    Route::get('department/edit/{department}', [DepartmentController::class,'departmentEdit'])->name('department.edit');
    Route::get('department/delete/{department}', [DepartmentController::class,'departmentDelete'])->name('department.delete');
    Route::post('department/edit/{department}', [DepartmentController::class,'departmentEditPost']);

//designation
    Route::get('designation', [DesignationController::class,'index'])->name('designation');
    Route::get('designation-datatable', [DesignationController::class,'datatable'])->name('designation.datatable');
    Route::get('designation/add', [DesignationController::class,'add'])->name('designation.add');
    Route::post('designation/add', [DesignationController::class,'addPost']);
    Route::get('designation/edit/{designation}', [DesignationController::class,'edit'])->name('designation.edit');
    Route::post('designation/edit/{designation}', [DesignationController::class,'editPost']);

// Employee
    Route::get('employee', [EmployeeController::class,'index'])->name('employee');
    Route::get('employee/datatable', [EmployeeController::class,'datatableEmployee'])->name('employee.datatable');
    Route::get('employee/get-designation', [EmployeeController::class,'getDesignation'])->name('get_employee_designation');
    Route::post('employee/designation-update', [EmployeeController::class,'designationUpdate'])->name('employee.designation_update');
    Route::get('employee/add', [EmployeeController::class,'add'])->name('employee.add');
    Route::post('employee/add', [EmployeeController::class,'addPost']);
    Route::get('employee/edit/{employee}', [EmployeeController::class,'edit'])->name('employee.edit');
    Route::get('employee/edit/details/{employee}', [EmployeeController::class,'edit'])->name('employee_details');
    Route::post('employee/edit/{employee}', [EmployeeController::class,'editPost']);
    Route::get('employee-all-list', [EmployeeController::class,'employeeList'])->name('employee_list');

//   Purchase
// Supplier
    Route::get('supplier', [SupplierController::class,'index'])->name('supplier');
    Route::get('supplier/add', [SupplierController::class,'add'])->name('supplier.add');
    Route::post('supplier/add', [SupplierController::class,'addPost']);
    Route::get('supplier/edit/{supplier}', [SupplierController::class,'edit'])->name('supplier.edit');
    Route::post('supplier/edit/{supplier}', [SupplierController::class,'editPost']);

// Product Category
    Route::get('product-category', [ProductCategoryController::class,'index'])->name('product_category');
    Route::get('product-category/add', [ProductCategoryController::class,'add'])->name('product_category.add');
    Route::post('product-category/add', [ProductCategoryController::class,'addPost']);
    Route::get('product-category/edit/{category}', [ProductCategoryController::class,'edit'])->name('product_category.edit');
    Route::post('product-category/edit/{category}', [ProductCategoryController::class,'editPost']);

// Product
    Route::get('all-product', [ProductController::class,'product'])->name('all_product');
    Route::get('all-product-datatable', [ProductController::class,'productDatatable'])->name('product.datatable');
    Route::get('product/add', [ProductController::class,'productAdd'])->name('product_add');
    Route::post('product/add', [ProductController::class,'productAddPost']);
    Route::get('product/edit/{product}', [ProductController::class,'productEdit'])->name('product.edit');
    Route::post('product/edit/{product}', [ProductController::class,'productEditPost']);

    //Warning Product
    Route::get('market-list-product', [ProductController::class,'marketList'])->name('warning_product');
    Route::get('market-list-product-datatable', [ProductController::class,'marketListDatatable'])->name('market_list.datatable');


// Purchase Order
    Route::get('purchase-order', [PurchaseController::class,'purchaseOrder'])->name('purchase_order.create');
    Route::post('purchase-order', [PurchaseController::class,'purchaseOrderPost']);
    Route::get('purchase-product-json', [PurchaseController::class,'purchaseProductJson'])->name('purchase_product.json');

// Purchase Receipt
    Route::get('purchase-receipt', [PurchaseController::class,'purchaseReceipt'])->name('purchase_receipt.all');
    Route::get('purchase-receipt/edit/{order}', [PurchaseController::class,'purchaseReceiptEdit'])->name('purchase_receipt.edit');
    Route::post('purchase-receipt/edit/{order}', [PurchaseController::class,'editPost']);

    Route::get('purchase-receipt/details/{order}', [PurchaseController::class,'purchaseReceiptDetails'])->name('purchase_receipt.details');
    Route::get('purchase-receipt/print/{order}', [PurchaseController::class,'purchaseReceiptPrint'])->name('purchase_receipt.print');
    Route::get('purchase-receipt/datatable', [PurchaseController::class,'purchaseReceiptDatatable'])->name('purchase_receipt.datatable');
    Route::get('purchase-receipt/payment/details/{order}', [PurchaseController::class,'purchasePaymentDetails'])->name('purchase_receipt.payment_details');
    Route::get('purchase-receipt/payment/print/{payment}', [PurchaseController::class,'purchasePaymentPrint'])->name('purchase_receipt.payment_print');
    Route::get('purchase-order/confirm', [PurchaseController::class,'purchaseOrderConfirm'])->name('purchase_order_confirm');
    Route::post('purchase-payment/make-payment', [PurchaseController::class,'purchaseMakePayment'])->name('purchase_payment.make_payment');

    //purchase Return
    Route::get('purchase-return', [PurchaseReturnController::class,'purchaseReturn'])->name('purchase.return');
    Route::post('purchase-return', [PurchaseReturnController::class,'purchaseReturnPost']);
    Route::get('purchase-return-receipt', [PurchaseReturnController::class,'purchaseReturnReceipt'])->name('purchase_return_receipt.all');
    Route::get('purchase-return-receipt/datatable', [PurchaseReturnController::class,'purchaseReturnReceiptDatatable'])->name('purchase_return_receipt.datatable');
    Route::get('get-purchase-return-order-product', [PurchaseReturnController::class,'getPurchaseReturnOrderProduct'])->name('get_order_product');
    Route::get('get-purchase-return-purchase-details', [PurchaseReturnController::class,'getPurchaseReturnDetails'])->name('get_purchase_details');
    Route::get('purchase-return-receipt/details/{order}', [PurchaseReturnController::class,'purchaseReturnReceiptDetails'])->name('purchase_return_receipt.details');
    Route::post('purchase-return-receipt/make-receipt', [PurchaseReturnController::class,'purchaseReturnPaymentReceipt'])->name('purchase_return_payment');
    Route::get('purchase-return-receipt/all/details/{order}', [PurchaseReturnController::class,'purchaseReturnPaymentReceiptAll'])->name('purchase_return_payment_all_details');


    // Purchase Inventory
    Route::get('inventory', [PurchaseController::class,'inventory'])->name('inventory.all');
    Route::get('inventory/datatable', [PurchaseController::class,'inventoryDatatable'])->name('inventory.datatable');
    Route::get('inventory/details/{product}', [PurchaseController::class,'inventoryDetails'])->name('inventory.details');
    Route::get('inventory-details/datatable', [PurchaseController::class,'inventoryDetailsDatatable'])->name('inventory.details.datatable');

// Supplier Payment
    Route::get('supplier-payment', [PurchaseController::class,'supplierPayment'])->name('supplier_payment.all');
//    Route::get('supplier-payment/get-orders', [PurchaseController::class,'supplierPaymentGetOrders'])->name('supplier_payment.get_orders');
//    Route::get('supplier-payment/get-refund-orders', [PurchaseController::class,'supplierPaymentGetRefundOrders'])->name('supplier_payment.get_refund_orders');
    Route::get('supplier-payment/order-details/{supplier}', [PurchaseController::class,'supplierPaymentDetails'])->name('supplier_payment.order_details');
    Route::post('supplier-payment/payment', [PurchaseController::class,'supplierMakePayment'])->name('supplier_payment.make_payment');


    //manufacture template
    Route::get('manufacture-template', [ManufacturerConfigController::class,'index'])->name('manufacture_template');
    Route::get('manufacture-template/create', [ManufacturerConfigController::class,'create'])->name('manufacture_template.create');
    Route::post('manufacture-template/create', [ManufacturerConfigController::class,'store']);
    Route::get('manufacture-template/datatable', [ManufacturerConfigController::class,'datatable'])->name('manufacture_template.datatable');
    Route::get('manufacture-template/edit/{configProduct}', [ManufacturerConfigController::class,'edit'])->name('manufacture_template.edit');
    Route::post('manufacture-template/edit/{configProduct}', [ManufacturerConfigController::class,'update']);
    Route::post('manufacture-template/delete', [ManufacturerConfigController::class,'delete'])->name('manufacture_template.delete');

    Route::get('get-product-details', [ManufacturerConfigController::class,'getProductDetails'])->name('get_product_details');
    Route::get('get_template_details', [ManufacturerConfigController::class,'getTemplateDetails'])->name('get_template_details');


    //Manufacture
    Route::get('finished-goods', [ManufacturerController::class,'index'])->name('finished_goods');
    Route::get('finished-goods-details/{finishedGoods}', [ManufacturerController::class,'details'])->name('finished_goods_details');
    Route::get('finished-goods/datatable', [ManufacturerController::class,'datatable'])->name('finished_goods.datatable');
    Route::get('manufacture/create', [ManufacturerController::class,'create'])->name('manufacture.create');
    Route::post('manufacture/create', [ManufacturerController::class,'store']);
    Route::get('manufacture/edit/{finishedGoods}', [ManufacturerController::class,'edit'])->name('manufacture.edit');
    Route::post('manufacture/edit/{finishedGoods}', [ManufacturerController::class,'update']);
    Route::get('finish-inventory', [ManufacturerController::class,'inventory'])->name('finish_inventory.all');
    Route::get('finish-inventory/datatable', [ManufacturerController::class,'inventoryDatatable'])->name('finish_inventory.datatable');
    Route::post('finished-goods/delete', [ManufacturerController::class,'delete'])->name('finished_goods.delete');
    Route::get('consumption', [ManufacturerController::class,'consumption'])->name('consumption');

    //Stock
    Route::get('stock', [ManufacturerController::class,'stock'])->name('all_stock.all');
    Route::get('stock/datatable', [ManufacturerController::class,'stockDatatable'])->name('all_stock.datatable');
    Route::get('stock/{product}', [ManufacturerController::class,'stockDetails'])->name('stock.details');
    Route::get('finish-stock/{product}', [ManufacturerController::class,'finishStockDetails'])->name('finish_stock.details');
    Route::get('stock-details/datatable', [ManufacturerController::class,'inventoryDetailsDatatable'])->name('stock.details.datatable');

    //serial add
    Route::get('serial-add/{finishedGoods}', [ManufacturerController::class,'addSerial'])->name('add_serial');
    Route::post('serial/update', [ManufacturerController::class,'updateSerial'])->name('update_serial');


    // Customer
    Route::get('customer', [CustomerController::class,'index'])->name('customer');
    Route::get('customer/add', [CustomerController::class,'add'])->name('customer.add');
    Route::post('customer/add', [CustomerController::class,'addPost']);
    Route::get('customer/edit/{customer}', [CustomerController::class,'edit'])->name('customer.edit');
    Route::post('customer/edit/{customer}', [CustomerController::class,'editPost']);
    Route::get('customer/datatable', [CustomerController::class,'datatable'])->name('customer.datatable');

    // Booking
    Route::get('booking', [BookingController::class,'index'])->name('booking');
    Route::get('booking/add', [BookingController::class,'add'])->name('booking.add');
    Route::post('booking/add', [BookingController::class,'addPost']);
    Route::get('booking/edit/{booking}', [BookingController::class,'edit'])->name('booking.edit');
    Route::post('booking/edit/{booking}', [BookingController::class,'editPost']);
    Route::get('booking/datatable', [BookingController::class,'datatable'])->name('booking.datatable');
    Route::get('booking/details/{booking}', [BookingController::class,'bookingDetails'])->name('booking_details');
    Route::post('booking/delivery-date-change', [BookingController::class,'deliveryDateChange'])->name('update_delivery_date');
    Route::post('booking/delete', [BookingController::class,'delete'])->name('booking.delete');



    //Sale Order
    Route::get('sales-order', [SaleController::class,'salesOrder'])->name('sales_order.create');
    Route::post('sales-order', [SaleController::class,'salesOrderPost']);
    Route::get('sale/product/details', [SaleController::class,'saleProductDetails'])->name('get_sale_details');
    Route::get('get/service/details', [SaleController::class,'getServiceDetails'])->name('get_product_sale_details');
//    Route::get('sale-booking/details', [SaleController::class,'getBookingDetails'])->name('get_booking_details');
    Route::get('get-booking/details', [SaleController::class,'getBookingDetails'])->name('get_booking_details');

    // Sale Receipt
    Route::get('sale-receipt', [SaleController::class,'saleReceipt'])->name('sale_receipt.all');
    Route::get('sale-receipt/details/{order}', [SaleController::class,'saleReceiptDetails'])->name('sale_receipt.details');
    Route::get('sale-receipt/all/details/{order}', [SaleController::class,'saleReceiptAll'])->name('sale_receipt_all_details');

    Route::get('sale-receipt/print/{order}', [SaleController::class,'saleReceiptPrint'])->name('sale_receipt.print');
    Route::get('sale-receipt/print/with-header/{order}', [SaleController::class,'saleReceiptPrintWithHeader'])->name('sale_receipt.print_with_header');
    Route::get('sale-receipt/datatable', [SaleController::class,'saleReceiptDatatable'])->name('sale_receipt.datatable');
    Route::post('sale-receipt/payment', [SaleController::class,'makePayment'])->name('sale_receipt.make_payment');
    Route::get('sale-receipt/payment/details/{payment}', [SaleController::class,'salePaymentDetails'])->name('sale_receipt.payment_details');
    Route::get('sale-receipt/payment/print/{payment}', [SaleController::class,'salePaymentPrint'])->name('sale_receipt.payment_print');
    Route::get('sale-receipt/edit/{order}', [SaleController::class,'saleReceiptEdit'])->name('sale_receipt.edit');
    Route::post('sale-receipt/edit/{order}', [SaleController::class,'saleReceiptEditPost']);
    Route::post('sale-receipt/make-receipt', [SaleController::class,'saleMakeReceipt'])->name('sale_receipt.make_receipt');

    //sale Return
    Route::get('sale-return', [SaleReturnController::class,'saleReturn'])->name('sale.return');
    Route::post('sale-return', [SaleReturnController::class,'saleReturnPost']);
    Route::get('sale-return-receipt', [SaleReturnController::class,'saleReturnReceipt'])->name('sale_return_receipt.all');
    Route::get('sale-return-receipt/datatable', [SaleReturnController::class,'saleReturnReceiptDatatable'])->name('sale_return_receipt.datatable');
    Route::get('get-sale-return-order-product', [SaleReturnController::class,'getSaleReturnOrderProduct'])->name('get_sale_return_order_product');
    Route::get('get-sale-return-purchase-details', [SaleReturnController::class,'getSaleReturnDetails'])->name('get_sale_return_details');
    Route::get('sale-return-receipt/details/{order}', [SaleReturnController::class,'saleReturnReceiptDetails'])->name('sale_return_receipt.details');
    Route::post('sale-return-receipt/make-receipt', [SaleReturnController::class,'saleReturnPaymentReceipt'])->name('sale_return_payment');
    Route::get('sale-return-receipt/all/details/{order}', [SaleReturnController::class,'saleReturnPaymentReceiptAll'])->name('sale_return_payment_all_details');


    //Contractor Payment Details
    Route::get('payment/individual/details/{payment}', [SaleController::class,'individualSaleReceiptDetails'])->name('sale_receipt.all_payment_details');

    //Journal Receipt
    Route::get('sale-receipt-journal/details/{order}', [SaleController::class,'saleJournalDetails'])->name('sale_journal_voucher.details');

    // Customer Payment
    Route::get('customer-payment', [SaleController::class,'customerPayment'])->name('customer_payment.all');
    Route::get('customer-payment/order-details/{customer}', [SaleController::class,'customerPaymentDetails'])->name('customer_payment.order_details');
//    Route::get('customer-payment/get-orders', [SaleController::class,'customerPaymentGetOrders'])->name('customer_payment.get_orders');
//    Route::get('customer-payment/get-refund-orders', [SaleController::class,'customerPaymentGetRefundOrders'])->name('customer_payment.get_refund_orders');
    Route::post('customer-payment/payment', [SaleController::class,'customerMakePaymentReceipt'])->name('customer_payment.make_payment');

    // Service
    Route::get('service-order/create', [ServiceController::class,'serviceOrderCreate'])->name('service_order.create');
    Route::post('service-order/create', [ServiceController::class,'serviceOrderCreatePost']);
    Route::get('service-order/receipt', [ServiceController::class,'serviceOrderReceipt'])->name('service_order.receipt');
    Route::get('service-receipt/datatable', [ServiceController::class,'serviceDatatable'])->name('service_receipt.datatable');
    Route::get('service/product/details', [ServiceController::class,'serviceProductDetails'])->name('get_service_product_details');
    Route::get('service-receipt/details/{order}', [ServiceController::class,'serviceReceiptDetails'])->name('service_receipt.details');
    Route::get('search-service-product', [ServiceController::class,'searchServiceProduct'])->name('search_service_product');
    Route::get('search-re-service-product', [ServiceController::class,'searchReServiceProduct'])->name('search_re_service_product');
    Route::get('service-receipt/all/details/{order}', [ServiceController::class,'serviceReceiptAll'])->name('service_receipt_all_details');
    Route::get('service-receipt/print/{order}', [ServiceController::class,'serviceReceiptPrint'])->name('service_receipt.print');
    Route::get('service-receipt-header/print/{order}', [ServiceController::class,'serviceReceiptHeaderPrint'])->name('service_receipt.print_with_header');
    Route::post('service-make-receipt', [ServiceController::class,'serviceMakeReceipt'])->name('service.make_receipt');

    //Common
    Route::get('get-product', [CommonController::class,'getProduct'])->name('get_product');
    Route::get('get-sub-category', [CommonController::class,'getSubCategory'])->name('get_subCategory');
    Route::get('get-unit', [CommonController::class,'getUnit'])->name('get_unit');
    Route::get('sale-product-json', [CommonController::class, 'saleProductJson'])->name('sale_product.json');
    Route::get('product-json', [CommonController::class, 'productJson'])->name('product.json');
    Route::get('finish-product-json', [CommonController::class, 'finishProductJson'])->name('finishProduct.json');
    Route::get('get-stock', [CommonController::class,'getStock'])->name('get_inventory_stock');
    Route::get('get-employee-details', [CommonController::class,'getEmployeeDetails'])->name('get_employee_details');
    Route::get('get-customer-details', [CommonController::class,'getCustomerDetails'])->name('get_customer_details');
    Route::get('get-designation', [CommonController::class,'getField'])->name('get_designation');
    Route::get('get-designation-edit', [CommonController::class,'getFieldEdit'])->name('get_designation_edit');
    Route::get('account-head-code-json', [CommonController::class, 'accountHeadCodeJson'])->name('account_head_code.json');
    Route::get('sale-account-head-code-json', [CommonController::class, 'saleAccountHeadCodeJson'])->name('sale_account_head_code.json');
    Route::get('payee-json', [CommonController::class,'payeeJson'])->name('payee_json');
    Route::get('payee-json1', [CommonController::class,'payeeJson1'])->name('payee_json1');

    //Technician
    Route::get('assign-order-receipt', [BookingController::class,'assignSaleReceipt'])->name('assign_receipt.all');
    Route::get('assign-order-receipt/datatable', [BookingController::class,'assignSaleReceiptDatatable'])->name('assign_receipt.datatable');
    Route::post('assign-order-accept', [BookingController::class,'acceptAssignOrder'])->name('accept_assign_order');
    Route::post('assign-order-complete', [BookingController::class,'completeAssignOrder'])->name('complete_assign_order');
    Route::post('assign-order-cancel', [BookingController::class,'cancelAssignOrder'])->name('cancel_assign_order');
    Route::post('assign-order-stock', [BookingController::class,'stockAssignOrder'])->name('stock_assign_order');
    Route::post('assign-order-remake', [BookingController::class,'remakeAssignOrder'])->name('remake_assign_order');
    Route::get('assign-order/process/{booking}', [BookingController::class,'assignOrderProcess'])->name('assign_order_process');
    Route::get('booking-receipt/print/{booking}', [BookingController::class,'bookingReceiptPrint'])->name('booking_receipt.print');
});



require __DIR__.'/auth.php';
