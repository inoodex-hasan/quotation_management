<?php

namespace App\Models;

use Illuminate\Database\Eloquent\{Model, SoftDeletes};
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};


class Quotation extends Model
{

    use SoftDeletes;
    protected $fillable = [
        'quotation_number',
        'client_id',
        'quotation_date',
        'expiry_date',
        'notes',
        'sub_total',
        'discount_amount',
        'vat_percent',
        'vat_amount',
        'tax_percent',
        'tax_amount',
        'installation_charge',
        'round_off',
        'total_amount',
        'status',

        // PDF snapshot fields
        'client_name',
        'client_designation',
        'client_address',
        'client_phone',
        'client_email',
        'attention_to',
        'body_content',
        'terms_conditions',
        'subject',
        'company_name',
        'company_phone',
        'company_email',
        'company_website',
        'company_address',
        'logo',
        'signatory_name',
        'signatory_designation',
        'signatory_photo',
        'additional_enclosed',
    ];

    protected $casts = [
        'quotation_date' => 'date',
        'expiry_date' => 'date',
        'sub_total' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'vat_percent' => 'decimal:2',
        'vat_amount' => 'decimal:2',
        'tax_percent' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'installation_charge' => 'decimal:2',
        'round_off' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(QuotationItem::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($quotation) {
            $quotation->quotation_number = static::generateQuotationNumber();
        });
    }

    public static function generateQuotationNumber()
{
    $prefix = 'PK';
    $date = date('Ymd'); // 20251126
    
    // Search last quotation for today
    $lastQuotation = static::where('quotation_number', 'like', "{$prefix}-{$date}-%")
    ->withTrashed() // include deleted quotations
    ->orderBy('id', 'desc')
    ->first();

    // Extract last 4 digits sequence
    $sequence = $lastQuotation 
        ? (int)substr($lastQuotation->quotation_number, -4) + 1 
        : 1;

    // Format: QT-20251126-0001
    return "{$prefix}-{$date}-" . str_pad($sequence, 4, '0', STR_PAD_LEFT);
}

}
