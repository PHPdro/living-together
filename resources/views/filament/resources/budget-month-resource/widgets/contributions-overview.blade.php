@use('Illuminate\Support\Number')

<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            Divisão dos gastos — {{ $record?->label() }}
        </x-slot>

        <x-slot name="description">
            Cada pessoa contribui proporcionalmente à sua renda no mês. O saldo mostra quem já pagou a mais (a receber) ou a menos (a pagar) para equilibrar as contas.
        </x-slot>

        @php $contributions = $this->getContributions(); @endphp

        @if ($contributions->isEmpty())
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Cadastre pessoas para ver a divisão dos gastos aqui.
            </p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 text-left text-xs font-medium uppercase tracking-wide text-gray-500 dark:border-white/10 dark:text-gray-400">
                            <th class="py-2 pe-3">Pessoa</th>
                            <th class="py-2 px-3">Renda no mês</th>
                            <th class="py-2 px-3">% da renda do casal</th>
                            <th class="py-2 px-3">Deve contribuir</th>
                            <th class="py-2 px-3">Pagou</th>
                            <th class="py-2 ps-3">Saldo</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                        @foreach ($contributions as $contribution)
                            <tr>
                                <td class="py-2 pe-3 font-medium text-gray-950 dark:text-white">
                                    {{ $contribution['user']->name }}
                                </td>
                                <td class="py-2 px-3">{{ Number::currency($contribution['income'], 'BRL') }}</td>
                                <td class="py-2 px-3">{{ Number::percentage($contribution['income_share'] * 100, maxPrecision: 1) }}</td>
                                <td class="py-2 px-3">{{ Number::currency($contribution['due'], 'BRL') }}</td>
                                <td class="py-2 px-3">{{ Number::currency($contribution['paid'], 'BRL') }}</td>
                                <td class="py-2 ps-3">
                                    @if ($contribution['balance'] > 0)
                                        <x-filament::badge color="success">
                                            A receber {{ Number::currency($contribution['balance'], 'BRL') }}
                                        </x-filament::badge>
                                    @elseif ($contribution['balance'] < 0)
                                        <x-filament::badge color="danger">
                                            A pagar {{ Number::currency(abs($contribution['balance']), 'BRL') }}
                                        </x-filament::badge>
                                    @else
                                        <x-filament::badge color="gray">
                                            Em dia
                                        </x-filament::badge>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($record?->totalIncome() <= 0)
                <p class="mt-3 text-sm text-warning-600 dark:text-warning-400">
                    Nenhum ganho registrado neste mês ainda — cadastre os ganhos na aba "Ganhos" para calcular a contribuição proporcional.
                </p>
            @endif
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
