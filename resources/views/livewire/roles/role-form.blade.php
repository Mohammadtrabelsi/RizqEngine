<div>
<form wire:submit="save">
                    
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">{{ $roleId ? __('roles.update_role') : __('roles.create_role') }} <i class="bi bi-check"></i>
                        </button>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <div class="form-group">
                                <label for="name">{{ __('roles.role_name') }} <span class="text-danger">*</span></label>
                                <input class="form-control @error('name') is-invalid @enderror" type="text" wire:model="name">
                                @error('name') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                            </div>

                            <hr>

                            <div class="form-group">
                                <label for="permissions">{{ __('roles.permissions') }} <span class="text-danger">*</span></label>
                                @error('permissions') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="select-all" wire:model.live="selectAll">
                                    <label class="custom-control-label" for="select-all">{{ __('roles.give_all_permissions') }}</label>
                                </div>
                            </div>

                            <div class="row">
                                <!-- Dashboard Permissions -->
                                <div class="col-lg-4 col-md-6 mb-3">
                                    <div class="card h-100 border-0 shadow">
                                        <div class="card-header">
                                            {{ __('roles.dashboard') }}
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-6">
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input"
                                                               id="show_total_stats" wire:model="permissions"
                                                               value="show_total_stats">
                                                        <label class="custom-control-label" for="show_total_stats">{{ __('roles.total_stats') }}</label>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input"
                                                               id="show_notifications" wire:model="permissions"
                                                               value="show_notifications">
                                                        <label class="custom-control-label" for="show_notifications">{{ __('roles.notifications') }}</label>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input"
                                                               id="show_month_overview" wire:model="permissions"
                                                               value="show_month_overview">
                                                        <label class="custom-control-label" for="show_month_overview">{{ __('roles.month_overview') }}</label>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input"
                                                               id="show_weekly_sales_purchases" wire:model="permissions"
                                                               value="show_weekly_sales_purchases">
                                                        <label class="custom-control-label" for="show_weekly_sales_purchases">{{ __('roles.weekly_sales_purchases') }}</label>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input"
                                                               id="show_monthly_cashflow" wire:model="permissions"
                                                               value="show_monthly_cashflow">
                                                        <label class="custom-control-label" for="show_monthly_cashflow">{{ __('roles.monthly_cashflow') }}</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- User Management Permission -->
                                <div class="col-lg-4 col-md-6 mb-3">
                                    <div class="card h-100 border-0 shadow">
                                        <div class="card-header">
                                            {{ __('roles.user_management') }}
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-6">
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input"
                                                               id="access_user_management" wire:model="permissions"
                                                               value="access_user_management">
                                                        <label class="custom-control-label" for="access_user_management">{{ __('roles.access') }}</label>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input"
                                                               id="edit_own_profile" wire:model="permissions"
                                                               value="edit_own_profile">
                                                        <label class="custom-control-label" for="edit_own_profile">{{ __('roles.edit_own_profile') }}</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Products Permission -->
                                <div class="col-lg-4 col-md-6 mb-3">
                                    <div class="card h-100 border-0 shadow">
                                        <div class="card-header">
                                            {{ __('roles.products') }}
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-6">
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input"
                                                               id="access_products" wire:model="permissions"
                                                               value="access_products">
                                                        <label class="custom-control-label" for="access_products">{{ __('roles.access') }}</label>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input"
                                                               id="show_products" wire:model="permissions"
                                                               value="show_products">
                                                        <label class="custom-control-label" for="show_products">{{ __('roles.view') }}</label>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input"
                                                               id="create_products" wire:model="permissions"
                                                               value="create_products">
                                                        <label class="custom-control-label" for="create_products">{{ __('roles.create') }}</label>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input"
                                                               id="edit_products" wire:model="permissions"
                                                               value="edit_products">
                                                        <label class="custom-control-label" for="edit_products">{{ __('roles.edit') }}</label>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input"
                                                               id="delete_products" wire:model="permissions"
                                                               value="delete_products">
                                                        <label class="custom-control-label" for="delete_products">{{ __('roles.delete') }}</label>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input"
                                                               id="access_product_categories" wire:model="permissions"
                                                               value="access_product_categories">
                                                        <label class="custom-control-label" for="access_product_categories">{{ __('roles.product_categories') }}</label>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input"
                                                               id="print_barcodes" wire:model="permissions"
                                                               value="print_barcodes">
                                                        <label class="custom-control-label" for="print_barcodes">{{ __('roles.print_barcodes') }}</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Adjustments Permission -->
                                <div class="col-lg-4 col-md-6 mb-3">
                                    <div class="card h-100 border-0 shadow">
                                        <div class="card-header">
                                            {{ __('roles.adjustments') }}
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-6">
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input"
                                                               id="access_adjustments" wire:model="permissions"
                                                               value="access_adjustments">
                                                        <label class="custom-control-label" for="access_adjustments">{{ __('roles.access') }}</label>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input"
                                                               id="create_adjustments" wire:model="permissions"
                                                               value="create_adjustments">
                                                        <label class="custom-control-label" for="create_adjustments">{{ __('roles.create') }}</label>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input"
                                                               id="show_adjustments" wire:model="permissions"
                                                               value="show_adjustments">
                                                        <label class="custom-control-label" for="show_adjustments">{{ __('roles.view') }}</label>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input"
                                                               id="edit_adjustments" wire:model="permissions"
                                                               value="edit_adjustments">
                                                        <label class="custom-control-label" for="edit_adjustments">{{ __('roles.edit') }}</label>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input"
                                                               id="delete_adjustments" wire:model="permissions"
                                                               value="delete_adjustments">
                                                        <label class="custom-control-label" for="delete_adjustments">{{ __('roles.delete') }}</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Quotations Permission -->
                                <div class="col-lg-4 col-md-6 mb-3">
                                    <div class="card h-100 border-0 shadow">
                                        <div class="card-header">
                                            {{ __('roles.quotations') }}
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-6">
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input"
                                                               id="access_quotations" wire:model="permissions"
                                                               value="access_quotations">
                                                        <label class="custom-control-label" for="access_quotations">{{ __('roles.access') }}</label>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input"
                                                               id="create_quotations" wire:model="permissions"
                                                               value="create_quotations">
                                                        <label class="custom-control-label" for="create_quotations">{{ __('roles.create') }}</label>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input"
                                                               id="show_quotations" wire:model="permissions"
                                                               value="show_quotations">
                                                        <label class="custom-control-label" for="show_quotations">{{ __('roles.view') }}</label>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input"
                                                               id="edit_quotations" wire:model="permissions"
                                                               value="edit_quotations">
                                                        <label class="custom-control-label" for="edit_quotations">{{ __('roles.edit') }}</label>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input"
                                                               id="delete_quotations" wire:model="permissions"
                                                               value="delete_quotations">
                                                        <label class="custom-control-label" for="delete_quotations">{{ __('roles.delete') }}</label>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input"
                                                               id="send_quotation_mails" wire:model="permissions"
                                                               value="send_quotation_mails">
                                                        <label class="custom-control-label" for="send_quotation_mails">{{ __('roles.send_emails') }}</label>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input"
                                                               id="create_quotation_sales" wire:model="permissions"
                                                               value="create_quotation_sales">
                                                        <label class="custom-control-label" for="create_quotation_sales">{{ __('roles.create_sale_from_quotation') }}</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Expenses Permission -->
                                <div class="col-lg-4 col-md-6 mb-3">
                                    <div class="card h-100 border-0 shadow">
                                        <div class="card-header">
                                            {{ __('roles.expenses') }}
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-6">
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input"
                                                               id="access_expenses" wire:model="permissions"
                                                               value="access_expenses">
                                                        <label class="custom-control-label" for="access_expenses">{{ __('roles.access') }}</label>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input"
                                                               id="create_expenses" wire:model="permissions"
                                                               value="create_expenses">
                                                        <label class="custom-control-label" for="create_expenses">{{ __('roles.create') }}</label>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input"
                                                               id="edit_expenses" wire:model="permissions"
                                                               value="edit_expenses">
                                                        <label class="custom-control-label" for="edit_expenses">{{ __('roles.edit') }}</label>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input"
                                                               id="delete_expenses" wire:model="permissions"
                                                               value="delete_expenses">
                                                        <label class="custom-control-label" for="delete_expenses">{{ __('roles.delete') }}</label>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input"
                                                               id="access_expense_categories" wire:model="permissions"
                                                               value="access_expense_categories">
                                                        <label class="custom-control-label" for="access_expense_categories">{{ __('roles.categories') }}</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Customers Permission -->
                                <div class="col-lg-4 col-md-6 mb-3">
                                    <div class="card h-100 border-0 shadow">
                                        <div class="card-header">
                                            {{ __('roles.customers') }}
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-6">
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input"
                                                               id="access_customers" wire:model="permissions"
                                                               value="access_customers">
                                                        <label class="custom-control-label" for="access_customers">{{ __('roles.access') }}</label>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input"
                                                               id="create_customers" wire:model="permissions"
                                                               value="create_customers">
                                                        <label class="custom-control-label" for="create_customers">{{ __('roles.create') }}</label>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input"
                                                               id="show_customers" wire:model="permissions"
                                                               value="show_customers">
                                                        <label class="custom-control-label" for="show_customers">{{ __('roles.view') }}</label>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input"
                                                               id="edit_customers" wire:model="permissions"
                                                               value="edit_customers">
                                                        <label class="custom-control-label" for="edit_customers">{{ __('roles.edit') }}</label>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input"
                                                               id="delete_customers" wire:model="permissions"
                                                               value="delete_customers">
                                                        <label class="custom-control-label" for="delete_customers">{{ __('roles.delete') }}</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Suppliers Permission -->
                                <div class="col-lg-4 col-md-6 mb-3">
                                    <div class="card h-100 border-0 shadow">
                                        <div class="card-header">
                                            {{ __('roles.suppliers') }}
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-6">
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input"
                                                               id="access_suppliers" wire:model="permissions"
                                                               value="access_suppliers">
                                                        <label class="custom-control-label" for="access_suppliers">{{ __('roles.access') }}</label>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input"
                                                               id="create_suppliers" wire:model="permissions"
                                                               value="create_suppliers">
                                                        <label class="custom-control-label" for="create_suppliers">{{ __('roles.create') }}</label>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input"
                                                               id="show_suppliers" wire:model="permissions"
                                                               value="show_suppliers">
                                                        <label class="custom-control-label" for="show_suppliers">{{ __('roles.view') }}</label>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input"
                                                               id="edit_suppliers" wire:model="permissions"
                                                               value="edit_suppliers">
                                                        <label class="custom-control-label" for="edit_suppliers">{{ __('roles.edit') }}</label>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input"
                                                               id="delete_customers" wire:model="permissions"
                                                               value="delete_customers">
                                                        <label class="custom-control-label" for="delete_customers">{{ __('roles.delete') }}</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Sales Permission -->
                                <div class="col-lg-4 col-md-6 mb-3">
                                    <div class="card h-100 border-0 shadow">
                                        <div class="card-header">
                                            {{ __('roles.sales') }}
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-6">
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input"
                                                               id="access_sales" wire:model="permissions"
                                                               value="access_sales">
                                                        <label class="custom-control-label" for="access_sales">{{ __('roles.access') }}</label>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input"
                                                               id="create_sales" wire:model="permissions"
                                                               value="create_sales">
                                                        <label class="custom-control-label" for="create_sales">{{ __('roles.create') }}</label>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input"
                                                               id="show_sales" wire:model="permissions"
                                                               value="show_suppliers">
                                                        <label class="custom-control-label" for="show_sales">{{ __('roles.view') }}</label>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input"
                                                               id="edit_sales" wire:model="permissions"
                                                               value="edit_sales">
                                                        <label class="custom-control-label" for="edit_sales">{{ __('roles.edit') }}</label>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input"
                                                               id="delete_sales" wire:model="permissions"
                                                               value="delete_sales">
                                                        <label class="custom-control-label" for="delete_sales">{{ __('roles.delete') }}</label>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input"
                                                               id="create_pos_sales" wire:model="permissions"
                                                               value="create_pos_sales">
                                                        <label class="custom-control-label" for="create_pos_sales">{{ __('roles.pos_system') }}</label>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input"
                                                               id="access_sale_payments" wire:model="permissions"
                                                               value="access_sale_payments">
                                                        <label class="custom-control-label" for="access_sale_payments">{{ __('roles.payments') }}</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Sale Returns Permission -->
                                <div class="col-lg-4 col-md-6 mb-3">
                                    <div class="card h-100 border-0 shadow">
                                        <div class="card-header">
                                            {{ __('roles.sale_returns') }}
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-6">
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input"
                                                               id="access_sale_returns" wire:model="permissions"
                                                               value="access_sale_returns">
                                                        <label class="custom-control-label" for="access_sale_returns">{{ __('roles.access') }}</label>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input"
                                                               id="create_sale_returns" wire:model="permissions"
                                                               value="create_sale_returns">
                                                        <label class="custom-control-label" for="create_sale_returns">{{ __('roles.create') }}</label>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input"
                                                               id="show_sale_returns" wire:model="permissions"
                                                               value="show_sale_returns">
                                                        <label class="custom-control-label" for="show_sale_returns">{{ __('roles.view') }}</label>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input"
                                                               id="edit_sale_returns" wire:model="permissions"
                                                               value="edit_sale_returns">
                                                        <label class="custom-control-label" for="edit_sale_returns">{{ __('roles.edit') }}</label>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input"
                                                               id="delete_sale_returns" wire:model="permissions"
                                                               value="delete_sale_returns">
                                                        <label class="custom-control-label" for="delete_sale_returns">{{ __('roles.delete') }}</label>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input"
                                                               id="access_sale_return_payments" wire:model="permissions"
                                                               value="access_sale_return_payments">
                                                        <label class="custom-control-label" for="access_sale_return_payments">{{ __('roles.payments') }}</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Purchases Permission -->
                                <div class="col-lg-4 col-md-6 mb-3">
                                    <div class="card h-100 border-0 shadow">
                                        <div class="card-header">
                                            Purchases
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-6">
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input"
                                                               id="access_purchases" wire:model="permissions"
                                                               value="access_purchases">
                                                        <label class="custom-control-label" for="access_purchases">{{ __('roles.access') }}</label>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input"
                                                               id="create_purchases" wire:model="permissions"
                                                               value="create_purchases">
                                                        <label class="custom-control-label" for="create_purchases">{{ __('roles.create') }}</label>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input"
                                                               id="show_purchases" wire:model="permissions"
                                                               value="show_purchases">
                                                        <label class="custom-control-label" for="show_purchases">{{ __('roles.view') }}</label>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input"
                                                               id="edit_purchases" wire:model="permissions"
                                                               value="edit_purchases">
                                                        <label class="custom-control-label" for="edit_purchases">{{ __('roles.edit') }}</label>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input"
                                                               id="delete_purchases" wire:model="permissions"
                                                               value="delete_purchases">
                                                        <label class="custom-control-label" for="delete_purchases">{{ __('roles.delete') }}</label>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input"
                                                               id="access_purchase_payments" wire:model="permissions"
                                                               value="access_purchase_payments">
                                                        <label class="custom-control-label" for="access_purchase_payments">{{ __('roles.payments') }}</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Purchases Returns Permission -->
                                <div class="col-lg-4 col-md-6 mb-3">
                                    <div class="card h-100 border-0 shadow">
                                        <div class="card-header">
                                            {{ __('roles.purchase_returns') }}
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-6">
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input"
                                                               id="access_purchase_returns" wire:model="permissions"
                                                               value="access_purchase_returns">
                                                        <label class="custom-control-label" for="access_purchase_returns">{{ __('roles.access') }}</label>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input"
                                                               id="create_purchase_returns" wire:model="permissions"
                                                               value="create_purchase_returns">
                                                        <label class="custom-control-label" for="create_purchase_returns">{{ __('roles.create') }}</label>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input"
                                                               id="show_purchase_returns" wire:model="permissions"
                                                               value="show_purchase_returns">
                                                        <label class="custom-control-label" for="show_purchase_returns">{{ __('roles.view') }}</label>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input"
                                                               id="edit_purchase_returns" wire:model="permissions"
                                                               value="edit_purchase_returns">
                                                        <label class="custom-control-label" for="edit_purchase_returns">{{ __('roles.edit') }}</label>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input"
                                                               id="delete_purchase_returns" wire:model="permissions"
                                                               value="delete_purchase_returns">
                                                        <label class="custom-control-label" for="delete_purchase_returns">{{ __('roles.delete') }}</label>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input"
                                                               id="access_purchase_return_payments" wire:model="permissions"
                                                               value="access_purchase_return_payments">
                                                        <label class="custom-control-label" for="access_purchase_return_payments">{{ __('roles.payments') }}</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Currencies Permission -->
                                <div class="col-lg-4 col-md-6 mb-3">
                                    <div class="card h-100 border-0 shadow">
                                        <div class="card-header">
                                            {{ __('roles.currencies') }}    
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-6">
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input"
                                                               id="access_currencies" wire:model="permissions"
                                                               value="access_currencies">
                                                        <label class="custom-control-label" for="access_currencies">{{ __('roles.access') }}</label>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input"
                                                               id="create_currencies" wire:model="permissions"
                                                               value="create_currencies">
                                                        <label class="custom-control-label" for="create_currencies">{{ __('roles.create') }}</label>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input"
                                                               id="edit_currencies" wire:model="permissions"
                                                               value="edit_currencies">
                                                        <label class="custom-control-label" for="edit_currencies">{{ __('roles.edit') }}</label>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input"
                                                               id="delete_currencies" wire:model="permissions"
                                                               value="delete_currencies">
                                                        <label class="custom-control-label" for="delete_currencies">{{ __('roles.delete') }}</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Reports -->
                                <div class="col-lg-4 col-md-6 mb-3">
                                    <div class="card h-100 border-0 shadow">
                                        <div class="card-header">
                                            {{ __('roles.reports') }}
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-6">
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input"
                                                               id="access_reports" wire:model="permissions"
                                                               value="access_reports">
                                                        <label class="custom-control-label" for="access_reports">{{ __('roles.access') }}</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Settings -->
                                <div class="col-lg-4 col-md-6 mb-3">
                                    <div class="card h-100 border-0 shadow">
                                        <div class="card-header">
                                            {{ __('roles.settings') }}
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-6">
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input"
                                                               id="access_settings" wire:model="permissions"
                                                               value="access_settings">
                                                        <label class="custom-control-label" for="access_settings">{{ __('roles.access') }}</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Activity Logs -->
                                <div class="col-lg-4 col-md-6 mb-3">
                                    <div class="card h-100 border-0 shadow">
                                        <div class="card-header">
                                            {{ __('roles.activity_logs') }}
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-6">
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input"
                                                               id="access_activity_logs" wire:model="permissions"
                                                               value="access_activity_logs">
                                                        <label class="custom-control-label" for="access_activity_logs">{{ __('roles.access') }}</label>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input"
                                                               id="delete_activity_logs" wire:model="permissions"
                                                               value="delete_activity_logs">
                                                        <label class="custom-control-label" for="delete_activity_logs">{{ __('roles.delete') }}</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </form>
</div>
