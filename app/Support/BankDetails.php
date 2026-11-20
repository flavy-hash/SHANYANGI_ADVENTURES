<?php

namespace App\Support;

use App\Models\SiteSetting;

/**
 * Bank accounts printed in the "Payment details" box of quotation PDFs.
 * Edited in the admin under Pages → Site settings → Bank details.
 */
class BankDetails
{
    public const KEY = 'bank';

    public const FIELDS = [
        'bank_name' => 'Bank',
        'account_name' => 'Account name',
        'account_number' => 'Account number',
        'branch' => 'Branch',
        'swift' => 'SWIFT / BIC',
        'currency' => 'Currency',
    ];

    /**
     * Accounts with at least a bank name and account number.
     *
     * @return list<array<string, string>>
     */
    public static function accounts(): array
    {
        $accounts = (array) (SiteSetting::raw(self::KEY)['accounts'] ?? []);

        return array_values(array_filter(
            array_map(fn ($account) => array_map(fn ($value) => trim((string) $value), array_intersect_key((array) $account, self::FIELDS)), $accounts),
            fn (array $account) => ($account['bank_name'] ?? '') !== '' && ($account['account_number'] ?? '') !== '',
        ));
    }

    /** Payment instructions shown under the accounts, e.g. who pays bank charges. */
    public static function note(): ?string
    {
        return trim((string) (SiteSetting::raw(self::KEY)['note'] ?? '')) ?: null;
    }

    public static function isConfigured(): bool
    {
        return self::accounts() !== [];
    }
}
