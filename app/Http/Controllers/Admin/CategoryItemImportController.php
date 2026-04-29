<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CategoryDetail;
use App\Models\CategoryItem;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CategoryItemImportController extends Controller
{
    private const TYPE_INSPECTION_MONTHLY_SAFETY = 1;
    private const TYPE_INSPECTION_PPE = 3;

    public function inspection(Request $request)
    {
        /** @disregard P1013 */
        abort_if(!auth()->user()->can('category_item_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $teamId = $this->currentTeamId();
        $team = $teamId > 0 ? Team::find($teamId) : null;

        return Inertia::render('CategoryItem/ImportInspection', [
            'team' => $team,
            'typeInspectionId' => self::TYPE_INSPECTION_MONTHLY_SAFETY,
            'preview' => session('import_preview'),
            'result' => session('import_result'),
            'warnings' => session('import_warnings') ?? [],
        ]);
    }

    public function ppe(Request $request)
    {
        /** @disregard P1013 */
        abort_if(!auth()->user()->can('category_item_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $teamId = $this->currentTeamId();
        $team = $teamId > 0 ? Team::find($teamId) : null;

        return Inertia::render('EquipmentItem/ImportPpe', [
            'team' => $team,
            'typeInspectionId' => self::TYPE_INSPECTION_PPE,
            'preview' => session('import_preview'),
            'result' => session('import_result'),
            'warnings' => session('import_warnings') ?? [],
        ]);
    }

    public function inspectionTemplate(): StreamedResponse
    {
        /** @disregard P1013 */
        abort_if(!auth()->user()->can('category_item_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $teamId = $this->currentTeamId();
        $teamName = Team::find($teamId)?->name ?? 'site';
        $safeTeamName = $this->slugFilename($teamName);

        // "sep=," helps Excel choose the correct delimiter even in locales that default to ";".
        $rows = [
            ['sep=,'],
            ['item_name', 'detail_name'],
            ['Burma Bridge', 'Pole 1'],
            ['Burma Bridge', 'Pole 2'],
            ['Burma Bridge', 'Platform'],
        ];

        return $this->streamCsv("inspection-elements-template-{$safeTeamName}.csv", $rows);
    }

    public function ppeTemplate(): StreamedResponse
    {
        /** @disregard P1013 */
        abort_if(!auth()->user()->can('category_item_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $teamId = $this->currentTeamId();
        $teamName = Team::find($teamId)?->name ?? 'site';
        $safeTeamName = $this->slugFilename($teamName);

        $rows = [
            ['sep=,'],
            ['item_name', 'detail_name'],
            ['Harnesses', 'H-001'],
            ['Harnesses', 'H-002'],
            ['Carabiners', 'C-001'],
        ];

        return $this->streamCsv("ppe-template-{$safeTeamName}.csv", $rows);
    }

    public function inspectionExport(): StreamedResponse
    {
        /** @disregard P1013 */
        abort_if(!auth()->user()->can('category_item_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $teamId = $this->currentTeamId();
        $teamName = Team::find($teamId)?->name ?? 'site';
        $safeTeamName = $this->slugFilename($teamName);

        $items = CategoryItem::query()
            ->where('site_id', $teamId)
            ->where('type_inspection_id', self::TYPE_INSPECTION_MONTHLY_SAFETY)
            ->with(['identifications' => function ($q) {
                $q->orderBy('name');
            }])
            ->orderBy('name')
            ->get();

        $rows = [
            ['sep=,'],
            ['item_id', 'item_name', 'detail_id', 'detail_name', 'delete'],
        ];

        foreach ($items as $item) {
            $details = $item->identifications;
            if ($details->isEmpty()) {
                $rows[] = [
                    (int) $item->id,
                    (string) $item->name,
                    '',
                    '',
                    '',
                ];
                continue;
            }

            foreach ($details as $detail) {
                $rows[] = [
                    (int) $item->id,
                    (string) $item->name,
                    (int) $detail->id,
                    (string) $detail->name,
                    '',
                ];
            }
        }

        return $this->streamCsv("inspection-elements-export-{$safeTeamName}.csv", $rows);
    }

    public function ppeExport(): StreamedResponse
    {
        /** @disregard P1013 */
        abort_if(!auth()->user()->can('category_item_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $teamId = $this->currentTeamId();
        $teamName = Team::find($teamId)?->name ?? 'site';
        $safeTeamName = $this->slugFilename($teamName);

        $items = CategoryItem::query()
            ->where('site_id', $teamId)
            ->where('type_inspection_id', self::TYPE_INSPECTION_PPE)
            ->with(['identifications' => function ($q) {
                $q->orderBy('name');
            }])
            ->orderBy('name')
            ->get();

        $rows = [
            ['sep=,'],
            ['item_id', 'item_name', 'detail_id', 'detail_name', 'delete'],
        ];

        foreach ($items as $item) {
            $details = $item->identifications;
            if ($details->isEmpty()) {
                $rows[] = [
                    (int) $item->id,
                    (string) $item->name,
                    '',
                    '',
                    '',
                ];
                continue;
            }

            foreach ($details as $detail) {
                $rows[] = [
                    (int) $item->id,
                    (string) $item->name,
                    (int) $detail->id,
                    (string) $detail->name,
                    '',
                ];
            }
        }

        return $this->streamCsv("ppe-export-{$safeTeamName}.csv", $rows);
    }

    public function inspectionPreview(Request $request)
    {
        /** @disregard P1013 */
        abort_if(!auth()->user()->can('category_item_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $teamId = $this->currentTeamId();
        if ($teamId <= 0) {
            return back()->withErrors(['file' => 'No active site selected.']);
        }

        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt'],
        ]);

        $file = $request->file('file');
        if (!$file instanceof UploadedFile) {
            return back()->withErrors(['file' => 'Invalid file upload.']);
        }

        $parsed = $this->parseItemDetailCsv($file);
        if (!empty($parsed['errors'])) {
            return back()->withErrors($parsed['errors']);
        }

        $warnings = $this->buildPreviewWarnings(
            siteId: $teamId,
            typeInspectionId: self::TYPE_INSPECTION_MONTHLY_SAFETY,
            items: $parsed['items']
        );

        $preview = [
            'site_id' => $teamId,
            'type_inspection_id' => self::TYPE_INSPECTION_MONTHLY_SAFETY,
            'total_rows' => (int) $parsed['total_rows'],
            'items_count' => count($parsed['items']),
            'items' => $parsed['items'],
        ];

        return redirect()
            ->route('category-items.import.inspection')
            ->with('import_preview', $preview)
            ->with('import_warnings', $warnings);
    }

    public function ppePreview(Request $request)
    {
        /** @disregard P1013 */
        abort_if(!auth()->user()->can('category_item_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $teamId = $this->currentTeamId();
        if ($teamId <= 0) {
            return back()->withErrors(['file' => 'No active site selected.']);
        }

        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt'],
        ]);

        $file = $request->file('file');
        if (!$file instanceof UploadedFile) {
            return back()->withErrors(['file' => 'Invalid file upload.']);
        }

        $parsed = $this->parseItemDetailCsv($file);
        if (!empty($parsed['errors'])) {
            return back()->withErrors($parsed['errors']);
        }

        $warnings = $this->buildPreviewWarnings(
            siteId: $teamId,
            typeInspectionId: self::TYPE_INSPECTION_PPE,
            items: $parsed['items']
        );

        $preview = [
            'site_id' => $teamId,
            'type_inspection_id' => self::TYPE_INSPECTION_PPE,
            'total_rows' => (int) $parsed['total_rows'],
            'items_count' => count($parsed['items']),
            'items' => $parsed['items'],
        ];

        return redirect()
            ->route('equipment-items.import.ppe')
            ->with('import_preview', $preview)
            ->with('import_warnings', $warnings);
    }

    public function inspectionApply(Request $request)
    {
        /** @disregard P1013 */
        abort_if(!auth()->user()->can('category_item_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $teamId = $this->currentTeamId();
        if ($teamId <= 0) {
            return back()->withErrors(['file' => 'No active site selected.']);
        }

        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt'],
            'confirm' => ['required', 'accepted'],
        ]);

        $file = $request->file('file');
        if (!$file instanceof UploadedFile) {
            return back()->withErrors(['file' => 'Invalid file upload.']);
        }

        $parsed = $this->parseItemDetailCsv($file);
        if (!empty($parsed['errors'])) {
            return back()->withErrors($parsed['errors']);
        }

        $result = DB::transaction(function () use ($teamId, $parsed) {
            $createdItems = 0;
            $restoredItems = 0;
            $createdDetails = 0;
            $restoredDetails = 0;

            foreach ($parsed['items'] as $itemRow) {
                $itemName = (string) ($itemRow['item_name'] ?? '');
                $detailNames = $itemRow['details'] ?? [];

                $item = $this->findOrCreateCategoryItem(
                    siteId: $teamId,
                    typeInspectionId: self::TYPE_INSPECTION_MONTHLY_SAFETY,
                    itemName: $itemName,
                    createdCount: $createdItems,
                    restoredCount: $restoredItems
                );

                $detailMap = $this->existingDetailsMapForItem($item->id);

                foreach ($detailNames as $detailName) {
                    $key = $this->normalizeKey($detailName);
                    if ($key === '') {
                        continue;
                    }

                    if (isset($detailMap[$key])) {
                        $detail = $detailMap[$key];
                        if ($detail->deleted_at !== null) {
                            $detail->deleted_at = null;
                            $detail->save();
                            $restoredDetails++;
                        }
                        continue;
                    }

                    CategoryDetail::create([
                        'category_item_id' => $item->id,
                        'name' => $detailName,
                        'in_use_date' => now()->toDateTimeString(),
                        'out_use_date' => null,
                    ]);
                    $createdDetails++;
                }
            }

            return [
                'items_created' => $createdItems,
                'items_restored' => $restoredItems,
                'details_created' => $createdDetails,
                'details_restored' => $restoredDetails,
            ];
        });

        return redirect()
            ->route('category-items.import.inspection')
            ->with('import_result', $result)
            ->with('import_preview', null)
            ->with('import_warnings', null);
    }

    public function ppeApply(Request $request)
    {
        /** @disregard P1013 */
        abort_if(!auth()->user()->can('category_item_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $teamId = $this->currentTeamId();
        if ($teamId <= 0) {
            return back()->withErrors(['file' => 'No active site selected.']);
        }

        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt'],
            'confirm' => ['required', 'accepted'],
        ]);

        $file = $request->file('file');
        if (!$file instanceof UploadedFile) {
            return back()->withErrors(['file' => 'Invalid file upload.']);
        }

        $parsed = $this->parseItemDetailCsv($file);
        if (!empty($parsed['errors'])) {
            return back()->withErrors($parsed['errors']);
        }

        $result = DB::transaction(function () use ($teamId, $parsed) {
            $createdItems = 0;
            $restoredItems = 0;
            $createdDetails = 0;
            $restoredDetails = 0;

            foreach ($parsed['items'] as $itemRow) {
                $itemName = (string) ($itemRow['item_name'] ?? '');
                $detailNames = $itemRow['details'] ?? [];

                $item = $this->findOrCreateCategoryItem(
                    siteId: $teamId,
                    typeInspectionId: self::TYPE_INSPECTION_PPE,
                    itemName: $itemName,
                    createdCount: $createdItems,
                    restoredCount: $restoredItems
                );

                $detailMap = $this->existingDetailsMapForItem($item->id);

                foreach ($detailNames as $detailName) {
                    $key = $this->normalizeKey($detailName);
                    if ($key === '') {
                        continue;
                    }

                    if (isset($detailMap[$key])) {
                        $detail = $detailMap[$key];
                        if ($detail->deleted_at !== null) {
                            $detail->deleted_at = null;
                            $detail->save();
                            $restoredDetails++;
                        }
                        continue;
                    }

                    CategoryDetail::create([
                        'category_item_id' => $item->id,
                        'name' => $detailName,
                        'in_use_date' => now()->toDateTimeString(),
                        'out_use_date' => null,
                    ]);
                    $createdDetails++;
                }
            }

            return [
                'items_created' => $createdItems,
                'items_restored' => $restoredItems,
                'details_created' => $createdDetails,
                'details_restored' => $restoredDetails,
            ];
        });

        return redirect()
            ->route('equipment-items.import.ppe')
            ->with('import_result', $result)
            ->with('import_preview', null)
            ->with('import_warnings', null);
    }

    private function currentTeamId(): int
    {
        $teamId = (int) (Session::get('team_id') ?? 0);
        if ($teamId > 0) {
            return $teamId;
        }

        /** @disregard P1013 */
        $user = auth()->user();
        $fallback = (int) (optional($user)->teams->first()?->id ?? 0);
        if ($fallback > 0) {
            Session::put('team_id', $fallback);
        }

        return $fallback;
    }

    private function streamCsv(string $filename, array $rows): StreamedResponse
    {
        $callback = function () use ($rows) {
            $out = fopen('php://output', 'wb');
            if ($out === false) {
                return;
            }

            // UTF-8 BOM so Excel reliably opens as UTF-8.
            fwrite($out, "\xEF\xBB\xBF");

            foreach ($rows as $row) {
                // Support Excel delimiter hint line (must not be quoted).
                if (is_array($row) && count($row) === 1 && is_string($row[0]) && str_starts_with($row[0], 'sep=')) {
                    fwrite($out, $row[0] . "\n");
                    continue;
                }

                fputcsv($out, $row);
            }
            fclose($out);
        };

        return response()->streamDownload($callback, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * Parses a row-per-detail CSV with headers:
     * - item_name
     * - detail_name
     *
     * Returns:
     * - total_rows
     * - items: [{ item_name: string, details_count: int, details: string[] }]
     * - errors: [field => message]
     */
    private function parseItemDetailCsv(UploadedFile $file): array
    {
        $errors = [];
        $items = [];
        $totalRows = 0;

        $handle = fopen($file->getRealPath(), 'rb');
        if ($handle === false) {
            return [
                'total_rows' => 0,
                'items' => [],
                'errors' => ['file' => 'Could not read uploaded file.'],
            ];
        }

        $firstLine = fgets($handle);
        if ($firstLine === false) {
            fclose($handle);
            return [
                'total_rows' => 0,
                'items' => [],
                'errors' => ['file' => 'CSV appears to be empty.'],
            ];
        }

        $firstLine = $this->stripUtf8Bom($firstLine);
        $delimiter = null;
        $headerLine = $firstLine;

        // Excel/Sheets sometimes include a delimiter hint line like: sep=;
        if (preg_match('/^\s*sep\s*=\s*(.)\s*$/i', trim($firstLine), $m)) {
            $delimiter = (string) ($m[1] ?? '');
            $headerLine = fgets($handle);
            if ($headerLine === false) {
                fclose($handle);
                return [
                    'total_rows' => 0,
                    'items' => [],
                    'errors' => ['file' => 'CSV header row missing after sep= line.'],
                ];
            }
            $headerLine = $this->stripUtf8Bom($headerLine);
        }

        $headerParse = $this->parseHeaderLine($headerLine, $delimiter);
        $delimiter = $headerParse['delimiter'];
        $itemHeader = $headerParse['itemHeader'];
        $detailHeader = $headerParse['detailHeader'];

        if ($delimiter === null || $itemHeader === null || $detailHeader === null) {
            fclose($handle);
            return [
                'total_rows' => 0,
                'items' => [],
                'errors' => ['file' => 'CSV header must include item_name and detail_name columns.'],
            ];
        }

        $grouped = [];
        while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
            $totalRows++;

            $itemName = trim((string) ($row[$itemHeader] ?? ''));
            $detailName = trim((string) ($row[$detailHeader] ?? ''));

            if ($itemName === '' && $detailName === '') {
                continue;
            }

            if ($itemName === '') {
                $errors['file'] = 'Some rows are missing item_name.';
                continue;
            }

            $key = $this->normalizeKey($itemName);
            $grouped[$key] ??= [
                'item_name' => $itemName,
                'details' => [],
            ];

            if ($detailName !== '') {
                $grouped[$key]['details'][] = $detailName;
            }
        }
        fclose($handle);

        foreach ($grouped as $key => $g) {
            $details = $g['details'] ?? [];
            $details = array_values(array_filter(array_map('trim', $details), fn($v) => $v !== ''));
            $details = $this->uniqueByNormalizedKey($details);

            if (count($details) === 0) {
                $errors['file'] = 'Each item must have at least 1 detail_name row.';
            }

            $items[] = [
                'item_name' => (string) ($g['item_name'] ?? ''),
                'details_count' => count($details),
                'details' => $details,
            ];
        }

        usort($items, fn($a, $b) => strcmp($a['item_name'], $b['item_name']));

        return [
            'total_rows' => $totalRows,
            'items' => $items,
            'errors' => $errors,
        ];
    }

    private function stripUtf8Bom(string $value): string
    {
        return preg_replace('/^\xEF\xBB\xBF/', '', $value) ?? $value;
    }

    /**
     * Try to parse the header line with a chosen delimiter, or auto-detect if none is provided.
     *
     * Returns:
     * - delimiter (string|null)
     * - itemHeader (int|null)
     * - detailHeader (int|null)
     */
    private function parseHeaderLine(string $headerLine, ?string $delimiter): array
    {
        $candidates = [];
        if (is_string($delimiter) && strlen($delimiter) === 1) {
            $candidates[] = $delimiter;
        }
        foreach ([',', ';', "\t", '|'] as $d) {
            if (!in_array($d, $candidates, true)) {
                $candidates[] = $d;
            }
        }

        foreach ($candidates as $d) {
            $headerRow = str_getcsv($headerLine, $d);
            if (!is_array($headerRow) || count($headerRow) < 2) {
                continue;
            }

            $normalized = array_map(fn($h) => $this->normalizeHeader((string) $h), $headerRow);
            $indexByHeader = [];
            foreach ($normalized as $idx => $header) {
                if ($header === '') {
                    continue;
                }
                $indexByHeader[$header] = $idx;
            }

            $itemHeaderIdx = $this->firstHeaderMatch($indexByHeader, ['item_name', 'item', 'element', 'name']);
            $detailHeaderIdx = $this->firstHeaderMatch($indexByHeader, ['detail_name', 'detail', 'identification', 'component']);

            if ($itemHeaderIdx !== null && $detailHeaderIdx !== null) {
                return [
                    'delimiter' => $d,
                    'itemHeader' => $itemHeaderIdx,
                    'detailHeader' => $detailHeaderIdx,
                ];
            }
        }

        return [
            'delimiter' => null,
            'itemHeader' => null,
            'detailHeader' => null,
        ];
    }

    private function buildPreviewWarnings(int $siteId, int $typeInspectionId, array $items): array
    {
        $warnings = [];

        $normalizedNames = [];
        foreach ($items as $item) {
            $name = (string) ($item['item_name'] ?? '');
            $key = $this->normalizeKey($name);
            if ($key === '') {
                continue;
            }
            $normalizedNames[$key] = $name;
        }

        $keys = array_keys($normalizedNames);
        if (empty($keys)) {
            return $warnings;
        }

        // If there are already duplicates in DB for the same normalized name, importing is ambiguous.
        // We warn and on apply we pick the lowest non-deleted id.
        $existing = CategoryItem::withTrashed()
            ->select(['id', 'name', 'deleted_at'])
            ->where('site_id', $siteId)
            ->where('type_inspection_id', $typeInspectionId)
            ->get()
            ->groupBy(fn($item) => $this->normalizeKey((string) $item->name))
            ->filter(fn($group) => $group->count() > 1);

        foreach ($existing as $key => $group) {
            if (!in_array($key, $keys, true)) {
                continue;
            }
            $warnings[] = [
                'type' => 'db_duplicate_item_name',
                'key' => $key,
                'name_variants' => $group->pluck('name')->unique()->values()->all(),
                'ids' => $group->pluck('id')->values()->all(),
            ];
        }

        return $warnings;
    }

    private function findOrCreateCategoryItem(int $siteId, int $typeInspectionId, string $itemName, int &$createdCount, int &$restoredCount): CategoryItem
    {
        $key = $this->normalizeKey($itemName);

        $existing = CategoryItem::withTrashed()
            ->where('site_id', $siteId)
            ->where('type_inspection_id', $typeInspectionId)
            ->whereRaw('LOWER(TRIM(name)) = ?', [$key])
            ->orderByRaw('deleted_at is not null') // non-deleted first
            ->orderBy('id')
            ->get();

        if ($existing->isEmpty()) {
            $createdCount++;
            return CategoryItem::create([
                'site_id' => $siteId,
                'type_inspection_id' => $typeInspectionId,
                'name' => $itemName,
                'certificate_recieved_id' => 2,
            ]);
        }

        /** @var CategoryItem $item */
        $item = $existing->first();
        if ($item->deleted_at !== null) {
            $item->deleted_at = null;
            $item->save();
            $restoredCount++;
        }

        return $item;
    }

    /**
     * @return array<string, CategoryDetail> normalized_key => detail model (withTrashed)
     */
    private function existingDetailsMapForItem(int $categoryItemId): array
    {
        $details = CategoryDetail::withTrashed()
            ->where('category_item_id', $categoryItemId)
            ->orderBy('id')
            ->get();

        $map = [];
        foreach ($details as $detail) {
            $key = $this->normalizeKey((string) $detail->name);
            if ($key === '') {
                continue;
            }
            $map[$key] ??= $detail;
        }

        return $map;
    }

    private function normalizeHeader(string $header): string
    {
        $header = trim($header);
        $header = preg_replace('/^\xEF\xBB\xBF/', '', $header) ?? $header;
        $header = strtolower($header);
        $header = preg_replace('/\s+/', '_', $header) ?? $header;
        $header = preg_replace('/[^a-z0-9_]+/', '', $header) ?? $header;
        return $header;
    }

    private function firstHeaderMatch(array $indexByHeader, array $candidates): ?int
    {
        foreach ($candidates as $c) {
            $c = $this->normalizeHeader((string) $c);
            if (array_key_exists($c, $indexByHeader)) {
                return (int) $indexByHeader[$c];
            }
        }
        return null;
    }

    private function normalizeKey(string $value): string
    {
        $value = trim($value);
        $value = preg_replace('/\s+/', ' ', $value) ?? $value;
        $value = mb_strtolower($value);
        return trim($value);
    }

    private function uniqueByNormalizedKey(array $values): array
    {
        $out = [];
        $seen = [];
        foreach ($values as $v) {
            $display = trim((string) $v);
            if ($display === '') {
                continue;
            }
            $key = $this->normalizeKey($display);
            if ($key === '' || isset($seen[$key])) {
                continue;
            }
            $seen[$key] = true;
            $out[] = $display;
        }
        return $out;
    }

    private function slugFilename(string $value): string
    {
        $value = trim($value);
        $value = preg_replace('/\s+/', '-', $value) ?? $value;
        $value = preg_replace('/[^A-Za-z0-9\\-]+/', '', $value) ?? $value;
        $value = strtolower($value);
        return $value !== '' ? $value : 'site';
    }
}
