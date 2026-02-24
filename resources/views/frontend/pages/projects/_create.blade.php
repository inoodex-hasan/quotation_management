@extends('frontend.layouts.app')
@section('content')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
    /* Make all selectors more specific to avoid conflicts */
    .content.container-fluid .select2-container--default.select2-container--open .select2-selection--single .select2-selection__arrow b {
        border-color: transparent transparent #888 transparent !important;
        border-width: 0 !important;
    }

    .content.container-fluid .select2-container--default .select2-selection--single .select2-selection__arrow b {
        border-color: #888 transparent transparent transparent !important;
        border-style: solid;
        border-width: 0 !important;
        height: 0;
        left: 50%;
        margin-left: -4px;
        margin-top: -2px;
        position: absolute;
        top: 50%;
        width: 0;
    }

    .content.container-fluid .card select,
    .content.container-fluid .card input {
        border-color: #000 !important;
        border-width: 1px;
        box-shadow: none !important;
    }

    .content.container-fluid .card label {
        color: #000 !important;
        font-weight: 500;
    }

    .content.container-fluid .select2-container--default .select2-selection--single {
        background-color: #fff !important;
        border: 1px solid #000 !important;
        border-radius: 4px;
        height: 30px !important;
        display: flex;
        align-items: center;
        width: 100% !important;
        padding-left: 8px;
    }

    .content.container-fluid .select2-selection__rendered {
        line-height: 30px !important;
    }

    .content.container-fluid .select2-container {
        width: 100% !important;
    }

    .content.container-fluid .card {
        border: 1px solid #ddd;
        border-radius: 8px;
    }

    .content.container-fluid .card-body {
        padding: 20px;
    }

    .content.container-fluid .content-page-header h6 {
        color: #198754;
        font-weight: 600;
        margin-bottom: 15px;
    }

    .content.container-fluid .input-block {
        margin-bottom: 15px;
    }

    .content.container-fluid .create-btn-sm {
        background: #ff3300;
        color: #fff;
        padding: 4px 12px;
        font-size: 14px;
        border: 1px solid #ff3300;
        border-radius: 4px;
        transition: 0.3s;
    }

    .content.container-fluid .create-btn-sm:hover {
        background: transparent;
        color: #ff3300;
        border: 1px solid #ff3300;
    }

    /* Modal specific styles */
    #clientSelectionModal .client-card {
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 10px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    #clientSelectionModal .client-card:hover {
        border-color: #198754;
        background-color: #f8f9fa;
    }

    #clientSelectionModal .client-card.selected {
        border-color: #198754;
        background-color: #e8f5e8;
    }

    #clientSelectionModal .client-info {
        margin-bottom: 5px;
    }

    #clientSelectionModal .client-name {
        font-weight: bold;
        color: #333;
    }

    #clientSelectionModal .client-details {
        color: #666;
        font-size: 0.9em;
    }

    #clientSelectionModal .modal-header {
        background-color: #198754;
        color: white;
    }

    #clientSelectionModal .search-box {
        margin-bottom: 20px;
    }

    #clientSelectionModal .no-clients {
        text-align: center;
        padding: 20px;
        color: #666;
    }

    /* Fix for sidebar - explicitly remove text-decoration */
    .sidebar-menu a,
    .nav-sidebar a,
    .sidebar-nav a,
    [class*="sidebar"] a {
        text-decoration: none !important;
    }
</style>

    <!-- Client Selection Modal -->
    <div class="modal fade" id="clientSelectionModal" tabindex="-1" aria-labelledby="clientSelectionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="clientSelectionModalLabel">Select Existing Client</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Search Box -->
                    <div class="search-box">
                        <input type="text" id="clientSearch" class="form-control" placeholder="Search clients by name, phone, or email...">
                    </div>

                    <!-- Clients List -->
                    <div id="clientsList">
                        @foreach($existingClients as $client)
                            <div class="client-card" data-client-id="{{ $client->id }}" 
                                 data-client-name="{{ $client->name }}"
                                 data-client-phone="{{ $client->phone }}"
                                 data-client-email="{{ $client->email }}"
                                 data-client-address="{{ $client->address }}">
                                <div class="client-info">
                                    <div class="client-name">{{ $client->name }}</div>
                                    <div class="client-details">
                                        <strong>Phone:</strong> {{ $client->phone }} | 
                                        <strong>Email:</strong> {{ $client->email }}
                                    </div>
                                    <div class="client-details">
                                        <strong>Address:</strong> {{ $client->address }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        
                        @if($existingClients->isEmpty())
                            <div class="no-clients">
                                <p>No existing clients found.</p>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" id="selectClientBtn" disabled>Select Client</button>
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('projects.store') }}" method="post">
        @csrf
        <div class="content container-fluid pt-0">
            <div class="card mb-3">
                <div class="card-body">
                    <div class="page-header mb-3">
                        <div class="content-page-header mb-3">
                            <h6>Client Information</h6>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group-item mb-0 pb-0">
                                <!-- Client Type Selection -->
                                <div class="row mb-4">
                                    <div class="col-md-4">
                                        <div class="input-block mb-3">
                                            <label class="form-label">Client Type <span class="text-danger">*</span></label>
                                            <div class="d-flex gap-3">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="client_type"
                                                        id="newClient" value="new" checked>
                                                    <label class="form-check-label" for="newClient">
                                                        New Client
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="client_type"
                                                        id="existingClient" value="existing">
                                                    <label class="form-check-label" for="existingClient">
                                                        Existing Client
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-6 col-sm-12">
                                        <div class="input-block mb-3">
                                            <label class="form-label">Project Name <span class="text-danger">*</span></label>
                                            <input type="text" name="project_name" class="form-control"
                                                placeholder="Enter Project Name" value="{{ old('project_name') }}" required
                                                autocomplete="off">
                                        </div>
                                    </div>
                                </div>

                                <!-- New Client Form -->
                                <div id="newClientForm">
                                    <div class="row">
                                        <div class="col-lg-4 col-md-6 col-sm-12">
                                            <div class="input-block mb-3">
                                                <label class="form-label">Client/Company Name <span class="text-danger">*</span></label>
                                                <input type="text" name="client_name" class="form-control"
                                                    id="newClientName" placeholder="Enter Client Name"
                                                    value="{{ old('client_name') }}" required autocomplete="off">
                                            </div>
                                        </div>

                                        <div class="col-lg-4 col-md-6 col-sm-12">
                                            <div class="input-block mb-3">
                                                <label class="form-label">Phone <span class="text-danger">*</span></label>
                                                <input type="tel" class="form-control" name="client_phone"
                                                    id="newClientPhone" pattern="[0-9]{11}" maxlength="11"
                                                    placeholder="Enter phone number" required
                                                    value="{{ old('client_phone') }}">
                                            </div>
                                        </div>

                                        <div class="col-lg-4 col-md-6 col-sm-12">
                                            <div class="input-block mb-3">
                                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                                <input type="email" class="form-control" placeholder="Enter Email"
                                                    name="client_email" id="newClientEmail"
                                                    value="{{ old('client_email') }}" required autocomplete="off">
                                            </div>
                                        </div>

                                        <div class="col-lg-4 col-md-6 col-sm-12">
                                            <div class="input-block mb-3">
                                                <label class="form-label">Address <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" placeholder="Enter Address"
                                                    name="client_address" id="newClientAddress"
                                                    value="{{ old('client_address') }}" required autocomplete="off">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Existing Client Display (Hidden by default) -->
                                <div id="existingClientForm" style="display: none;">
                                    <div class="row">
                                        <div class="col-lg-4 col-md-6 col-sm-12">
                                            <div class="input-block mb-3">
                                                <label class="form-label">Selected Client <span class="text-danger">*</span></label>
                                                <div class="d-flex align-items-center gap-2">
                                                    <input type="text" class="form-control" id="selectedClientDisplay" 
                                                           readonly style="background-color: #f8f9fa;">
                                                    <button type="button" class="btn btn-outline-primary btn-sm" 
                                                            id="changeClientBtn">
                                                        Change
                                                    </button>
                                                </div>
                                                <input type="hidden" name="existing_client_id" id="selectedClientId">
                                            </div>
                                        </div>

                                        <div class="col-lg-4 col-md-6 col-sm-12">
                                            <div class="input-block mb-3">
                                                <label class="form-label">Phone</label>
                                                <input type="tel" class="form-control" id="existingClientPhone"
                                                    readonly style="background-color: #f8f9fa;">
                                            </div>
                                        </div>

                                        <div class="col-lg-4 col-md-6 col-sm-12">
                                            <div class="input-block mb-3">
                                                <label class="form-label">Email</label>
                                                <input type="email" class="form-control" id="existingClientEmail"
                                                    readonly style="background-color: #f8f9fa;">
                                            </div>
                                        </div>

                                        <div class="col-lg-4 col-md-6 col-sm-12">
                                            <div class="input-block mb-3">
                                                <label class="form-label">Address</label>
                                                <input type="text" class="form-control" id="existingClientAddress"
                                                    readonly style="background-color: #f8f9fa;">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Rest of your form content (Project Information, Project Items, etc.) -->
            <div class="card mb-3">
                <div class="card-body">
                    <div class="page-header mb-3">
                        <div class="content-page-header mb-3">
                            <h6>Project Information</h6>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group-item mb-0 pb-0">
                                <div class="row">
                                    <div class="col-lg-4 col-md-6 col-sm-12">
                                        <div class="input-block mb-3">
                                            <label class="form-label">Budget<span class="text-danger">*</span></label>
                                            <input type="number" name="budget" class="form-control"
                                                placeholder="Enter Project Budget" value="{{ old('budget') }}"
                                                step="0.01" min="0">
                                        </div>
                                    </div>

                                    <div class="col-lg-4 col-md-6 col-sm-12">
                                        <div class="input-block mb-3">
                                            <label class="form-label">Start Date </label>
                                            <input type="date" name="start_date" class="form-control"
                                                value="{{ old('start_date') }}">
                                        </div>
                                    </div>

                                    <div class="col-lg-4 col-md-6 col-sm-12">
                                        <div class="input-block mb-3">
                                            <label class="form-label">End Date</label>
                                            <input type="date" name="end_date" class="form-control"
                                                value="{{ old('end_date') }}">
                                        </div>
                                    </div>

                                    {{-- <div class="col-lg-4 col-md-6 col-sm-12">
                                        <div class="input-block mb-3">
                                            <label class="form-label">Status</label>
                                            <select name="status" class="form-control" required>
                                                <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                                <option value="in_progress" {{ old('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                                <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                                <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                            </select>
                                        </div>
                                    </div> --}}

                                    <div class="col-lg-4 col-md-6 col-sm-12">
                                    <div class="input-block mb-3">
                                        <label class="form-label">Status</label>
                                        <select name="status" class="form-control" required>
                                            <option value="pending">Pending</option>
                                            <option value="in_progress">In Progress</option>
                                            <option value="completed">Completed</option>
                                            <option value="cancelled">Cancelled</option>
                                        </select>
                                    </div>
                                </div>

                                    <div class="col-lg-4 col-md-6 col-sm-12">
                                        <div class="input-block mb-3">
                                            <label class="form-label">Description</label>
                                            <textarea name="description" class="form-control" placeholder="Project Description" rows="2">{{ old('description') }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Project Items Section -->
            <div class="card mb-0">
                <div class="card-body">
                    <div class="page-header mb-3">
                        <div class="content-page-header mb-3">
                            <h6>Project Items</h6>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="group-item" data-itemnumber="1" id="form-group-item1"
                                style="background:#198754; color:#fff !important; padding: 10px 5px; border-radius: 5px;">
                                <div class="row align-items-end">
                                    <div class="col-md-2">
                                        <label style="color:#fff !important;">Product Name</label>
                                        <select onchange="selectProduct(1)" id="product1"
                                            class="form-control js-example-basic-single" style="height: 30px;">
                                            <option value=""></option>
                                            @foreach ($products as $product)
                                                <option value="{{ $product->id }}"
                                                    data-name="{{ $product->name }}({{ $product->model }})"
                                                    data-stock="{{ $product->inventory->current_stock ?? 0 }}"
                                                    data-price="{{ $product->latestPurchase->unit_price ?? 0 }}"
                                                    data-warranty="{{ $product->warranty ?? 0 }}">
                                                    {{ $product->name }}({{ $product->model }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-1">
                                        <label style="color:#fff !important;">Stock</label>
                                        <input type="number" id="stock1" style="height: 30px;" class="form-control"
                                            readonly>
                                    </div>
                                    <div class="col-md-1">
                                        <label style="color:#fff !important;"> Warranty (Days)</label>
                                        <input type="number" id="warranty1" style="height: 30px;" class="form-control"
                                            readonly>
                                    </div>
                                    <div class="col-md-2">
                                        <label style="color:#fff !important;"> Purchase Price</label>
                                        <input type="number" id="purchase_price1" style="height: 30px;"
                                            class="form-control" readonly>
                                    </div>
                                    <div class="col-md-2">
                                        <label style="color:#fff !important;"> Unit Price</label>
                                        <input onchange="calculateTotal()" type="number" id="unit_price1"
                                            style="height: 30px;" class="form-control unit-price">
                                    </div>
                                    <div class="col-md-1">
                                        <label style="color:#fff !important;">Qty</label>
                                        <input onchange="calculateTotal()" type="number" id="qty1"
                                            style="height: 30px;" class="form-control qty" min="0">
                                    </div>
                                    <div class="col-md-2">
                                        <label style="color:#fff !important;">Total</label>
                                        <input type="number" id="total1" style="height: 30px;"
                                            class="form-control total" readonly>
                                    </div>
                                    <div class="col-md-1 text-end btn-holder">
                                        <button onclick="addItem()" type="button"
                                            class=" btn btn-primary addItemBtn">Add</button>
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <div class="" style="color:#000 !important;">
                                <div class="row align-items-end">
                                    <div class="col-md-4">
                                        <label style="color:#000 !important;">Product Name</label>
                                    </div>
                                    <div class="col-md-2">
                                        <label style="color:#000 !important;"> Unit Price</label>
                                    </div>
                                    <div class="col-md-2">
                                        <label style="color:#000 !important;">Qty</label>
                                    </div>
                                    <div class="col-md-2">
                                        <label style="color:#000 !important;">Total</label>
                                    </div>
                                    <div class="col-md-1 text-end btn-holder">
                                    </div>
                                </div>
                            </div>
                            <div id="item_container"></div>
                            <hr>

                            <br>
                            <div id="summerySection" class="row align-items-end justify-content-end mt-3 d-none"
                                style="color:#000;">
                                <div class="col-md-2">
                                    <label>Sub Total</label>
                                    <input onchange="calculateTotal()" type="number" id="subTotal" name="subTotal"
                                        class="form-control total" style="height: 30px;" readonly>
                                </div>

                                <div class="col-md-2">
                                    <label>Grand Total</label>
                                    <input type="number" id="grandTotal" name="grandTotal" class="form-control total"
                                        style="height: 30px;" readonly>
                                </div>

                                <div class="col-md-1">
                                    <button type="submit" class="btn customer-btn-save create-btn-sm">
                                        Create
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        var itemNumber = 2;
        var selectedClient = null;

        $(document).ready(function() {
            $('.js-example-basic-single').select2({
                placeholder: "Select a product",
                allowClear: true
            });

            // Initialize Bootstrap modal
            var clientModal = new bootstrap.Modal(document.getElementById('clientSelectionModal'));

            // Client type change handler
            $('input[name="client_type"]').change(function() {
                if ($(this).val() === 'new') {
                    $('#newClientForm').show();
                    $('#existingClientForm').hide();

                    // Make new client fields required
                    $('#newClientName').prop('required', true);
                    $('#newClientPhone').prop('required', true);
                    $('#newClientEmail').prop('required', true);
                    $('#newClientAddress').prop('required', true);

                    // Clear existing client selection
                    clearClientSelection();
                } else {
                    // Show modal for client selection
                    clientModal.show();
                }
            });

            // Change client button
            $('#changeClientBtn').click(function() {
                clientModal.show();
            });

            // Client card selection
            $(document).on('click', '.client-card', function() {
                $('.client-card').removeClass('selected');
                $(this).addClass('selected');
                selectedClient = {
                    id: $(this).data('client-id'),
                    name: $(this).data('client-name'),
                    phone: $(this).data('client-phone'),
                    email: $(this).data('client-email'),
                    address: $(this).data('client-address')
                };
                $('#selectClientBtn').prop('disabled', false);
            });

            // Select client button
            $('#selectClientBtn').click(function() {
                if (selectedClient) {
                    populateClientForm(selectedClient);
                    clientModal.hide();
                }
            });

            // Client search functionality
            $('#clientSearch').on('input', function() {
                const searchTerm = $(this).val().toLowerCase();
                $('.client-card').each(function() {
                    const clientName = $(this).data('client-name').toLowerCase();
                    const clientPhone = $(this).data('client-phone').toLowerCase();
                    const clientEmail = $(this).data('client-email').toLowerCase();
                    
                    if (clientName.includes(searchTerm) || 
                        clientPhone.includes(searchTerm) || 
                        clientEmail.includes(searchTerm)) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            });

            // Double click to select client
            $(document).on('dblclick', '.client-card', function() {
                $('.client-card').removeClass('selected');
                $(this).addClass('selected');
                selectedClient = {
                    id: $(this).data('client-id'),
                    name: $(this).data('client-name'),
                    phone: $(this).data('client-phone'),
                    email: $(this).data('client-email'),
                    address: $(this).data('client-address')
                };
                populateClientForm(selectedClient);
                clientModal.hide();
            });
        });

        function populateClientForm(client) {
            $('#newClientForm').hide();
            $('#existingClientForm').show();

            // Populate the display fields
            $('#selectedClientDisplay').val(client.name);
            $('#selectedClientId').val(client.id);
            $('#existingClientPhone').val(client.phone);
            $('#existingClientEmail').val(client.email);
            $('#existingClientAddress').val(client.address);

            // Make new client fields not required
            $('#newClientName').prop('required', false);
            $('#newClientPhone').prop('required', false);
            $('#newClientEmail').prop('required', false);
            $('#newClientAddress').prop('required', false);

            // Also populate the hidden fields for form submission
            $('#newClientName').val(client.name);
            $('#newClientPhone').val(client.phone);
            $('#newClientEmail').val(client.email);
            $('#newClientAddress').val(client.address);
        }

        function clearClientSelection() {
            selectedClient = null;
            $('#selectedClientDisplay').val('');
            $('#selectedClientId').val('');
            $('#existingClientPhone').val('');
            $('#existingClientEmail').val('');
            $('#existingClientAddress').val('');
            $('.client-card').removeClass('selected');
            $('#selectClientBtn').prop('disabled', true);
        }

        // Rest of your existing functions (addItem, removeItem, selectProduct, calculateTotal)
        function addItem() {
            var product = document.getElementById('product1').value;
            var qty = document.getElementById('qty1').value;

            if (product == "") {
                alert("Please select a product");
                return;
            }

            let selectedName = document.getElementById('product1').options[document.getElementById('product1')
                .selectedIndex].text;

            const price = document.getElementById('unit_price1').value;
            if (price.trim() === "") {
                alert("Please enter unit price");
                return;
            }

            if (qty.trim() === "") {
                alert("Please enter quantity");
                return;
            }

            const stock = parseFloat(document.getElementById('stock1').value) || 0;
            if (parseFloat(qty) > stock) {
                alert("Quantity exceeds available stock!");
                return;
            }

            var html = `
                <div class="item${product} group-item mt-2" data-itemnumber="${itemNumber}" id="form-group-item${itemNumber}">
                    <div class="row align-items-end">
                        <div class="col-md-4">
                            <input type="hidden" name="product[]" value="${product}">
                            <p><strong>${selectedName}</strong></p>
                        </div>
                        <div class="col-md-2">
                            <input onchange="calculateTotal()" type="number" name="unit_price[]" id="unit_price${itemNumber}" style="height: 30px;" class="form-control unit-price" value="${price}" >
                        </div>
                        <div class="col-md-2">
                            <input onchange="calculateTotal()" type="number" name="qty[]" id="qty${itemNumber}" style="height: 30px;" class="form-control qty" min="1" value="${qty}">
                        </div>
                        <div class="col-md-2">
                            <input type="number" id="total${itemNumber}" style="height: 30px;" class="form-control total" readonly>
                        </div>
                        <div class="col-md-2 text-end btn-holder">
                            <button onclick="removeItem(${itemNumber})" type="button" class="btn btn-danger remove-item me-1">×</button>
                        </div>
                    </div>
                </div>
            `;

            $('#item_container').append(html);
            itemNumber++;
            calculateTotal();

            // Reset the first row
            document.getElementById('product1').value = '';
            document.getElementById('product1').dispatchEvent(new Event('change'));
            document.getElementById('unit_price1').value = '';
            document.getElementById('qty1').value = '';
            document.getElementById('total1').value = '';
            document.getElementById('stock1').value = '';
            document.getElementById('warranty1').value = '';
            document.getElementById('purchase_price1').value = '';

            $('.js-example-basic-single').trigger('change');
        }

        function removeItem(item) {
            document.getElementById('form-group-item' + item).remove();
            calculateTotal();
        }

        function selectProduct(item) {
            var selected = $('#product' + item + ' option:selected');
            var selectedPrice = selected.data('price') || 0;
            var selectedWarranty = selected.data('warranty') || 0;
            var selectedStock = selected.data('stock') || 0;

            if (document.getElementById('purchase_price' + item))
                document.getElementById('purchase_price' + item).value = selectedPrice;
            if (document.getElementById('warranty' + item))
                document.getElementById('warranty' + item).value = selectedWarranty;
            if (document.getElementById('stock' + item))
                document.getElementById('stock' + item).value = selectedStock;

            // Auto-fill unit price with purchase price
            if (document.getElementById('unit_price' + item)) {
                document.getElementById('unit_price' + item).value = selectedPrice;
            }

            calculateTotal();
        }

        function calculateTotal() {
            let subTotal = 0;

            // Calculate items in the container
            const containerItems = document.querySelectorAll('#item_container .group-item');
            containerItems.forEach(item => {
                const itemNumber = item.dataset.itemnumber;
                const unit_price = parseFloat(document.getElementById('unit_price' + itemNumber)?.value) || 0;
                const qty = parseFloat(document.getElementById('qty' + itemNumber)?.value) || 0;
                const totalEle = document.getElementById('total' + itemNumber);

                const total = qty * unit_price;
                if (totalEle) totalEle.value = total.toFixed(2);
                subTotal += total;
            });

            // Calculate preview item (first row)
            const previewUnitPrice = parseFloat(document.getElementById('unit_price1')?.value) || 0;
            const previewQty = parseFloat(document.getElementById('qty1')?.value) || 0;
            const previewTotal = previewUnitPrice * previewQty;
            const previewTotalInput = document.getElementById('total1');
            if (previewTotalInput) previewTotalInput.value = previewTotal.toFixed(2);

            // Calculate summary
            let discount = parseFloat(document.getElementById('discount')?.value) || 0;
            if (discount > subTotal) discount = subTotal;
            if (document.getElementById('discount')) document.getElementById('discount').value = discount.toFixed(2);

            const grandTotal = subTotal - discount;
            if (document.getElementById('subTotal')) document.getElementById('subTotal').value = subTotal.toFixed(2);
            if (document.getElementById('grandTotal')) document.getElementById('grandTotal').value = grandTotal.toFixed(2);

            const advanced = parseFloat(document.getElementById('advancedPayment')?.value) || 0;
            const due = grandTotal - advanced;
            if (document.getElementById('duePayment')) document.getElementById('duePayment').value = due.toFixed(2);

            // Show/hide summary section
            const hasItems = document.querySelectorAll('#item_container .group-item').length > 0;
            if (document.getElementById("summerySection")) {
                document.getElementById("summerySection").classList.toggle('d-none', !hasItems);
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const advancedPayment = document.getElementById('advancedPayment');
            if (advancedPayment) {
                advancedPayment.addEventListener('input', calculateTotal);
            }
        });
    </script>
@endsection