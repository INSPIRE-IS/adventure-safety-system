<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TypeDrill;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TypeDrillImportController extends Controller
{
    public function index(Request $request)
    {
        /** @disregard P1013 */
        abort_if(!auth()->user()->can('type_drill_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return Inertia::render('TypeDrill/Import', [
            'preview' => session('import_preview'),
            'result' => session('import_result'),
            'warnings' => session('import_warnings') ?? [],
        ]);
    }

    public function template(): StreamedResponse
    {
        /** @disregard P1013 */
        abort_if(!auth()->user()->can('type_drill_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $rows = [
            ['sep=,'],
            ['name', 'due_days'],
            ['Fire Fighting', '14'],
            ['Evacuation', '14'],
            ['CPR', '14'],
        ];

        return $this->streamCsv('type-drills-template.csv', $rows);
    }

    public function export(): StreamedResponse
    {
        /** @disregard P1013 */
        abort_if(!auth()->user()->can('type_drill_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $drills = TypeDrill::query()
            ->orderBy('name')
            ->get(['name', 'due_days']);

        $rows = [
            ['sep=,'],
            ['name', 'due_days'],
        ];

        foreach ($drills as $d) {
            $rows[] = [
                (string) $d->name,
                (string) ($d->due_days ?? 14),
            ];
        }

        return $this->streamCsv('type-drills-export.csv', $rows);
    }

    public function preview(Request $request)
    {
        /** @disregard P1013 */
        abort_if(!auth()->user()->can('type_drill_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt'],
        ]);

        $file = $request->file('file');
        if (!$file instanceof UploadedFile) {
            return back()->withErrors(['file' => 'Invalid file upload.']);
        }

        $parsed = $this->parseTypeDrillCsv($file);
        if (!empty($parsed['errors'])) {
            return back()->withErrors($parsed['errors']);
        }

        $warnings = $this->buildPreviewWarnings($parsed['items']);

        $preview = [
            'total_rows' => (int) $parsed['total_rows'],
            'items_count' => count($parsed['items']),
            'items' => $parsed['items'],
        ];

        return redirect()
            ->route('type-drills.import')
            ->with('import_preview', $preview)
            ->with('import_warnings', $warnings);
    }

    public function apply(Request $request)
    {
        /** @disregard P1013 */
        abort_if(!auth()->user()->can('type_drill_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt'],
            'confirm' => ['required', 'accepted'],
        ]);

        $file = $request->file('file');
        if (!$file instanceof UploadedFile) {
            return back()->withErrors(['file' => 'Invalid file upload.']);
        }

        $parsed = $this->parseTypeDrillCsv($file);
        if (!empty($parsed['errors'])) {
            return back()->withErrors($parsed['errors']);
        }

        $result = DB::transaction(function () use ($parsed) {
            $created = 0;
            $updated = 0;
            $restored = 0;

            foreach ($parsed['items'] as $row) {
                $name = trim((string) ($row['name'] ?? ''));
                if ($name === '') {
                    continue;
                }

                $dueDays = $row['due_days'] ?? null;
                if ($dueDays !== null) {
                    $dueDays = (int) $dueDays;
                }

                $key = $this->normalizeKey($name);

                $existing = TypeDrill::withTrashed()
                    ->whereRaw('LOWER(TRIM(name)) = ?', [$key])
                    ->orderByRaw('deleted_at is not null') // non-deleted first
                    ->orderBy('id')
                    ->first();

                if (!$existing) {
                    TypeDrill::create([
                        'name' => $name,
                        'due_days' => $dueDays ?? 14,
                    ]);
                    $created++;
                    continue;
                }

                $wasDeleted = $existing->deleted_at !== null;
                $existing->deleted_at = null;

                // Only overwrite due_days if the sheet includes it.
                if ($dueDays !== null) {
                    $existing->due_days = $dueDays;
                }

                $dirty = $existing->isDirty();
                $existing->save();

                if ($wasDeleted) {
                    $restored++;
                } elseif ($dirty) {
                    $updated++;
                }
            }

            return [
                'created' => $created,
                'updated' => $updated,
                'restored' => $restored,
            ];
        });

        return redirect()
            ->route('type-drills.import')
            ->with('import_result', $result)
            ->with('import_preview', null)
            ->with('import_warnings', null);
    }

    private function buildPreviewWarnings(array $items): array
    {
        $warnings = [];

        // If there are already duplicates in DB for the same normalized name, importing is ambiguous.
        $normalizedNames = [];
        foreach ($items as $it) {
            $name = (string) ($it['name'] ?? '');
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

        $existing = TypeDrill::withTrashed()
            ->select(['id', 'name', 'deleted_at'])
            ->get()
            ->groupBy(fn($row) => $this->normalizeKey((string) $row->name))
            ->filter(fn($group) => $group->count() > 1);

        foreach ($existing as $key => $group) {
            if (!in_array($key, $keys, true)) {
                continue;
            }
            $warnings[] = [
                'type' => 'db_duplicate_type_drill_name',
                'key' => $key,
                'name_variants' => $group->pluck('name')->unique()->values()->all(),
                'ids' => $group->pluck('id')->values()->all(),
            ];
        }

        return $warnings;
    }

    /**
     * Parses a CSV with headers:
     * - name
     * - due_days (optional)
     *
     * Returns:
     * - total_rows
     * - items: [{ name: string, due_days: int|null }]
     * - errors: [field => message]
     */
    private function parseTypeDrillCsv(UploadedFile $file): array
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
        $nameHeader = $headerParse['nameHeader'];
        $dueHeader = $headerParse['dueHeader'];

        if ($delimiter === null || $nameHeader === null) {
            fclose($handle);
            return [
                'total_rows' => 0,
                'items' => [],
                'errors' => ['file' => 'CSV header must include a name column.'],
            ];
        }

        $seen = [];
        while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
            $totalRows++;

            $name = trim((string) ($row[$nameHeader] ?? ''));
            $dueRaw = $dueHeader !== null ? trim((string) ($row[$dueHeader] ?? '')) : '';

            if ($name === '' && $dueRaw === '') {
                continue;
            }

            if ($name === '') {
                $errors['file'] = 'Some rows are missing name.';
                continue;
            }

            $key = $this->normalizeKey($name);
            if ($key === '') {
                continue;
            }
            if (isset($seen[$key])) {
                $errors['file'] = 'Your CSV contains duplicate drill type names (case-insensitive).';
                continue;
            }
            $seen[$key] = true;

            $dueDays = null;
            if ($dueRaw !== '') {
                if (!ctype_digit($dueRaw)) {
                    $errors['file'] = 'due_days must be a whole number (or blank).';
                    continue;
                }
                $dueDays = (int) $dueRaw;
            }

            $items[] = [
                'name' => $name,
                'due_days' => $dueDays,
            ];
        }
        fclose($handle);

        usort($items, fn($a, $b) => strcmp($a['name'], $b['name']));

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
     * - nameHeader (int|null)
     * - dueHeader (int|null)
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
            if (!is_array($headerRow) || count($headerRow) < 1) {
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

            $nameHeaderIdx = $this->firstHeaderMatch($indexByHeader, ['name', 'drill_type', 'type_drill', 'type']);
            $dueHeaderIdx = $this->firstHeaderMatch($indexByHeader, ['due_days', 'due', 'days']);

            if ($nameHeaderIdx !== null) {
                return [
                    'delimiter' => $d,
                    'nameHeader' => $nameHeaderIdx,
                    'dueHeader' => $dueHeaderIdx,
                ];
            }
        }

        return [
            'delimiter' => null,
            'nameHeader' => null,
            'dueHeader' => null,
        ];
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
}

