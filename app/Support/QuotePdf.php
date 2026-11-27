<?php

namespace App\Support;

use App\Models\Package;
use App\Models\Quote;
use Barryvdh\DomPDF\Facade\Pdf;

/**
 * Renders quotations as PDF and builds price lines from a package.
 */
class QuotePdf
{
    public static function output(Quote $quote): string
    {
        $quote->loadMissing('package');

        return Pdf::loadView('pdf.quote', ['quote' => $quote])
            ->setPaper('a4')
            ->setOption('isFontSubsettingEnabled', true) // embed only the characters used
            ->output();
    }

    /**
     * Itemised lines for a package: adults at the package price, children at
     * config('quotes.child_rate_percent') of it. Empty when the package has no price.
     *
     * @return list<array{description: string, quantity: int, unit_price: float}>
     */
    public static function packageItems(?Package $package, int $adults, int $children): array
    {
        if (! $package || ! $package->price) {
            return [];
        }

        // Group prices depend on everyone travelling; children pay the child price when set.
        $perPerson = $package->pricePerPerson(max(1, $adults) + $children);

        $items = [[
            'description' => $package->title.' ('.($package->duration_label ?? 'package').'), per adult',
            'quantity' => max(1, $adults),
            'unit_price' => (float) $perPerson,
        ]];

        if ($children > 0) {
            $items[] = [
                'description' => $package->title.', per child',
                'quantity' => $children,
                'unit_price' => $package->child_price
                    ? (float) $package->child_price
                    : round($perPerson * config('quotes.child_rate_percent') / 100, 2),
            ];
        }

        return $items;
    }
}
