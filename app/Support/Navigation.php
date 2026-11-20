<?php

namespace App\Support;

use App\Models\SiteSetting;

/**
 * The header menu (and the footer "Explore" links), edited in the admin under
 * Pages → Navigation menu. Until it is saved there, config/navigation.php is used.
 *
 * Item: label, path, children [{label, path, title?, text?, cta?, image?}],
 *       mega {title, text, cta, image} (the dropdown panel's default preview).
 * A child with its own title/text swaps the panel's preview while hovered.
 * Images are online URLs or uploaded paths on the media disk.
 */
class Navigation
{
    public const KEY = 'navigation';

    public const MAX_ITEMS = 8;

    public const MAX_CHILDREN = 10;

    /** Links: a site path ("/safaris?type=family"), an anchor ("#team") or a full http(s) address. */
    public const PATH_PATTERN = '#^(/|\#|https?://)\S*$#i';

    /** @return list<array<string, mixed>> */
    public static function primary(): array
    {
        $saved = SiteSetting::raw(self::KEY)['primary'] ?? null;

        return is_array($saved) && $saved !== [] ? $saved : self::defaults();
    }

    /** @return list<array<string, mixed>> */
    public static function defaults(): array
    {
        return config('navigation.primary', []);
    }

    /**
     * Menu items as admin form state: online photos move to a hidden "image_url" field,
     * because the upload field only holds files stored on the media disk.
     */
    public static function toForm(array $items): array
    {
        $split = function (array $data): array {
            $image = $data['image'] ?? null;
            $isUrl = is_string($image) && str_starts_with($image, 'http');

            return [...$data, 'image' => $isUrl ? null : $image, 'image_url' => $isUrl ? $image : null];
        };

        return array_map(fn (array $item) => [
            'label' => $item['label'] ?? '',
            'path' => $item['path'] ?? '/',
            'mega' => $split($item['mega'] ?? []),
            'children' => array_map($split, $item['children'] ?? []),
        ], $items);
    }

    /** Admin form state back to menu items, without empty fields (they mean "use the default"). */
    public static function fromForm(array $items): array
    {
        $clean = function (array $data, array $fields): array {
            $image = filled($data['image'] ?? null) ? $data['image'] : ($data['image_url'] ?? null);
            $data['image'] = is_array($image) ? (array_values($image)[0] ?? null) : $image;

            $out = [];
            foreach ($fields as $field) {
                $value = is_string($data[$field] ?? null) ? trim($data[$field]) : ($data[$field] ?? null);
                if (filled($value)) {
                    $out[$field] = $value;
                }
            }

            return $out;
        };

        return array_values(array_map(function (array $item) use ($clean) {
            $out = [
                'label' => trim((string) $item['label']),
                'path' => trim((string) $item['path']),
            ];

            $children = array_values(array_map(fn (array $child) => $clean($child, ['label', 'path', 'title', 'text', 'cta', 'image']), $item['children'] ?? []));
            if ($children) {
                $out['children'] = $children;
            }

            $mega = $clean($item['mega'] ?? [], ['title', 'text', 'cta', 'image']);
            if ($mega) {
                $out['mega'] = $mega;
            }

            return $out;
        }, $items));
    }
}
