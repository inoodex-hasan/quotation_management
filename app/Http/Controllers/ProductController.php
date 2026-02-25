<?php

namespace App\Http\Controllers;

use Illuminate\Database\QueryException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB, Schema};
use Illuminate\Validation\Rule;
use App\Models\Product;
use ZipArchive;

class ProductController extends Controller
{
    private function hasProductsTable(): bool
    {
        return Schema::hasTable('products');
    }

    private function missingProductsTableMessage(): string
    {
        return "The 'products' table is missing. Import DB or run migrations.";
    }

    // Show all products
   public function index(Request $request)
{
    if (!$this->hasProductsTable()) {
        $products = new LengthAwarePaginator(
            [],
            0,
            10,
            1,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('frontend.pages.products.index', compact('products'))
            ->with('warning', $this->missingProductsTableMessage());
    }

    // Start query
    $query = Product::query();

    // Apply filters if present
    if ($request->filled('name')) {
        $query->where('name', 'like', '%' . $request->name . '%');
    }

    if ($request->filled('product_code')) {
        $query->where('product_code', 'like', '%' . $request->product_code . '%');
    }

    $allowedSorts = ['name', 'product_code', 'unit', 'price', 'created_at'];
    $sortBy = $request->get('sort_by', 'created_at');
    $sortDir = strtolower((string)$request->get('sort_dir', 'desc')) === 'asc' ? 'asc' : 'desc';

    if (!in_array($sortBy, $allowedSorts, true)) {
        $sortBy = 'created_at';
    }

    $products = $query
        ->orderBy($sortBy, $sortDir)
        ->paginate(15)
        ->withQueryString();

    return view('frontend.pages.products.index', compact('products'));
}

    // Show create form
    public function create()
    {
        if (!$this->hasProductsTable()) {
            return redirect()->route('products.index')
                ->with('warning', $this->missingProductsTableMessage());
        }

        return view('frontend.pages.products.create');
    }

    // Store new product
    public function store(Request $request)
    {
        if (!$this->hasProductsTable()) {
            return redirect()->route('products.index')
                ->with('warning', $this->missingProductsTableMessage());
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'product_code' => 'required|string|max:255|unique:products',
            'details' => 'nullable|string',
            'unit' => 'required|string|max:50',
            'price' => 'required|numeric|min:0',
        ]);

        try {
            Product::create($request->all());
        } catch (QueryException $e) {
            return back()->withInput()->with('warning', 'Unable to save product right now.');
        }

        return redirect()->route('products.index')
            ->with('success', 'Product created successfully.');
    }

    // Show single product
    public function show($id)
    {
        if (!$this->hasProductsTable()) {
            return redirect()->route('products.index')
                ->with('warning', $this->missingProductsTableMessage());
        }

        $product = Product::find($id);
        if (!$product) {
            return redirect()->route('products.index')->with('warning', 'Product not found.');
        }

        return view('frontend.pages.products.show', compact('product'));
    }

    // Show edit form
    public function edit($id)
    {
        if (!$this->hasProductsTable()) {
            return redirect()->route('products.index')
                ->with('warning', $this->missingProductsTableMessage());
        }

        $product = Product::find($id);
        if (!$product) {
            return redirect()->route('products.index')->with('warning', 'Product not found.');
        }

        return view('frontend.pages.products.edit', compact('product'));
    }

    // Update product
    public function update(Request $request, $id)
    {
        if (!$this->hasProductsTable()) {
            return redirect()->route('products.index')
                ->with('warning', $this->missingProductsTableMessage());
        }

        $product = Product::find($id);
        if (!$product) {
            return redirect()->route('products.index')->with('warning', 'Product not found.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'product_code' => ['required', 'string', 'max:255', Rule::unique('products', 'product_code')->ignore($product->id)],
            'details' => 'nullable|string',
            'unit' => 'required|string|max:50',
            'price' => 'required|numeric|min:0',
        ]);

        try {
            $product->update($request->all());
        } catch (QueryException $e) {
            return back()->withInput()->with('warning', 'Unable to update product right now.');
        }

        return redirect()->route('products.index')
            ->with('success', 'Product updated successfully.');
    }

    // Delete product
    public function destroy($id)
    {
        if (!$this->hasProductsTable()) {
            return redirect()->route('products.index')
                ->with('warning', $this->missingProductsTableMessage());
        }

        $product = Product::find($id);
        if (!$product) {
            return redirect()->route('products.index')->with('warning', 'Product not found.');
        }

        try {
            $product->delete();
        } catch (QueryException $e) {
            return redirect()->route('products.index')->with('warning', 'Unable to delete product right now.');
        }

        return redirect()->route('products.index')
            ->with('success', 'Product deleted successfully.');
    }

    public function import(Request $request)
    {
        if (!$this->hasProductsTable()) {
            return redirect()->route('products.index')
                ->with('warning', $this->missingProductsTableMessage());
        }

        $request->validate([
            'import_file' => 'required|file|mimes:csv,txt,xlsx|max:10240',
        ]);

        $file = $request->file('import_file');
        $ext = strtolower((string)$file->getClientOriginalExtension());
        $path = $file->getRealPath();

        try {
            $rows = $ext === 'xlsx'
                ? $this->parseXlsxRows($path)
                : $this->parseCsvRows($path);
        } catch (\Throwable $e) {
            return redirect()->route('products.index')
                ->with('warning', 'Unable to read import file. Please check file format.');
        }

        if (count($rows) === 0) {
            return redirect()->route('products.index')
                ->with('warning', 'No data rows found in import file.');
        }

        $created = 0;
        $updated = 0;
        $skipped = 0;
        $errors = [];

        DB::beginTransaction();
        try {
            foreach ($rows as $line => $row) {
                $name = trim((string)($row['name'] ?? ''));
                $code = trim((string)($row['product_code'] ?? ''));
                $unit = trim((string)($row['unit'] ?? ''));
                $details = isset($row['details']) ? trim((string)$row['details']) : null;
                $priceRaw = trim((string)($row['price'] ?? ''));
                $price = is_numeric($priceRaw) ? (float)$priceRaw : null;

                if ($name === '' && $code === '' && $unit === '' && ($priceRaw === '' || $priceRaw === '0')) {
                    $skipped++;
                    continue;
                }

                if ($name === '' || $code === '' || $unit === '' || $price === null || $price < 0) {
                    $errors[] = "Row {$line}: invalid required data.";
                    continue;
                }

                $payload = [
                    'name' => $name,
                    'details' => $details ?: null,
                    'unit' => $unit,
                    'price' => $price,
                ];

                $existing = Product::where('product_code', $code)->first();
                if ($existing) {
                    $existing->update($payload);
                    $updated++;
                } else {
                    $payload['product_code'] = $code;
                    Product::create($payload);
                    $created++;
                }
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->route('products.index')
                ->with('warning', 'Import failed while saving data.');
        }

        $message = "Import completed. Created: {$created}, Updated: {$updated}, Skipped: {$skipped}.";
        if (!empty($errors)) {
            $message .= ' Some rows had errors.';
        }

        return redirect()->route('products.index')
            ->with('success', $message)
            ->with('import_errors', array_slice($errors, 0, 20));
    }

    private function parseCsvRows(string $path): array
    {
        $handle = fopen($path, 'r');
        if (!$handle) {
            throw new \RuntimeException('Cannot open CSV');
        }

        $header = null;
        $rows = [];
        $line = 1;
        while (($data = fgetcsv($handle)) !== false) {
            if ($header === null) {
                $header = array_map(fn($h) => $this->normalizeHeader((string)$h), $data);
                $line++;
                continue;
            }

            if (count(array_filter($data, fn($v) => trim((string)$v) !== '')) === 0) {
                $line++;
                continue;
            }

            $row = [];
            foreach ($header as $idx => $key) {
                if ($key === '') {
                    continue;
                }
                $row[$key] = $data[$idx] ?? null;
            }
            $rows[$line] = $row;
            $line++;
        }
        fclose($handle);

        return $rows;
    }

    private function parseXlsxRows(string $path): array
    {
        $zip = new ZipArchive();
        if ($zip->open($path) !== true) {
            throw new \RuntimeException('Cannot open XLSX');
        }

        $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');
        $sharedXml = $zip->getFromName('xl/sharedStrings.xml');
        $zip->close();

        if ($sheetXml === false) {
            throw new \RuntimeException('Sheet not found');
        }

        $shared = [];
        if ($sharedXml !== false) {
            $sx = simplexml_load_string($sharedXml);
            if ($sx && isset($sx->si)) {
                foreach ($sx->si as $si) {
                    $shared[] = (string)($si->t ?? '');
                }
            }
        }

        $sheet = simplexml_load_string($sheetXml);
        if (!$sheet || !isset($sheet->sheetData->row)) {
            return [];
        }

        $headerByIndex = [];
        $rows = [];
        $line = 0;
        foreach ($sheet->sheetData->row as $rowNode) {
            $line++;
            $cells = [];
            foreach ($rowNode->c as $c) {
                $ref = (string)$c['r'];
                $idx = $this->columnIndexFromRef($ref);
                $type = (string)$c['t'];
                $value = '';
                if ($type === 's') {
                    $si = (int)($c->v ?? -1);
                    $value = $shared[$si] ?? '';
                } elseif ($type === 'inlineStr') {
                    $value = (string)($c->is->t ?? '');
                } else {
                    $value = (string)($c->v ?? '');
                }
                $cells[$idx] = $value;
            }

            if ($line === 1) {
                foreach ($cells as $idx => $value) {
                    $headerByIndex[$idx] = $this->normalizeHeader($value);
                }
                continue;
            }

            if (count(array_filter($cells, fn($v) => trim((string)$v) !== '')) === 0) {
                continue;
            }

            $mapped = [];
            foreach ($headerByIndex as $idx => $key) {
                if ($key === '') {
                    continue;
                }
                $mapped[$key] = $cells[$idx] ?? null;
            }
            $rows[$line] = $mapped;
        }

        return $rows;
    }

    private function normalizeHeader(string $header): string
    {
        $key = strtolower(trim($header));
        $key = str_replace([' ', '-'], '_', $key);
        return $key;
    }

    private function columnIndexFromRef(string $ref): int
    {
        preg_match('/^[A-Z]+/i', $ref, $m);
        $letters = strtoupper($m[0] ?? 'A');
        $index = 0;
        foreach (str_split($letters) as $ch) {
            $index = ($index * 26) + (ord($ch) - 64);
        }
        return max(1, $index);
    }
}
