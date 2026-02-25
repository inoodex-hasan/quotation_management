<?php

namespace App\Http\Controllers;

use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB, Schema};
use App\Models\{Client, CompanyDetail, Product, Quotation, QuotationItem, User};
use Barryvdh\DomPDF\Facade\Pdf;

class QuotationController extends Controller
{
    public function index(Request $request)
    {
        $quotations = Quotation::with('client')
            ->when($request->filled('quotation_number'), function ($query) use ($request) {
                $query->where('quotation_number', 'like', '%' . trim((string) $request->quotation_number) . '%');
            })
            ->when($request->filled('client_name'), function ($query) use ($request) {
                $clientName = trim((string) $request->client_name);
                $query->whereHas('client', function ($clientQuery) use ($clientName) {
                    $clientQuery->where('name', 'like', '%' . $clientName . '%');
                });
            })
            ->when($request->filled('date_from'), function ($query) use ($request) {
                $query->whereDate('quotation_date', '>=', $request->date_from);
            })
            ->when($request->filled('date_to'), function ($query) use ($request) {
                $query->whereDate('quotation_date', '<=', $request->date_to);
            })
            ->latest()
            ->get();

        return view('frontend.pages.quotations.index', compact('quotations'));
    }

    public function create()
    {
        $clients = Schema::hasTable('clients')
            ? Client::query()->latest()->get()
            : collect();
        $defaultCompany = Schema::hasTable('company_details')
            ? CompanyDetail::query()->where('is_default', true)->first()
            : null;
        $userColumns = ['id', 'name', 'images'];
        if (Schema::hasColumn('users', 'photo')) {
            $userColumns[] = 'photo';
        }
        $signatories = Schema::hasTable('users')
            ? User::query()->orderBy('name')->get($userColumns)
            : collect();
        $products = Schema::hasTable('products')
            ? Product::query()->get()
            : collect();

        return view('frontend.pages.quotations.create', compact('clients', 'products', 'signatories', 'defaultCompany'));
    }

    public function store(Request $request)
{
    // Backward compatibility for legacy discount keys
    if (!$request->has('discount_amount')) {
        $fallbackDiscount = $request->input('overall_discount', $request->input('overall_disocunt'));
        if ($fallbackDiscount !== null) {
            $request->merge(['discount_amount' => $fallbackDiscount]);
        }
    }

    // Get default company and logo
    $defaultCompany = CompanyDetail::where('is_default', true)->first();
    $companyLogo = $defaultCompany?->photo;

    // Validation rules
    $rules = [
        'client_mode' => 'required|in:existing,new',
        'client_id' => 'nullable|integer',
        'client_name' => 'nullable|string|max:255',
        'client_designation' => 'nullable|string|max:255',
        'client_address' => 'nullable|string',
        'client_phone' => 'nullable|string|max:20',
        'client_email' => 'nullable|email|max:255',
        'attention_to' => 'nullable|string|max:255',
        'body_content' => 'nullable|string',
        'terms_conditions' => 'required|string',
        'subject' => 'nullable|string|max:255',
        'company_name' => 'required|string|max:255',
        'signatory_user_id' => 'required|integer|exists:users,id',
        'company_phone' => 'nullable|string|max:20',
        'company_email' => 'nullable|email|max:255',
        'company_website' => 'nullable|string|max:255',
        'company_address' => 'nullable|string',
        'additional_enclosed' => 'nullable|string',
        'discount_amount' => 'nullable|numeric|min:0',
        'vat_percent' => 'nullable|numeric|min:0',
        'tax_percent' => 'nullable|numeric|min:0',
        'installation_charge' => 'nullable|numeric|min:0',
        'round_off' => 'nullable|numeric|min:0',
        'items' => 'required|array|min:1',
        'items.*.product_id' => 'required|exists:products,id',
        'items.*.quantity' => 'required|integer|min:1',
        'items.*.unit_price' => 'required|numeric|min:0',
        'items.*.discount_amount' => 'nullable|numeric|min:0',
        'items.*.description' => 'nullable|string',
    ];

    if (Schema::hasTable('clients')) {
        $rules['client_id'] = 'nullable|integer|exists:clients,id';
    }

    if (($request->input('client_mode') ?? 'new') === 'existing') {
        $rules['client_id'] = 'required|integer|exists:clients,id';
    } else {
        $rules['client_name'] = 'required|string|max:255';
        $rules['client_address'] = 'required|string';
    }

    $validated = $request->validate($rules);

    try {
        DB::transaction(function () use ($request, $companyLogo) {
            // Prepare client data
            $clientId = null;
            $clientName = $request->client_name ?? '';
            $clientAddress = $request->client_address ?? '';
            $clientPhone = $request->client_phone ?? '';
            $clientEmail = $request->client_email ?? '';

            // Signatory info
            $signatoryUser = User::findOrFail($request->signatory_user_id);
            $signatoryName = $signatoryUser->name;
            $signatoryDesignation = optional($signatoryUser->roles()->orderBy('name')->first())->name ?? '';
            $signatoryPhoto = null;
            if (!empty($signatoryUser->photo)) {
                $signatoryPhoto = $signatoryUser->photo;
            } elseif (!empty($signatoryUser->images)) {
                $signatoryPhoto = 'frontend/users/' . $signatoryUser->images;
            }

            // Handle client selection
            if (($request->input('client_mode') ?? 'new') === 'existing') {
                $client = Client::findOrFail($request->client_id);
                $clientId = $client->id;
                $clientName = $client->name;
                $clientAddress = $client->address;
                $clientPhone = $client->phone ?? '';
                $clientEmail = $client->email ?? '';
            } else {
                $client = Client::create([
                    'name' => $clientName,
                    'phone' => $clientPhone ?: '-',
                    'email' => $clientEmail ?: ('no-email-' . now()->timestamp . '@local.test'),
                    'address' => $clientAddress,
                ]);
                $clientId = $client->id;
            }

            // Calculate totals
            $subTotal = 0;
            foreach ($request->items as $item) {
                $lineTotal = $item['quantity'] * $item['unit_price'];
                $lineDiscount = min($lineTotal, (float)($item['discount_amount'] ?? 0));
                $subTotal += ($lineTotal - $lineDiscount);
            }

            $discountAmount = min($subTotal, (float)($request->discount_amount ?? 0));
            $vatPercent = (float)($request->vat_percent ?? 0);
            $taxPercent = (float)($request->tax_percent ?? 0);
            $installationCharge = (float)($request->installation_charge ?? 0);
            $roundOff = (float)($request->round_off ?? 0);

            $totalAfterDiscount = max(0, $subTotal - $discountAmount);
            $vatAmount = $totalAfterDiscount * ($vatPercent / 100);
            $taxAmount = $totalAfterDiscount * ($taxPercent / 100);
            $totalAmount = $totalAfterDiscount + $vatAmount + $taxAmount + $installationCharge - $roundOff;

            // Create quotation with snapshot fields
            $quotation = Quotation::create([
                'client_id' => $clientId,
                'quotation_date' => now(),
                'expiry_date' => now()->addDays(15),
                'notes' => $request->subject,
                'sub_total' => $subTotal,
                'discount_amount' => $discountAmount,
                'vat_percent' => $vatPercent,
                'vat_amount' => $vatAmount,
                'tax_percent' => $taxPercent,
                'tax_amount' => $taxAmount,
                'installation_charge' => $installationCharge,
                'round_off' => $roundOff,
                'total_amount' => $totalAmount,
                'status' => 'draft',

                // PDF snapshot fields
                'client_name' => $clientName,
                'client_designation' => $request->client_designation,
                'client_address' => $clientAddress,
                'client_phone' => $clientPhone,
                'client_email' => $clientEmail,
                'attention_to' => $request->attention_to,
                'body_content' => $request->body_content,
                'terms_conditions' => $request->terms_conditions,
                'subject' => $request->subject,
                'company_name' => $request->company_name,
                'company_phone' => $request->company_phone,
                'company_email' => $request->company_email,
                'company_website' => $request->company_website,
                'company_address' => $request->company_address,
                'logo' => $companyLogo,
                'signatory_name' => $signatoryName,
                'signatory_designation' => $signatoryDesignation,
                'signatory_photo' => $signatoryPhoto,
                'additional_enclosed' => $request->additional_enclosed,
            ]);

            // Create quotation items
            foreach ($request->items as $item) {
                $lineTotal = $item['quantity'] * $item['unit_price'];
                $lineDiscount = min($lineTotal, (float)($item['discount_amount'] ?? 0));
                $netTotal = $lineTotal - $lineDiscount;

                QuotationItem::create([
                    'quotation_id' => $quotation->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total' => $netTotal,
                    'description' => $item['description'] ?? null,
                ]);
            }
        });

    } catch (QueryException $e) {
        return back()->withInput()->with('warning', 'Unable to save quotation right now. Please check client setup and try again.');
    } catch (\Throwable $e) {
        return back()->withInput()->with('warning', 'Unable to save quotation right now. Please check client information and try again.');
    }

    return redirect()->route('quotations.index')->with('success', 'Quotation created successfully.');
}

    public function show(Quotation $quotation)
    {
        $attention_to = $quotation->attention_to;
        $quotation->load(['client', 'items.product']);
        return view('frontend.pages.quotations.show', compact('quotation', 'attention_to'));
    }

    public function edit(Quotation $quotation)
    {
        $clients = collect();
        $products = Schema::hasTable('products')
            ? Product::query()->get()
            : collect();
        $quotation->load('items');

        return view('frontend.pages.quotations.edit', compact('quotation', 'clients', 'products'));
    }

    public function update(Request $request, Quotation $quotation)
    {
        if (!$request->has('discount_amount')) {
            $fallbackDiscount = $request->input('overall_discount', $request->input('overall_disocunt'));
            if ($fallbackDiscount !== null) {
                $request->merge(['discount_amount' => $fallbackDiscount]);
            }
        }

        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'quotation_date' => 'required|date',
            'expiry_date' => 'required|date|after:quotation_date',
            'notes' => 'nullable|string',
            'discount_amount' => 'nullable|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request, $quotation) {
            // Delete existing items
            $quotation->items()->delete();

            $subTotal = 0;

            // Calculate new subtotal
            foreach ($request->items as $item) {
                $subTotal += $item['quantity'] * $item['unit_price'];
            }

            $discountAmount = $request->discount_amount ?? 0;
            $totalAmount = $subTotal - $discountAmount;

            // Update quotation
            $quotation->update([
                'client_id' => $request->client_id,
                'quotation_date' => $request->quotation_date,
                'expiry_date' => $request->expiry_date,
                'notes' => $request->notes,
                'sub_total' => $subTotal,
                'discount_amount' => $discountAmount,
                'total_amount' => $totalAmount,
            ]);

            // Create new items
            foreach ($request->items as $item) {
                QuotationItem::create([
                    'quotation_id' => $quotation->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total' => $item['quantity'] * $item['unit_price'],
                ]);
            }
        });

        return redirect()->route('quotations.index')->with('success', 'Quotation updated successfully.');
    }

    public function destroy(Quotation $quotation)
    {
        $quotation->delete();
        return redirect()->route('quotations.index')->with('success', 'Quotation deleted successfully.');
    }

    public function download(Quotation $quotation)
    {
        $quotation->load(['client', 'items.product']);
        $amount_in_words = $this->convertNumberToWords($quotation->total_amount) . ' Taka Only';
        $pdf = PDF::loadView('pdf.quotations', compact('quotation'));
        return $pdf->download('quotation-' . $quotation->quotation_number . '.pdf');

    }

    public function generatePDF(Quotation $quotation)
{
    $quotation->load(['items.product', 'client']);

    $amount_in_words = $this->convertNumberToWords($quotation->total_amount) . ' Taka Only';

    $defaultCompany = CompanyDetail::where('is_default', true)->first();
    $companyLogo = $this->resolvePublicFilePath($quotation->logo ?: ($defaultCompany->photo ?? null));

    $signatoryPhotoRaw = $quotation->signatory_photo;
    if (empty($signatoryPhotoRaw) && !empty($quotation->signatory_user_id)) {
        $signatoryUser = User::find($quotation->signatory_user_id);
        if ($signatoryUser) {
            if (!empty($signatoryUser->photo)) {
                $signatoryPhotoRaw = $signatoryUser->photo;
            } elseif (!empty($signatoryUser->images)) {
                $signatoryPhotoRaw = 'frontend/users/' . $signatoryUser->images;
            }
        }
    }
    $signatoryPhoto = $this->resolvePublicFilePath($signatoryPhotoRaw);

    $data = [
        'quotation' => $quotation,
        'amount_in_words' => $amount_in_words,

        // Client snapshot
        'client_name' => $quotation->client_name,
        'client_designation' => $quotation->client_designation,
        'client_address' => $quotation->client_address,
        'client_phone' => $quotation->client_phone,
        'client_email' => $quotation->client_email,

        // PDF content
        'attention_to' => $quotation->attention_to,
        'body_content' => $quotation->body_content,
        'terms_conditions' => $quotation->terms_conditions,
        'subject' => $quotation->subject,

        // Company snapshot
        'company_name' => $quotation->company_name,
        'company_phone' => $quotation->company_phone,
        'company_email' => $quotation->company_email,
        'company_website' => $quotation->company_website,
        'company_address' => $quotation->company_address,
        'company_logo' => $companyLogo,  // company logo

        // Signatory snapshot
        'signatory_name' => $quotation->signatory_name,
        'signatory_designation' => $quotation->signatory_designation,
        'signatory_photo' => $signatoryPhoto,

        'additional_enclosed' => $quotation->additional_enclosed,
    ];

    $pdf = Pdf::loadView('pdf.quotations', $data);

    return $pdf->download('quotation-' . $quotation->quotation_number . '.pdf');
}

    private function resolvePublicFilePath(?string $path): ?string
    {
        if (empty($path)) {
            return null;
        }

        $path = trim($path);
        if (preg_match('/^https?:\/\//i', $path)) {
            $parsedPath = parse_url($path, PHP_URL_PATH);
            if (!empty($parsedPath)) {
                $path = $parsedPath;
            }
        }

        $path = str_replace('\\', '/', $path);
        $isAbsolute = preg_match('/^[A-Za-z]:\//', $path) === 1 || str_starts_with($path, '/');
        $fullPath = $isAbsolute ? $path : public_path(ltrim($path, '/'));

        return file_exists($fullPath) ? $fullPath : null;
    }

    // public function generatePDF(Quotation $quotation)
    // {
    //     $quotation->load(['items.product']);

    //     $pdfData = session('quotation_pdf_data_' . $quotation->id, []);
    //     $defaultCompany = Schema::hasTable('company_details')
    //         ? CompanyDetail::query()->where('is_default', true)->first()
    //         : null;
    //     $signatoryDesignation = $pdfData['signatory_designation'] ?? '';
    //     if ($signatoryDesignation === '' && !empty($pdfData['signatory_user_id'])) {
    //         $signatoryDesignation = (string) optional(
    //             User::query()->find($pdfData['signatory_user_id'])?->roles()->orderBy('name')->first()
    //         )->name;
    //     }

    //     $amount_in_words = $this->convertNumberToWords($quotation->total_amount) . ' Taka Only';

    //     $data = [
    //         'quotation' => $quotation,
    //         'amount_in_words' => $amount_in_words,
    //         'client_name' => $pdfData['client_name'] ?? '',
    //         'client_designation' => $pdfData['client_designation'] ?? '',
    //         'client_address' => $pdfData['client_address'] ?? '',
    //         'client_phone' => $pdfData['client_phone'] ?? '',
    //         'client_email' => $pdfData['client_email'] ?? '',
    //         'attention_to' => $pdfData['attention_to'] ?? '',
    //         'body_content' => $pdfData['body_content'] ?? '',
    //         'terms_conditions' => $pdfData['terms_conditions'] ?? '',
    //         'subject' => $pdfData['subject'] ?? '',
    //         'company_name' => $pdfData['company_name'] ?? ($defaultCompany->name ?? ''),
    //         'signatory_name' => $pdfData['signatory_name'] ?? '',
    //         'signatory_designation' => $signatoryDesignation,
    //         'signatory_photo' => $pdfData['signatory_photo'] ?? '',
    //         'company_phone' => $pdfData['company_phone'] ?? ($defaultCompany->phone ?? ''),
    //         'company_email' => $pdfData['company_email'] ?? ($defaultCompany->email ?? ''),
    //         'company_website' => $pdfData['company_website'] ?? ($defaultCompany->website ?? ''),
    //         'company_address' => $pdfData['company_address'] ?? ($defaultCompany->address ?? ''),
    //         'company_photo' => $defaultCompany->photo ?? '',
    //         'additional_enclosed' => $pdfData['additional_enclosed'] ?? '',
    //     ];

    //     $pdf = Pdf::loadView('pdf.quotations', $data);
    //     return $pdf->download('quotation-' . $quotation->quotation_number . '.pdf');
    // }

    public function sendQuotation(Quotation $quotation)
    {
        $quotation->update(['status' => 'sent']);
        return redirect()->back()->with('success', 'Quotation sent successfully.');
    }

    public function updateStatus(Request $request, Quotation $quotation)
    {
        $request->validate([
            'status' => 'required|in:sent,accepted,rejected,expired'
        ]);

        $quotation->update(['status' => $request->status]);
        return redirect()->back()->with('success', 'Quotation status updated successfully.');
    }

    public function getProduct($id)
    {
        $product = Product::findOrFail($id);
        return response()->json([
            'product' => $product,
            'unit_price' => $product->price ?? 0
        ]);
    }

    private function convertNumberToWords($number)
    {
        if ($number == 0) {
            return 'Zero';
        }

        $ones = array(
            0 => '',
            1 => 'One',
            2 => 'Two',
            3 => 'Three',
            4 => 'Four',
            5 => 'Five',
            6 => 'Six',
            7 => 'Seven',
            8 => 'Eight',
            9 => 'Nine',
            10 => 'Ten',
            11 => 'Eleven',
            12 => 'Twelve',
            13 => 'Thirteen',
            14 => 'Fourteen',
            15 => 'Fifteen',
            16 => 'Sixteen',
            17 => 'Seventeen',
            18 => 'Eighteen',
            19 => 'Nineteen'
        );

        $tens = array(
            2 => 'Twenty',
            3 => 'Thirty',
            4 => 'Forty',
            5 => 'Fifty',
            6 => 'Sixty',
            7 => 'Seventy',
            8 => 'Eighty',
            9 => 'Ninety'
        );

        $words = '';

        // Handle crores
        if ($number >= 10000000) {
            $crores = floor($number / 10000000);
            $words .= $this->convertNumberToWords($crores) . ' Crore ';
            $number %= 10000000; // ADDED THIS LINE
        } // ADDED MISSING CLOSING BRACE HERE

        // Handle lakhs
        if ($number >= 100000) {
            $lakhs = floor($number / 100000);
            $words .= $this->convertNumberToWords($lakhs) . ' Lakh ';
            $number %= 100000;
        }

        // Handle thousands
        if ($number >= 1000) {
            $thousands = floor($number / 1000);
            $words .= $this->convertNumberToWords($thousands) . ' Thousand ';
            $number %= 1000;
        }

        // Handle hundreds
        if ($number >= 100) {
            $hundreds = floor($number / 100);
            $words .= $this->convertNumberToWords($hundreds) . ' Hundred ';
            $number %= 100;
        }

        // Handle tens and ones
        if ($number > 0) {
            if ($number < 20) {
                $words .= $ones[$number];
            } else {
                $words .= $tens[floor($number / 10)];
                if ($number % 10 > 0) {
                    $words .= ' ' . $ones[$number % 10];
                }
            }
        }

        return trim($words);
    }

}
