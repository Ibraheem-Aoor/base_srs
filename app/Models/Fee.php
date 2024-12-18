<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fee extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'student_enroll_id',
        'category_id',
        'fee_amount',
        'fine_amount',
        'discount_amount',
        'paid_amount',
        'assign_date',
        'due_date',
        'pay_date',
        'payment_method',
        'note',
        'prove_file_path',
        'status',
        'created_by',
        'updated_by',
    ];

    public function studentEnroll()
    {
        return $this->belongsTo(StudentEnroll::class, 'student_enroll_id');
    }

    public function category()
    {
        return $this->belongsTo(FeesCategory::class, 'category_id');
    }


    /**
     * Simple Binded function to get the prove_file_path preview link
     */
    public function getProveFilePreviewLink()
    {
        $path = 'uploads/student_receipts/' . $this->studentEnroll->student_id . '/' . $this->prove_file_path;
        return asset($path);
    }



    /**
     * Quick Discount Insert
     */
    public function insertDiscount($discount)
    {
        if (isset($discount)) {
            if ($discount->type == '1') {
                $this->discount_amount = $discount->amount;
            } else {
                $this->discount_amount = (($this->fee_amount / 100) * $discount->amount);
            }
            $this->save();
        }
        return $this;
    }


    /**
     * Quick Fine Insert
     */
    public function insertFineAmount()
    {
        $fine_amount = 0;
        if (empty($this->pay_date) || $this->due_date < $this->pay_date) {

            $due_date = strtotime($this->due_date);
            $today = strtotime(date('Y-m-d'));
            $days = (int) (($today - $due_date) / 86400);

            if ($this->due_date < date("Y-m-d")) {
                if (isset($this->category)) {
                    foreach ($this->category->fines->where('status', '1') as $fine) {
                        if ($fine->start_day <= $days && $fine->end_day >= $days) {
                            if ($fine->type == '1') {
                                $fine_amount = $fine_amount + $fine->amount;
                            } else {
                                $fine_amount = $fine_amount + (($this->fee_amount / 100) * $fine->amount);
                            }
                        }
                    }
                }
            }
        }
        $this->fine_amount = $fine_amount;
        $this->save();
        return $this;
    }
}
