@extends('layouts.app')


@section('title')
    User Edit
@endsection

@section('content')
    <div class="row">
        <!-- left column -->
        <div class="col-md-12">
            <!-- jquery validation -->
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">User Information</h3>
                </div>
                <!-- /.box-header -->
                <!-- form start -->
                <form class="form-horizontal" method="POST" action="{{ route('user.edit', ['user' => $user->id]) }}">
                    @csrf

                    <div class="card-body">

{{--                        <div class="form-group row {{ $errors->has('company_type') ? 'has-error' :'' }}">--}}
{{--                            <label class="col-sm-2 control-label">Company Type *</label>--}}
{{--                            <div class="col-sm-10">--}}
{{--                                <select class="form-control company_type select2" style="width: 100%;" name="company_type" id="company_type">--}}
{{--                                    <option value="">Select Company Type</option>--}}
{{--                                    <option value="1" {{ old('company_type',$user->company_type) == 1 ? 'selected' : '' }}>Kent Company</option>--}}
{{--                                    <option value="2" {{ old('company_type',$user->company_type) == 2 ? 'selected' : '' }}>Care Company</option>--}}
{{--                                </select>--}}

{{--                                @error('company_type')--}}
{{--                                <span class="help-block">{{ $message }}</span>--}}
{{--                                @enderror--}}
{{--                            </div>--}}
{{--                        </div>--}}

{{--                        <div class="form-group row {{ $errors->has('role') ? 'has-error' :'' }}">--}}
{{--                            <label class="col-sm-2 control-label">Role Type *</label>--}}
{{--                            <div class="col-sm-10">--}}
{{--                                <select class="form-control role select2" style="width: 100%;" name="role" id="role">--}}
{{--                                    <option value="">Select Role Type</option>--}}
{{--                                    <option value="1" {{ old('role',$user->role) == 1 ? 'selected' : '' }}>Super Admin</option>--}}
{{--                                    <option value="2" {{ old('role',$user->role) == 2 ? 'selected' : '' }}>Admin</option>--}}
{{--                                    <option value="3" {{ old('role',$user->role) == 3 ? 'selected' : '' }}>Employee</option>--}}
{{--                                    <option value="4" {{ old('role',$user->role) == 4 ? 'selected' : '' }}>Dealer</option>--}}
{{--                                    <option value="5" {{ old('role',$user->role) == 5 ? 'selected' : '' }}>Customer</option>--}}
{{--                                    <option value="7" {{ old('role',$user->role) == 7 ? 'selected' : '' }}>Distributor</option>--}}
{{--                                </select>--}}

{{--                                @error('role')--}}
{{--                                <span class="help-block">{{ $message }}</span>--}}
{{--                                @enderror--}}
{{--                            </div>--}}
{{--                        </div>--}}

                        <div class="form-group row {{ $errors->has('name') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Name *</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" placeholder="Enter Name"
                                       name="name" value="{{ empty(old('name')) ? ($errors->has('name') ? '' : $user->name) : old('name') }}">

                                @error('name')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row {{ $errors->has('email') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Email *</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" placeholder="Enter Email"
                                       name="email" value="{{ empty(old('email')) ? ($errors->has('email') ? '' : $user->email) : old('email') }}">

                                @error('email')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row {{ $errors->has('password') ? 'has-error' :'' }}">
                            <label class="col-sm-2 control-label">Password</label>

                            <div class="col-sm-10">
                                <input type="password" class="form-control" placeholder="Enter Password"
                                       name="password">

                                @error('password')
                                <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-2 control-label">Confirm Password</label>

                            <div class="col-sm-10">
                                <input type="password" class="form-control" placeholder="Enter Confirm Password"
                                       name="password_confirmation">
                            </div>
                        </div>

{{--                        <table class="table table-bordered">--}}
{{--                            <tr>--}}
{{--                                <td colspan="2">--}}
{{--                                    <input type="checkbox" id="checkAll"> Check All--}}
{{--                                </td>--}}
{{--                            </tr>--}}

{{--                            <tr>--}}
{{--                                <td  rowspan="4" style="vertical-align: middle;">--}}
{{--                                    <input type="checkbox" name="permission[]" value="administrator" id="administrator" {{ ($user->can('administrator') ? 'checked' : '') }}> Administrator--}}
{{--                                </td>--}}

{{--                                <td>--}}
{{--                                    <input type="checkbox" name="permission[]" value="users" id="users" {{ ($user->can('users') ? 'checked' : '') }}> Users--}}
{{--                                </td>--}}
{{--                            </tr>--}}
{{--                            <tr>--}}
{{--                                <td>--}}
{{--                                    <input type="checkbox" name="permission[]" value="projects" id="projects" {{ ($user->can('projects') ? 'checked' : '') }}> Projects--}}
{{--                                </td>--}}
{{--                            </tr>--}}
{{--                            <tr>--}}
{{--                                <td>--}}
{{--                                    <input type="checkbox" name="permission[]" value="warehouses" id="warehouses" {{ ($user->can('warehouses') ? 'checked' : '') }}> Warehouses--}}
{{--                                </td>--}}
{{--                            </tr>--}}
{{--                            <tr>--}}
{{--                                <td>--}}
{{--                                    <input type="checkbox" name="permission[]" value="units" id="units" {{ ($user->can('units') ? 'checked' : '') }}> Units--}}
{{--                                </td>--}}
{{--                            </tr>--}}

{{--                            <tr>--}}
{{--                                <td rowspan="4" style="vertical-align: middle;">--}}
{{--                                    <input type="checkbox" name="permission[]" value="bank_and_account" id="bank_and_account" {{ ($user->can('bank_and_account') ? 'checked' : '') }}> Bank & Account--}}
{{--                                </td>--}}

{{--                                <td>--}}
{{--                                    <input type="checkbox" name="permission[]" value="bank" id="bank" {{ ($user->can('bank') ? 'checked' : '') }}> Bank--}}
{{--                                </td>--}}
{{--                            </tr>--}}

{{--                            <tr>--}}
{{--                                <td>--}}
{{--                                    <input type="checkbox" name="permission[]" value="branch" id="branch" {{ ($user->can('branch') ? 'checked' : '') }}> Branch--}}
{{--                                </td>--}}
{{--                            </tr>--}}

{{--                            <tr>--}}
{{--                                <td>--}}
{{--                                    <input type="checkbox" name="permission[]" value="account" id="account" {{ ($user->can('account') ? 'checked' : '') }}> Account--}}
{{--                                </td>--}}
{{--                            </tr>--}}
{{--                            <tr>--}}
{{--                                <td>--}}
{{--                                    <input type="checkbox" name="permission[]" value="cash" id="cash" {{ ($user->can('cash') ? 'checked' : '') }}> Cash--}}
{{--                                </td>--}}
{{--                            </tr>--}}

{{--                            <tr>--}}
{{--                                <td rowspan="4" style="vertical-align: middle;">--}}
{{--                                    <input type="checkbox" name="permission[]" value="project_control" id="project_control" {{ ($user->can('project_control') ? 'checked' : '') }}> Project Control--}}
{{--                                </td>--}}

{{--                                <td>--}}
{{--                                    <input type="checkbox" name="permission[]" value="project_duration" id="project_duration" {{ ($user->can('project_duration') ? 'checked' : '') }}> Project Duration--}}
{{--                                </td>--}}
{{--                            </tr>--}}

{{--                            <tr>--}}
{{--                                <td>--}}
{{--                                    <input type="checkbox" name="permission[]" value="project_budget" id="project_budget" {{ ($user->can('project_budget') ? 'checked' : '') }}> Project Budget--}}
{{--                                </td>--}}
{{--                            </tr>--}}

{{--                            <tr>--}}
{{--                                <td>--}}
{{--                                    <input type="checkbox" name="permission[]" value="segment" id="segment" {{ ($user->can('segment') ? 'checked' : '') }}> Segment--}}
{{--                                </td>--}}
{{--                            </tr>--}}

{{--                            <tr>--}}
{{--                                <td>--}}
{{--                                    <input type="checkbox" name="permission[]" value="product" id="product" {{ ($user->can('product') ? 'checked' : '') }}> Product--}}
{{--                                </td>--}}
{{--                            </tr>--}}



{{--                            <tr>--}}
{{--                                <td rowspan="3" style="vertical-align: middle;">--}}
{{--                                    <input type="checkbox" name="permission[]" value="stakeholder" id="stakeholder" {{ ($user->can('stakeholder') ? 'checked' : '') }}> Stakeholder--}}
{{--                                </td>--}}

{{--                                <td>--}}
{{--                                    <input type="checkbox" name="permission[]" value="stakeholders" id="stakeholders" {{ ($user->can('stakeholders') ? 'checked' : '') }}> Stakeholders--}}
{{--                                </td>--}}
{{--                            </tr>--}}

{{--                            <tr>--}}
{{--                                <td>--}}
{{--                                    <input type="checkbox" name="permission[]" value="budget_distribution" id="budget_distribution" {{ ($user->can('budget_distribution') ? 'checked' : '') }}> Budget Distribution--}}
{{--                                </td>--}}
{{--                            </tr>--}}

{{--                            <tr>--}}
{{--                                <td>--}}
{{--                                    <input type="checkbox" name="permission[]" value="stakeholder_payment" id="stakeholder_payment" {{ ($user->can('stakeholder_payment') ? 'checked' : '') }}> Stakeholder Payment--}}
{{--                                </td>--}}
{{--                            </tr>--}}


{{--                            <tr>--}}
{{--                                <td rowspan="2" style="vertical-align: middle;">--}}
{{--                                    <input type="checkbox" name="permission[]" value="bill_of_quantity" id="bill_of_quantity" {{ ($user->can('bill_of_quantity') ? 'checked' : '') }}> Bill of Quantity--}}
{{--                                </td>--}}

{{--                                <td>--}}
{{--                                    <input type="checkbox" name="permission[]" value="all_boq_costing" id="all_boq_costing" {{ ($user->can('all_boq_costing') ? 'checked' : '') }}> All BOQ Costing--}}
{{--                                </td>--}}
{{--                            </tr>--}}

{{--                            <tr>--}}
{{--                                <td>--}}
{{--                                    <input type="checkbox" name="permission[]" value="boq_report" id="boq_report" {{ ($user->can('boq_report') ? 'checked' : '') }}> BOQ Report--}}
{{--                                </td>--}}
{{--                            </tr>--}}


{{--                            <tr>--}}
{{--                                <td rowspan="3" style="vertical-align: middle;">--}}
{{--                                    <input type="checkbox" name="permission[]" value="requisition" id="requisition" {{ ($user->can('requisition') ? 'checked' : '') }}> Requisition--}}
{{--                                </td>--}}

{{--                                <td>--}}
{{--                                    <input type="checkbox" name="permission[]" value="requisitions" id="requisitions" {{ ($user->can('requisitions') ? 'checked' : '') }}> Requisitions--}}
{{--                                </td>--}}
{{--                            </tr>--}}

{{--                            <tr>--}}
{{--                                <td>--}}
{{--                                    <input type="checkbox" name="permission[]" value="requisition_report" id="requisition_report" {{ ($user->can('requisition_report') ? 'checked' : '') }}> Requisition Report--}}
{{--                                </td>--}}
{{--                            </tr>--}}

{{--                            <tr>--}}
{{--                                <td>--}}
{{--                                    <input type="checkbox" name="permission[]" value="requisition_and_boq" id="requisition_and_boq" {{ ($user->can('requisition_and_boq') ? 'checked' : '') }}> Requisition And BOQ--}}
{{--                                </td>--}}
{{--                            </tr>--}}

{{--                            <tr>--}}
{{--                                <td rowspan="7" style="vertical-align: middle;">--}}
{{--                                    <input type="checkbox" name="permission[]" value="purchase_control" id="purchase_control" {{ ($user->can('purchase_control') ? 'checked' : '') }}> Purchase Control--}}
{{--                                </td>--}}

{{--                                <td>--}}
{{--                                    <input type="checkbox" name="permission[]" value="supplier" id="supplier" {{ ($user->can('supplier') ? 'checked' : '') }}> Supplier--}}
{{--                                </td>--}}
{{--                            </tr>--}}
{{--                            <tr>--}}
{{--                                <td>--}}
{{--                                    <input type="checkbox" name="permission[]" value="purchase" id="purchase" {{ ($user->can('purchase') ? 'checked' : '') }}> Purchase--}}
{{--                                </td>--}}
{{--                            </tr>--}}
{{--                            <tr>--}}
{{--                                <td>--}}
{{--                                    <input type="checkbox" name="permission[]" value="purchase_receipt" id="purchase_receipt" {{ ($user->can('purchase_receipt') ? 'checked' : '') }}> Receipt--}}
{{--                                </td>--}}
{{--                            </tr>--}}
{{--                            <tr>--}}
{{--                                <td>--}}
{{--                                    <input type="checkbox" name="permission[]" value="purchase_inventory" id="purchase_inventory" {{ ($user->can('purchase_inventory') ? 'checked' : '') }}> Inventory--}}
{{--                                </td>--}}
{{--                            </tr>--}}
{{--                            <tr>--}}
{{--                                <td>--}}
{{--                                    <input type="checkbox" name="permission[]" value="supplier_payment" id="supplier_payment" {{ ($user->can('supplier_payment') ? 'checked' : '') }}> Supplier Payment--}}
{{--                                </td>--}}
{{--                            </tr>--}}
{{--                            <tr>--}}
{{--                                <td>--}}
{{--                                    <input type="checkbox" name="permission[]" value="utilize" id="utilize" {{ ($user->can('utilize') ? 'checked' : '') }}> Utilize--}}
{{--                                </td>--}}
{{--                            </tr>--}}
{{--                            <tr>--}}
{{--                                <td>--}}
{{--                                    <input type="checkbox" name="permission[]" value="purchase_and_requisition" id="purchase_and_requisition" {{ ($user->can('purchase_and_requisition') ? 'checked' : '') }}> Purchase & Requisition--}}
{{--                                </td>--}}
{{--                            </tr>--}}
{{--                            <tr>--}}
{{--                                <td rowspan="2" style="vertical-align: middle;">--}}
{{--                                    <input type="checkbox" name="permission[]" value="physical_progress" id="physical_progress" {{ ($user->can('physical_progress') ? 'checked' : '') }}> Physical Progress--}}
{{--                                </td>--}}

{{--                                <td>--}}
{{--                                    <input type="checkbox" name="permission[]" value="physical_progress_add" id="physical_progress_add" {{ ($user->can('physical_progress_add') ? 'checked' : '') }}> Physical Progress Add--}}
{{--                                </td>--}}
{{--                            </tr>--}}
{{--                            <tr>--}}
{{--                                <td>--}}
{{--                                    <input type="checkbox" name="permission[]" value="physical_progress_report" id="physical_progress_report" {{ ($user->can('physical_progress_report') ? 'checked' : '') }}> Physical Progress Report--}}
{{--                                </td>--}}
{{--                            </tr>--}}
{{--                            <tr>--}}
{{--                                <td rowspan="2" style="vertical-align: middle;">--}}
{{--                                    <input type="checkbox" name="permission[]" value="project_documentation" id="project_documentation" {{ ($user->can('project_documentation') ? 'checked' : '') }}> Project Documentation--}}
{{--                                </td>--}}

{{--                                <td>--}}
{{--                                    <input type="checkbox" name="permission[]" value="documentation_info" id="documentation_info" {{ ($user->can('documentation_info') ? 'checked' : '') }}> Documentation Info--}}
{{--                                </td>--}}
{{--                            </tr>--}}
{{--                            <tr>--}}
{{--                                <td>--}}
{{--                                    <input type="checkbox" name="permission[]" value="project_gallery" id="project_gallery" {{ ($user->can('project_gallery') ? 'checked' : '') }}> Project Gallery--}}
{{--                                </td>--}}
{{--                            </tr>--}}
{{--                            <tr>--}}
{{--                                <td rowspan="4" style="vertical-align: middle;">--}}
{{--                                    <input type="checkbox" name="permission[]" value="p_m_c" id="p_m_c" {{ ($user->can('p_m_c') ? 'checked' : '') }}> P M C--}}
{{--                                </td>--}}

{{--                                <td>--}}
{{--                                    <input type="checkbox" name="permission[]" value="account_head_type" id="account_head_type" {{ ($user->can('account_head_type') ? 'checked' : '') }}> Account Head Type--}}
{{--                                </td>--}}
{{--                            </tr>--}}
{{--                            <tr>--}}
{{--                                <td>--}}
{{--                                    <input type="checkbox" name="permission[]" value="account_sub_head_type" id="account_sub_head_type" {{ ($user->can('account_sub_head_type') ? 'checked' : '') }}> Account Sub Head Type--}}
{{--                                </td>--}}
{{--                            </tr>--}}
{{--                            <tr>--}}
{{--                                <td>--}}
{{--                                    <input type="checkbox" name="permission[]" value="project_wise_transaction" id="project_wise_transaction" {{ ($user->can('project_wise_transaction') ? 'checked' : '') }}> Project Wise Transaction--}}
{{--                                </td>--}}
{{--                            </tr>--}}
{{--                            <tr>--}}
{{--                                <td>--}}
{{--                                    <input type="checkbox" name="permission[]" value="balance_transfer" id="balance_transfer" {{ ($user->can('balance_transfer') ? 'checked' : '') }}> Balance Transfer--}}
{{--                                </td>--}}
{{--                            </tr>--}}
{{--                            <tr>--}}
{{--                                <td rowspan="5" style="vertical-align: middle;">--}}
{{--                                    <input type="checkbox" name="permission[]" value="report" id="report" {{ ($user->can('report') ? 'checked' : '') }}> Report--}}
{{--                                </td>--}}

{{--                                <td>--}}
{{--                                    <input type="checkbox" name="permission[]" value="supplier_report" id="supplier_report" {{ ($user->can('supplier_report') ? 'checked' : '') }}> Supplier Report--}}
{{--                                </td>--}}
{{--                            </tr>--}}
{{--                            <tr>--}}
{{--                                <td>--}}
{{--                                    <input type="checkbox" name="permission[]" value="purchase_report" id="purchase_report" {{ ($user->can('purchase_report') ? 'checked' : '') }}> Purchase Report--}}
{{--                                </td>--}}
{{--                            </tr>--}}
{{--                            <tr>--}}
{{--                                <td>--}}
{{--                                    <input type="checkbox" name="permission[]" value="stakeholder_report" id="stakeholder_report" {{ ($user->can('stakeholder_report') ? 'checked' : '') }}> Stakeholder Report--}}
{{--                                </td>--}}
{{--                            </tr>--}}
{{--                            <tr>--}}
{{--                                <td>--}}
{{--                                    <input type="checkbox" name="permission[]" value="project_report" id="project_report" {{ ($user->can('project_report') ? 'checked' : '') }}> Project Report--}}
{{--                                </td>--}}
{{--                            </tr>--}}
{{--                            <tr>--}}
{{--                                <td>--}}
{{--                                    <input type="checkbox" name="permission[]" value="progress_report" id="progress_report" {{ ($user->can('progress_report') ? 'checked' : '') }}> Progress Report--}}
{{--                                </td>--}}
{{--                            </tr>--}}
{{--                        </table>--}}
                    </div>
                    <!-- /.box-body -->

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
            <!-- /.card -->
        </div>
        <!--/.col (left) -->
    </div>
@endsection

@section('script')
    <!-- iCheck -->
{{--    <script src="{{ asset('themes/backend/plugins/iCheck/icheck.min.js') }}"></script>--}}
    <script>
        $(function () {
            $("#checkAll").click(function () {
                $('input:checkbox').not(this).prop('disabled', this.disabled);
                $('input:checkbox').not(this).prop('checked', this.checked);
                init();
            });

            // Administrator
            $('#administrator').click(function () {
                if ($(this).prop('checked')) {
                    $('#users').attr("disabled", false);
                    $('#projects').attr("disabled", false);
                    $('#warehouses').attr("disabled", false);
                    $('#units').attr("disabled", false);
                } else {
                    $('#users').attr("disabled", true);
                    $('#projects').attr("disabled", true);
                    $('#warehouses').attr("disabled", true);
                    $('#units').attr("disabled", true);
                }
            });

            // Bank & Account
            $('#bank_and_account').click(function () {
                if ($(this).prop('checked')) {
                    $('#bank').attr("disabled", false);
                    $('#branch').attr("disabled", false);
                    $('#account').attr("disabled", false);
                    $('#cash').attr("disabled", false);
                } else {
                    $('#bank').attr("disabled", true);
                    $('#branch').attr("disabled", true);
                    $('#account').attr("disabled", true);
                    $('#cash').attr("disabled", true);
                }
            });

            // Project Control
            $('#project_control').click(function () {
                if ($(this).prop('checked')) {
                    $('#project_duration').attr("disabled", false);
                    $('#project_budget').attr("disabled", false);
                    $('#segment').attr("disabled", false);
                    $('#product').attr("disabled", false);
                } else {
                    $('#project_duration').attr("disabled", true);
                    $('#project_budget').attr("disabled", true);
                    $('#segment').attr("disabled", true);
                    $('#product').attr("disabled", true);
                }
            });

            // Stakeholder
            $('#stakeholder').click(function () {
                if ($(this).prop('checked')) {
                    $('#stakeholders').attr("disabled", false);
                    $('#budget_distribution').attr("disabled", false);
                    $('#stakeholder_payment').attr("disabled", false);
                } else {
                    $('#stakeholders').attr("disabled", true);
                    $('#budget_distribution').attr("disabled", true);
                    $('#stakeholder_payment').attr("disabled", true);
                }
            });

            // Bill of Quantity
            $('#bill_of_quantity').click(function () {
                if ($(this).prop('checked')) {
                    $('#all_boq_costing').attr("disabled", false);
                    $('#boq_report').attr("disabled", false);
                } else {
                    $('#all_boq_costing').attr("disabled", true);
                    $('#boq_report').attr("disabled", true);
                }
            });

            // Requisition
            $('#requisition').click(function () {
                if ($(this).prop('checked')) {
                    $('#requisitions').attr("disabled", false);
                    $('#requisition_report').attr("disabled", false);
                    $('#requisition_and_boq').attr("disabled", false);
                } else {
                    $('#requisitions').attr("disabled", true);
                    $('#requisition_report').attr("disabled", true);
                    $('#requisition_and_boq').attr("disabled", true);
                }
            });

            // Purchase Control
            $('#purchase_control').click(function () {
                if ($(this).prop('checked')) {
                    $('#supplier').attr("disabled", false);
                    $('#purchase').attr("disabled", false);
                    $('#purchase_receipt').attr("disabled", false);
                    $('#purchase_inventory').attr("disabled", false);
                    $('#supplier_payment').attr("disabled", false);
                    $('#utilize').attr("disabled", false);
                    $('#purchase_and_requisition').attr("disabled", false);
                } else {
                    $('#supplier').attr("disabled", true);
                    $('#purchase').attr("disabled", true);
                    $('#purchase_receipt').attr("disabled", true);
                    $('#purchase_inventory').attr("disabled", true);
                    $('#supplier_payment').attr("disabled", true);
                    $('#utilize').attr("disabled", true);
                    $('#purchase_and_requisition').attr("disabled", true);
                }
            });
            //Physical Progress
            $('#physical_progress').click(function () {
                if ($(this).prop('checked')) {
                    $('#physical_progress_add').attr("disabled", false);
                    $('#physical_progress_report').attr("disabled", false);
                } else {
                    $('#physical_progress_add').attr("disabled", true);
                    $('#physical_progress_report').attr("disabled", true);
                }
            });

            //Project Documentation
            $('#project_documentation').click(function () {
                if ($(this).prop('checked')) {
                    $('#documentation_info').attr("disabled", false);
                    $('#project_gallery').attr("disabled", false);
                } else {
                    $('#documentation_info').attr("disabled", true);
                    $('#project_gallery').attr("disabled", true);
                }
            });
            //P M C
            $('#p_m_c').click(function () {
                if ($(this).prop('checked')) {
                    $('#account_head_type').attr("disabled", false);
                    $('#account_sub_head_type').attr("disabled", false);
                    $('#project_wise_transaction').attr("disabled", false);
                    $('#balance_transfer').attr("disabled", false);
                } else {
                    $('#account_head_type').attr("disabled", true);
                    $('#account_sub_head_type').attr("disabled", true);
                    $('#project_wise_transaction').attr("disabled", true);
                    $('#balance_transfer').attr("disabled", true);
                }
            });
            //Report
            $('#report').click(function () {
                if ($(this).prop('checked')) {
                    $('#supplier_report').attr("disabled", false);
                    $('#purchase_report').attr("disabled", false);
                    $('#stakeholder_report').attr("disabled", false);
                    $('#project_report').attr("disabled", false);
                    $('#progress_report').attr("disabled", false);
                } else {
                    $('#supplier_report').attr("disabled", true);
                    $('#purchase_report').attr("disabled", true);
                    $('#stakeholder_report').attr("disabled", true);
                    $('#project_report').attr("disabled", true);
                    $('#progress_report').attr("disabled", true);
                }
            });

            init();
        });

        function init() {
            if (!$('#administrator').prop('checked')) {
                $('#users').attr("disabled", true);
                $('#projects').attr("disabled", true);
                $('#warehouses').attr("disabled", true);
                $('#units').attr("disabled", true);
            }

            if (!$('#bank_and_account').prop('checked')) {
                $('#bank').attr("disabled", true);
                $('#branch').attr("disabled", true);
                $('#account').attr("disabled", true);
                $('#cash').attr("disabled", true);
            }

            if (!$('#project_control').prop('checked')) {
                $('#project_duration').attr("disabled", true);
                $('#project_budget').attr("disabled", true);
                $('#segment').attr("disabled", true);
                $('#product').attr("disabled", true);
            }

            if (!$('#stakeholder').prop('checked')) {
                $('#stakeholders').attr("disabled", true);
                $('#budget_distribution').attr("disabled", true);
                $('#stakeholder_payment').attr("disabled", true);
            }

            if (!$('#bill_of_quantity').prop('checked')) {
                $('#all_boq_costing').attr("disabled", true);
                $('#boq_report').attr("disabled", true);
            }

            if (!$('#requisition').prop('checked')) {
                $('#requisitions').attr("disabled", true);
                $('#requisition_report').attr("disabled", true);
                $('#requisition_and_boq').attr("disabled", true);
            }

            if (!$('#purchase_control').prop('checked')) {
                $('#supplier').attr("disabled", true);
                $('#purchase').attr("disabled", true);
                $('#purchase_receipt').attr("disabled", true);
                $('#purchase_inventory').attr("disabled", true);
                $('#supplier_payment').attr("disabled", true);
                $('#utilize').attr("disabled", true);
                $('#purchase_and_requisition').attr("disabled", true);
            }

            if (!$('#physical_progress').prop('checked')) {
                $('#physical_progress_add').attr("disabled", true);
                $('#physical_progress_report').attr("disabled", true);
            }

            if (!$('#project_documentation').prop('checked')) {
                $('#documentation_info').attr("disabled", true);
                $('#project_gallery').attr("disabled", true);
            }
            if (!$('#p_m_c').prop('checked')) {
                $('#account_head_type').attr("disabled", true);
                $('#account_sub_head_type').attr("disabled", true);
                $('#project_wise_transaction').attr("disabled", true);
                $('#balance_transfer').attr("disabled", true);
            }
            if (!$('#report').prop('checked')) {
                $('#supplier_report').attr("disabled", true);
                $('#purchase_report').attr("disabled", true);
                $('#stakeholder_report').attr("disabled", true);
                $('#project_report').attr("disabled", true);
                $('#progress_report').attr("disabled", true);
            }

        }
    </script>
@endsection
