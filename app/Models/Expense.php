<?php

namespace App\Models;

use App\Enums\ExpenseType;
use Database\Factories\ExpenseFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['budget_month_id', 'category_id', 'paid_by_user_id', 'description', 'amount', 'type', 'due_date'])]
class Expense extends Model
{
    /** @use HasFactory<ExpenseFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'type' => ExpenseType::class,
            'due_date' => 'date',
        ];
    }

    /**
     * Get the budget month this expense belongs to.
     *
     * @return BelongsTo<BudgetMonth, $this>
     */
    public function budgetMonth(): BelongsTo
    {
        return $this->belongsTo(BudgetMonth::class);
    }

    /**
     * Get the category this expense belongs to.
     *
     * @return BelongsTo<Category, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the user who paid for this expense.
     *
     * @return BelongsTo<User, $this>
     */
    public function paidBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'paid_by_user_id');
    }
}
