<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JournalEntry extends Model
{
    use HasFactory;

    protected $table = 'journal_entries';
    protected $fillable = [
        'entry_date',
        'reference_type',
        'reference_id',
        'description',
    ];

    protected $casts = [
        'entry_date' => 'date',
    ];

    public static function rules(): array
    {
        return [
            'entry_date' => 'required|date',

            'lines' => 'required|array|min:2',

            'lines.*.account_id' => 'required|exists:accounts,id',
            'lines.*.debit' => 'nullable|numeric|min:0',
            'lines.*.credit' => 'nullable|numeric|min:0',
        ];
    }

    public function lines()
    {
        return $this->hasMany(JournalEntryLine::class);
    }
}
