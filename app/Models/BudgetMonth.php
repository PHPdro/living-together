<?php

namespace App\Models;

use Carbon\Carbon;
use Database\Factories\BudgetMonthFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

#[Fillable(['year', 'month', 'notes'])]
class BudgetMonth extends Model
{
    /** @use HasFactory<BudgetMonthFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'year' => 'integer',
            'month' => 'integer',
        ];
    }

    /**
     * Get the current calendar month's budget, falling back to the most
     * recently registered one when the current month has not been created yet.
     */
    public static function current(): ?self
    {
        return static::query()
            ->where('year', now()->year)
            ->where('month', now()->month)
            ->first()
            ?? static::query()->orderByDesc('year')->orderByDesc('month')->first();
    }

    /**
     * Get the expenses recorded for this month.
     *
     * @return HasMany<Expense, $this>
     */
    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    /**
     * Get the income entries recorded for this month.
     *
     * @return HasMany<Income, $this>
     */
    public function incomes(): HasMany
    {
        return $this->hasMany(Income::class);
    }

    /**
     * Get a human-readable label for this month, e.g. "Setembro/2026".
     */
    public function label(): string
    {
        return ucfirst(Carbon::createFromDate($this->year, $this->month, 1)->translatedFormat('F/Y'));
    }

    /**
     * Get the total amount of expenses recorded for this month.
     */
    public function totalExpenses(): float
    {
        return (float) $this->expenses()->sum('amount');
    }

    /**
     * Get the total amount of income recorded for this month.
     */
    public function totalIncome(): float
    {
        return (float) $this->incomes()->sum('amount');
    }

    /**
     * Calculate each partner's proportional contribution for this month.
     *
     * Each user's share of the couple's total income determines the share of
     * total expenses they are expected to contribute. The "balance" compares
     * that expected contribution against what they actually paid, so the
     * couple can see who owes whom to settle up.
     *
     * @return Collection<int, array{user: User, income: float, income_share: float, due: float, paid: float, balance: float}>
     */
    public function contributions(): Collection
    {
        $totalIncome = $this->totalIncome();
        $totalExpenses = $this->totalExpenses();

        $incomeByUser = $this->incomes()
            ->selectRaw('user_id, sum(amount) as total')
            ->groupBy('user_id')
            ->pluck('total', 'user_id');

        $paidByUser = $this->expenses()
            ->whereNotNull('paid_by_user_id')
            ->selectRaw('paid_by_user_id, sum(amount) as total')
            ->groupBy('paid_by_user_id')
            ->pluck('total', 'paid_by_user_id');

        return User::all()->map(function (User $user) use ($incomeByUser, $paidByUser, $totalIncome, $totalExpenses) {
            $income = (float) ($incomeByUser[$user->id] ?? 0);
            $paid = (float) ($paidByUser[$user->id] ?? 0);
            $incomeShare = $totalIncome > 0 ? $income / $totalIncome : 0.0;
            $due = round($incomeShare * $totalExpenses, 2);

            return [
                'user' => $user,
                'income' => $income,
                'income_share' => $incomeShare,
                'due' => $due,
                'paid' => $paid,
                'balance' => round($paid - $due, 2),
            ];
        });
    }
}
