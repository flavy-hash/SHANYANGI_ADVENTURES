<?php

namespace App\Support;

use App\Models\SiteSetting;

/**
 * Official social media accounts. Edited in the admin (Site settings);
 * falls back to the SITE_* values in config/site.php. Only networks with a
 * URL are returned.
 */
class SocialLinks
{
    public const KEY = 'social';

    /**
     * label + 24x24 stroke icon for each supported network, in display order.
     */
    public const NETWORKS = [
        'instagram' => ['label' => 'Instagram', 'icon' => '<rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"/>'],
        'facebook' => ['label' => 'Facebook', 'icon' => '<path d="M14 8h3V4h-3a4 4 0 0 0-4 4v3H7v4h3v6h4v-6h3l1-4h-4V8Z"/>'],
        'linkedin' => ['label' => 'LinkedIn', 'icon' => '<rect x="3" y="3" width="18" height="18" rx="3"/><path d="M8 10.5V16M8 7.75v.01M11.5 16v-5.5M11.5 13a2.5 2.5 0 0 1 5 0v3"/>'],
        'tiktok' => ['label' => 'TikTok', 'icon' => '<path d="M14 3v11.5a3.5 3.5 0 1 1-3.5-3.5"/><path d="M14 3c.4 2.6 2.3 4.5 5 4.8"/>'],
        'youtube' => ['label' => 'YouTube', 'icon' => '<rect x="2.5" y="5" width="19" height="14" rx="4"/><path d="m10 9 5 3-5 3V9Z" fill="currentColor"/>'],
    ];

    /**
     * Saved URLs; until the admin saves Site settings, the .env defaults.
     * Once saved, an empty field hides that network even if .env has a value.
     *
     * @return array<string, ?string>
     */
    public static function urls(): array
    {
        return SiteSetting::raw(self::KEY) ?? array_filter(config('site.social', []));
    }

    /**
     * @return array<string, array{url: string, label: string, icon: string}>
     */
    public static function active(): array
    {
        $urls = self::urls();

        $links = [];
        foreach (self::NETWORKS as $key => $network) {
            if (! empty($urls[$key])) {
                $links[$key] = ['url' => $urls[$key]] + $network;
            }
        }

        return $links;
    }
}
