<?php

namespace App\Support;

use Illuminate\Http\Request;

final class AdminListRequest
{
    /**
     * @param  list<string>  $allowed
     * @return array{0: string, 1: 'asc'|'desc'}
     */
    public static function sort(Request $request, array $allowed, string $defaultColumn, string $defaultDir = 'desc'): array
    {
        $sort = (string) $request->query('sort', $defaultColumn);
        if (! in_array($sort, $allowed, true)) {
            $sort = $defaultColumn;
        }

        $dir = strtolower((string) $request->query('dir', $defaultDir));
        $dir = $dir === 'asc' ? 'asc' : 'desc';

        return [$sort, $dir];
    }

    public static function search(Request $request): ?string
    {
        $q = trim((string) $request->query('q', ''));

        return $q === '' ? null : $q;
    }
}
