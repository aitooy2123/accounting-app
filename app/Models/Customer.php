<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $table = 'customers';
    protected $fillable = [
        'code',
        'name',
        'email',
        'phone',
        'address',
        'tax_id',
        'contact_person',
        'contact_phone',
        'is_active',
        'company_id',
        'branch_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Relationships
     */

    // รายการขาย (Sales)
    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    // รายการซื้อ (Purchases) - เพิ่มส่วนนี้เข้าไป
    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Helper Functions
     */
    // public static function generateCode()
    // {
    //     $last = self::orderBy('id', 'desc')->first();

    //     if ($last && $last->code) {
    //         $num = intval(substr($last->code, -5));
    //         $next = str_pad($num + 1, 5, '0', STR_PAD_LEFT);
    //         return 'CUS-' . $next;
    //     }

    //     return 'CUS-00001';
    // }

      // ==================== SCOPES ====================

    /**
     * Scope a query to only include active customers.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include inactive customers.
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope a query to search customers.
     */
    public function scopeSearch($query, $search)
    {
        if ($search) {
            return $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('tax_id', 'like', "%{$search}%")
                  ->orWhere('contact_person', 'like', "%{$search}%");
            });
        }

        return $query;
    }

    /**
     * Generate customer code.
     */
    public static function generateCode(): string
    {
        $lastCustomer = self::orderBy('id', 'desc')->first();

        if ($lastCustomer && $lastCustomer->code) {
            $lastNumber = (int) substr($lastCustomer->code, 4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return 'CUS-' . str_pad($newNumber, 5, '0', STR_PAD_LEFT);
    }
}
