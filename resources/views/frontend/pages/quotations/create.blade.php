@extends('frontend.layouts.app')

@section('title', 'Create Quotation')

@section('content')
    <style>
        @media (min-width: 992px) {
            #items-table {
                table-layout: fixed;
                width: 100%;
            }

            #items-table .col-sl {
                width: 6%;
            }

            #items-table .col-product {
                width: 28%;
            }

            #items-table .col-unit-price {
                width: 12%;
            }

            #items-table .col-payable {
                width: 12%;
            }

            #items-table .col-qty {
                width: 8%;
            }

            #items-table .col-item-discount {
                width: 12%;
            }

            #items-table .col-total {
                width: 12%;
            }

            #items-table .col-action {
                width: 10%;
            }
        }

        @media (max-width: 991.98px) {
            #items-table {
                min-width: 980px;
            }
        }
    </style>

    @php
        $defaultTerms =
            " Delivery timeline will be confirmed after order confirmation.\n Prices are in BDT.\n VAT/TAX are not included unless mentioned.\n Payment terms: As per mutual agreement.";
        $hasClients = isset($clients) && $clients->count() > 0;
        $oldClientMode = $hasClients ? old('client_mode', 'existing') : 'new';
    @endphp

    <div class="container-fluid mt-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
            <h3 class="fw-bold mb-0">Create New Quotation</h3>
            <a href="{{ route('quotations.index') }}" class="btn btn-secondary rounded-pill px-4">Back</a>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('warning'))
            <div class="alert alert-warning">{{ session('warning') }}</div>
        @endif

        <form id="quotationForm" action="{{ route('quotations.store') }}" method="POST">
            @csrf

            <div class="card shadow-sm border-0 rounded-3 mb-3">
                <div class="card-body">
                    <h5 class="mb-3">Client Information</h5>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label d-block">Client Type</label>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="client_mode" id="client-mode-existing"
                                    value="existing" {{ $oldClientMode === 'existing' ? 'checked' : '' }}
                                    {{ !$hasClients ? 'disabled' : '' }}>
                                <label class="form-check-label" for="client-mode-existing">Select Existing</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="client_mode" id="client-mode-new"
                                    value="new" {{ $oldClientMode === 'new' ? 'checked' : '' }}>
                                <label class="form-check-label" for="client-mode-new">Create New</label>
                            </div>
                            @if (!$hasClients)
                                <div class="small text-muted mt-1">No client found. Please create new client information.
                                </div>
                            @endif
                        </div>
                        <div class="col-md-6" id="existing-client-wrap" style="display:none;">
                            <label class="form-label">Existing Client *</label>
                            <select name="client_id" id="existing-client-select" class="form-select">
                                <option value="">Choose existing client...</option>
                                @foreach ($clients as $client)
                                    <option value="{{ $client->id }}" data-name="{{ $client->name }}"
                                        data-phone="{{ $client->phone }}" data-email="{{ $client->email }}"
                                        data-address="{{ $client->address }}"
                                        {{ (string) old('client_id') === (string) $client->id ? 'selected' : '' }}>
                                        {{ $client->name }} - {{ $client->phone }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Client/Company Name *</label>
                            <input type="text" id="client-name-input" name="client_name" class="form-control"
                                value="{{ old('client_name') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Attention To</label>
                            <input type="text" name="attention_to" class="form-control"
                                value="{{ old('attention_to') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Designation</label>
                            <input type="text" name="client_designation" class="form-control"
                                value="{{ old('client_designation') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone</label>
                            <input type="text" id="client-phone-input" name="client_phone" class="form-control"
                                value="{{ old('client_phone') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" id="client-email-input" name="client_email" class="form-control"
                                value="{{ old('client_email') }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Client Address *</label>
                            <textarea id="client-address-input" name="client_address" class="form-control" rows="3">{{ old('client_address') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 rounded-3 mb-3">
                <div class="card-body">
                    <h5 class="mb-3">Quotation Content</h5>
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Subject</label>
                            <input type="text" name="subject" class="form-control" value="{{ old('subject') }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Body Content</label>
                            <textarea name="body_content" class="form-control" rows="8">{{ old('body_content') }}</textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Terms & Conditions *</label>
                            <textarea name="terms_conditions" class="form-control" rows="6" required>{{ old('terms_conditions', $defaultTerms) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 rounded-3 mb-3">
                <div class="card-body">
                    <h5 class="mb-3">Company & Signatory</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Company Name *</label>
                            <input type="text" name="company_name" class="form-control"
                                value="{{ old('company_name', $defaultCompany->name ?? '') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Company Phone</label>
                            <input type="text" name="company_phone" class="form-control"
                                value="{{ old('company_phone', $defaultCompany->phone ?? '') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Company Email</label>
                            <input type="email" name="company_email" class="form-control"
                                value="{{ old('company_email', $defaultCompany->email ?? '') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Company Website</label>
                            <input type="text" name="company_website" class="form-control"
                                value="{{ old('company_website', $defaultCompany->website ?? '') }}">
                        </div>
                        <input type="hidden" name="company_address"
                            value="{{ old('company_address', $defaultCompany->address ?? '') }}">
                        <div class="col-md-6">
                            <label class="form-label">Select Signatory *</label>
                            <select name="signatory_user_id" id="signatory-user-select" class="form-select" required>
                                <option value="">
                                    {{ $signatories->count() ? 'Choose signatory...' : 'No user found. Please create user first.' }}
                                </option>
                                @foreach ($signatories as $signatory)
                                    @php
                                        $signatoryPhotoPath =
                                            $signatory->photo ??
                                            (!empty($signatory->images) ? 'frontend/users/' . $signatory->images : '');
                                    @endphp
                                    <option value="{{ $signatory->id }}" data-name="{{ $signatory->name }}"
                                        data-photo="{{ $signatoryPhotoPath ? asset($signatoryPhotoPath) : '' }}"
                                        {{ (string) old('signatory_user_id') === (string) $signatory->id ? 'selected' : '' }}>
                                        {{ $signatory->name }}
                                    </option>
                                @endforeach
                            </select>
                            <input type="hidden" name="signatory_name" id="signatory-name-input"
                                value="{{ old('signatory_name') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Digital Signature</label>
                            <div class="border rounded p-2 text-center bg-light">
                                <img id="signatory-photo-preview" src="" alt="Signature Preview"
                                    style="max-height: 80px; display:none; object-fit: contain;">
                                <div id="signatory-photo-empty" class="text-muted small">No signature photo selected.
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Additional Enclosed</label>
                            <textarea name="additional_enclosed" class="form-control" rows="2">{{ old('additional_enclosed') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 rounded-3 mb-3">
                <div class="card-body">
                    <h5 class="mb-3">Products</h5>

                    <div class="row g-3 align-items-end mb-3">
                        <div class="col-md-5">
                            <label class="form-label">Select Product</label>
                            <select id="product-select" class="form-select">
                                <option value="">Choose a product...</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}" data-name="{{ $product->name }}"
                                        data-description="{{ $product->details ?? 'description not available' }}"
                                        data-price="{{ $product->price ?? 0 }}">
                                        {{ \Illuminate\Support\Str::limit($product->name, 30) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Unit Price</label>
                            <input type="number" id="product-db-price" class="form-control" value="0"
                                step="0.01" min="0" readonly>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Payable Price</label>
                            <input type="number" id="product-payable-price" class="form-control" value="0"
                                step="0.01" min="0">
                        </div>
                        <div class="col-md-1">
                            <label class="form-label">Quantity</label>
                            <input type="number" id="product-qty" class="form-control" value="1" min="1">
                        </div>
                        <div class="col-md-2">
                            <button type="button" id="add-product" class="btn btn-primary w-100">Add</button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle mb-0" id="items-table">
                            <colgroup>
                                <col class="col-sl">
                                <col class="col-product">
                                <col class="col-unit-price">
                                <col class="col-payable">
                                <col class="col-qty">
                                <col class="col-item-discount">
                                <col class="col-total">
                                <col class="col-action">
                            </colgroup>
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Product</th>
                                    <th class="text-end">Unit Price</th>
                                    <th class="text-end">Payable Price</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-end">Item Discount</th>
                                    <th class="text-end">Total</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody id="items-tbody">
                                <tr id="empty-row">
                                    <td colspan="8" class="text-center text-muted">No products added.</td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="6" class="text-end"><strong>Sub Total</strong></td>
                                    <td class="text-end"><strong id="sub-total">0.00</strong></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="6" class="text-end"><strong>Overall Discount</strong></td>
                                    <td class="text-end">
                                        <input type="number" name="discount_amount" id="discount-amount"
                                            class="form-control form-control-sm text-end"
                                            value="{{ old('discount_amount', 0) }}" min="0" step="0.01">
                                    </td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="6" class="text-end"><strong>VAT (%)</strong></td>
                                    <td class="text-end">
                                        <input type="number" name="vat_percent" id="vat-percent"
                                            class="form-control form-control-sm text-end"
                                            value="{{ old('vat_percent', 0) }}" min="0" step="0.01">
                                    </td>
                                    <td></td>
                                </tr>
                                {{-- <tr>
                                    <td colspan="6" class="text-end"><strong>Tax (%)</strong></td>
                                    <td class="text-end">
                                        <input type="number" name="tax_percent" id="tax-percent"
                                            class="form-control form-control-sm text-end"
                                            value="{{ old('tax_percent', 0) }}" min="0" step="0.01">
                                    </td>
                                    <td></td>
                                </tr> --}}
                                <tr>
                                    <td colspan="6" class="text-end"><strong>Installation Charge</strong></td>
                                    <td class="text-end">
                                        <input type="number" name="installation_charge" id="installation-charge"
                                            class="form-control form-control-sm text-end"
                                            value="{{ old('installation_charge', 0) }}" min="0" step="0.01">
                                    </td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="6" class="text-end"><strong>Round Off (-)</strong></td>
                                    <td class="text-end">
                                        <input type="number" name="round_off" id="round-off"
                                            class="form-control form-control-sm text-end"
                                            value="{{ old('round_off', 0) }}" min="0" step="0.01">
                                    </td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="6" class="text-end"><strong>Total Amount</strong></td>
                                    <td class="text-end"><strong id="grand-total">0.00</strong></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end mb-4">
                <button type="submit" class="btn btn-success rounded-pill px-5">Save Quotation</button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        (function() {
            let rowIndex = 0;

            const productSelect = document.getElementById('product-select');
            const productDbPrice = document.getElementById('product-db-price');
            const productPayablePrice = document.getElementById('product-payable-price');
            const productQty = document.getElementById('product-qty');
            const addBtn = document.getElementById('add-product');
            const tbody = document.getElementById('items-tbody');
            const subTotalEl = document.getElementById('sub-total');
            const grandTotalEl = document.getElementById('grand-total');
            const discountInput = document.getElementById('discount-amount');
            const vatInput = document.getElementById('vat-percent');
            const taxInput = document.getElementById('tax-percent');
            const installationInput = document.getElementById('installation-charge');
            const roundOffInput = document.getElementById('round-off');
            const form = document.getElementById('quotationForm');
            const clientModeExisting = document.getElementById('client-mode-existing');
            const clientModeNew = document.getElementById('client-mode-new');
            const existingClientWrap = document.getElementById('existing-client-wrap');
            const existingClientSelect = document.getElementById('existing-client-select');
            const clientNameInput = document.getElementById('client-name-input');
            const clientPhoneInput = document.getElementById('client-phone-input');
            const clientEmailInput = document.getElementById('client-email-input');
            const clientAddressInput = document.getElementById('client-address-input');
            const signatorySelect = document.getElementById('signatory-user-select');
            const signatoryNameInput = document.getElementById('signatory-name-input');
            const signatoryPhotoPreview = document.getElementById('signatory-photo-preview');
            const signatoryPhotoEmpty = document.getElementById('signatory-photo-empty');

            function useClientFromSelect() {
                if (!existingClientSelect || !existingClientSelect.value) {
                    return;
                }

                const selected = existingClientSelect.options[existingClientSelect.selectedIndex];
                clientNameInput.value = selected?.dataset?.name || '';
                clientPhoneInput.value = selected?.dataset?.phone || '';
                clientEmailInput.value = selected?.dataset?.email || '';
                clientAddressInput.value = selected?.dataset?.address || '';
            }

            function toggleClientMode() {
                const isExisting = !!(clientModeExisting && clientModeExisting.checked && !clientModeExisting.disabled);

                if (existingClientWrap) {
                    existingClientWrap.style.display = isExisting ? 'block' : 'none';
                }
                if (existingClientSelect) {
                    existingClientSelect.required = isExisting;
                }

                clientNameInput.readOnly = isExisting;
                clientPhoneInput.readOnly = isExisting;
                clientEmailInput.readOnly = isExisting;
                clientAddressInput.readOnly = isExisting;
                clientNameInput.required = !isExisting;
                clientAddressInput.required = !isExisting;

                if (isExisting) {
                    useClientFromSelect();
                }
            }

            function syncSignatoryData() {
                if (!signatorySelect) {
                    return;
                }

                const selected = signatorySelect.options[signatorySelect.selectedIndex];
                const signatoryName = selected?.dataset?.name || '';
                const signatoryPhoto = selected?.dataset?.photo || '';

                if (signatoryNameInput) {
                    signatoryNameInput.value = signatoryName;
                }

                if (signatoryPhoto && signatoryPhotoPreview) {
                    signatoryPhotoPreview.src = signatoryPhoto;
                    signatoryPhotoPreview.style.display = 'inline-block';
                    if (signatoryPhotoEmpty) {
                        signatoryPhotoEmpty.style.display = 'none';
                    }
                } else if (signatoryPhotoPreview) {
                    signatoryPhotoPreview.src = '';
                    signatoryPhotoPreview.style.display = 'none';
                    if (signatoryPhotoEmpty) {
                        signatoryPhotoEmpty.style.display = 'block';
                    }
                }
            }

            function recalcTotals() {
                let subtotal = 0;
                tbody.querySelectorAll('tr.item-row').forEach((row) => {
                    const qty = parseFloat(row.querySelector('.item-qty').value) || 0;
                    const payable = parseFloat(row.querySelector('.item-payable').value) || 0;
                    const lineTotal = qty * payable;
                    const lineDiscountRaw = parseFloat(row.querySelector('.item-discount').value) || 0;
                    const lineDiscount = Math.min(lineTotal, Math.max(0, lineDiscountRaw));
                    const total = lineTotal - lineDiscount;

                    row.querySelector('.item-total').textContent = total.toFixed(2);
                    subtotal += total;
                });

                const overallDiscountRaw = parseFloat(discountInput.value) || 0;
                const overallDiscount = Math.min(subtotal, Math.max(0, overallDiscountRaw));
                const vatPercent = Math.max(0, parseFloat(vatInput.value) || 0);
                const taxPercent = Math.max(0, parseFloat(taxInput?.value || 0) || 0);
                const installationCharge = Math.max(0, parseFloat(installationInput.value) || 0);
                const roundOff = Math.max(0, parseFloat(roundOffInput.value) || 0);
                const base = Math.max(0, subtotal - overallDiscount);
                const vat = base * (vatPercent / 100);
                const tax = base * (taxPercent / 100);
                const grand = base + vat + tax + installationCharge - roundOff;

                subTotalEl.textContent = subtotal.toFixed(2);
                grandTotalEl.textContent = grand.toFixed(2);
            }

            function renumber() {
                const rows = tbody.querySelectorAll('tr.item-row');
                rows.forEach((row, i) => {
                    row.querySelector('.item-sl').textContent = i + 1;
                });

                const empty = document.getElementById('empty-row');
                if (rows.length === 0) {
                    if (!empty) {
                        const tr = document.createElement('tr');
                        tr.id = 'empty-row';
                        tr.innerHTML = '<td colspan="8" class="text-center text-muted">No products added.</td>';
                        tbody.appendChild(tr);
                    }
                } else if (empty) {
                    empty.remove();
                }
            }

            productSelect.addEventListener('change', function() {
                const selected = this.options[this.selectedIndex];
                const dbPrice = selected?.dataset?.price || 0;
                productDbPrice.value = dbPrice;
                productPayablePrice.value = dbPrice;
            });

            addBtn.addEventListener('click', function() {
                const selected = productSelect.options[productSelect.selectedIndex];
                if (!selected.value) {
                    alert('Please select a product.');
                    return;
                }

                const qty = Math.max(1, parseFloat(productQty.value) || 1);
                const dbPrice = Math.max(0, parseFloat(productDbPrice.value) || 0);
                const payable = Math.max(0, parseFloat(productPayablePrice.value) || 0);
                const desc = selected.dataset.description || '';
                const fullName = selected.dataset.name || '';
                const shortName = fullName.length > 15 ? `${fullName.substring(0, 25)}...` : fullName;

                const tr = document.createElement('tr');
                tr.className = 'item-row';
                tr.innerHTML = `
                    <td class="item-sl">1</td>
                    <td>
                        <div class="fw-semibold">${shortName}</div>
                        <small class="text-muted">${desc}</small>
                        <input type="hidden" name="items[${rowIndex}][product_id]" value="${selected.value}">
                        <input type="hidden" name="items[${rowIndex}][description]" value="${desc.replace(/"/g, '&quot;')}">
                    </td>
                    <td>
                        <input type="number" class="form-control form-control-sm text-end item-db-price"
                            value="${dbPrice.toFixed(2)}" min="0" step="0.01" readonly>
                    </td>
                    <td>
                        <input type="number" class="form-control form-control-sm text-end item-payable"
                            name="items[${rowIndex}][unit_price]" value="${payable.toFixed(2)}" min="0" step="0.01" required>
                    </td>
                    <td>
                        <input type="number" class="form-control form-control-sm text-center item-qty"
                            name="items[${rowIndex}][quantity]" value="${qty}" min="1" required>
                    </td>
                    <td>
                        <input type="number" class="form-control form-control-sm text-end item-discount"
                            name="items[${rowIndex}][discount_amount]" value="0.00" min="0" step="0.01">
                    </td>
                    <td class="text-end fw-semibold item-total">${(qty * payable).toFixed(2)}</td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-danger remove-item px-2 fw-bold">X</button>
                    </td>
                `;

                tbody.appendChild(tr);
                rowIndex++;

                tr.querySelector('.item-qty').addEventListener('input', recalcTotals);
                tr.querySelector('.item-payable').addEventListener('input', recalcTotals);
                tr.querySelector('.item-discount').addEventListener('input', recalcTotals);
                tr.querySelector('.remove-item').addEventListener('click', function() {
                    tr.remove();
                    renumber();
                    recalcTotals();
                });

                renumber();
                recalcTotals();

                productSelect.value = '';
                productQty.value = 1;
                productDbPrice.value = 0;
                productPayablePrice.value = 0;
            });

            discountInput.addEventListener('input', recalcTotals);
            vatInput.addEventListener('input', recalcTotals);
            if (taxInput) {
                taxInput.addEventListener('input', recalcTotals);
            }
            installationInput.addEventListener('input', recalcTotals);
            roundOffInput.addEventListener('input', recalcTotals);
            if (clientModeExisting) {
                clientModeExisting.addEventListener('change', toggleClientMode);
            }
            if (clientModeNew) {
                clientModeNew.addEventListener('change', toggleClientMode);
            }
            if (existingClientSelect) {
                existingClientSelect.addEventListener('change', useClientFromSelect);
            }
            if (signatorySelect) {
                signatorySelect.addEventListener('change', syncSignatoryData);
            }

            form.addEventListener('submit', function(e) {
                if (tbody.querySelectorAll('tr.item-row').length === 0) {
                    e.preventDefault();
                    alert('Please add at least one product item.');
                }
            });

            toggleClientMode();
            syncSignatoryData();
            recalcTotals();
        })();
    </script>
@endpush
