<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\EmploymentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class Employment extends Model
{
    /** @use HasFactory<\Database\Factories\EmploymentFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'client_id',
        'position',
        'hire_date',
        'status',
        'salary',
        'work_location',
        'effective_date',
        'end_date',
    ];

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<Client, $this>
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    protected function casts(): array
    {
        return [
            'hire_date' => 'date',
            'salary' => 'decimal:2',
            'effective_date' => 'date',
            'end_date' => 'date',
            'status' => EmploymentStatus::class,
        ];
    }
}
