<?php

namespace App\Http\Controllers\Fiu;

use App\Http\Controllers\Controller;
use App\Models\EffectivenessDocument;
use App\Models\EffectivenessImmediateOutcome;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class EffectivenessFolderController extends Controller
{
    /**
     * Display the Effectiveness workspace.
     *
     * Initial load is safe: it opens on the first active Immediate Outcome,
     * preselects its first active sub-IO when available, and always passes the
     * complete split-dashboard state contract expected by the Blade view.
     */
    public function index(): View
    {
        $immediateOutcomes = $this->immediateOutcomesWithSubOutcomes();
        $documentCounts = $this->subIoDocumentCounts();
        $immediateOutcome = $immediateOutcomes->first();
        $subOutcomes = $immediateOutcome?->subOutcomes?->values() ?? collect();
        $selectedSubOutcome = $this->resolveSelectedSubOutcome($immediateOutcome, request('sub_io'));
        $documentsBySubIo = $this->documentsPerSubIo($subOutcomes->pluck('id')->all());
        $documents = $this->documentsForSelection($immediateOutcome, $selectedSubOutcome);

        return view('fiu.tracks.Effectiveness.index', $this->viewData(
            $immediateOutcomes,
            $immediateOutcome,
            $subOutcomes,
            $selectedSubOutcome,
            $documents,
            $documentsBySubIo,
            $documentCounts
        ));
    }

    /**
     * Display one main IO as a split dashboard with sub-IO navigation.
     */
    public function show(string $code): View
    {
        $immediateOutcomes = $this->immediateOutcomesWithSubOutcomes();
        $immediateOutcome = $immediateOutcomes
            ->first(fn (EffectivenessImmediateOutcome $outcome) => strtoupper($outcome->code) === strtoupper($code));

        if (! $immediateOutcome) {
            throw (new ModelNotFoundException())->setModel(EffectivenessImmediateOutcome::class, [$code]);
        }

        $subOutcomes = $immediateOutcome->subOutcomes->values();
        $selectedSubOutcome = $this->resolveSelectedSubOutcome($immediateOutcome, request('sub_io'));
        $documentsBySubIo = $this->documentsPerSubIo($subOutcomes->pluck('id')->all());
        $documents = $this->documentsForSelection($immediateOutcome, $selectedSubOutcome);
        $documentCounts = $this->subIoDocumentCounts();

        return view('fiu.tracks.Effectiveness.show', $this->viewData(
            $immediateOutcomes,
            $immediateOutcome,
            $subOutcomes,
            $selectedSubOutcome,
            $documents,
            $documentsBySubIo,
            $documentCounts
        ));
    }

    /**
     * @return \Illuminate\Support\Collection<int, \App\Models\EffectivenessImmediateOutcome>
     */
protected function immediateOutcomesWithSubOutcomes(): Collection
    {
        return EffectivenessImmediateOutcome::query()
            ->with([
                // 🌟 FIXED: Dropped type-hints so both HasMany and Builder contexts work flawlessly
                'subOutcomes' => fn ($query) => $query->active()->orderBy('sort_order')->orderBy('code'),
            ])
            ->withCount([
                'subOutcomes' => fn ($query) => $query->active(),
            ])
            ->active()
            ->orderBy('sort_order')
            ->orderBy('number')
            ->get();
    }

    protected function viewData(
        Collection $immediateOutcomes,
        ?EffectivenessImmediateOutcome $immediateOutcome,
        Collection $subOutcomes,
        $selectedSubOutcome,
        LengthAwarePaginator $documents,
        array $documentsBySubIo,
        array $documentCounts
    ): array {
        return [
            'immediateOutcomes' => $immediateOutcomes,
            'immediateOutcome' => $immediateOutcome,
            'subOutcomes' => $subOutcomes,
            'selectedSubOutcome' => $selectedSubOutcome,
            'documents' => $documents,
            'documentsBySubIo' => $documentsBySubIo,
            'documentCounts' => $documentCounts,
        ];
    }

    protected function resolveSelectedSubOutcome(?EffectivenessImmediateOutcome $immediateOutcome, ?string $requestedSubIo)
    {
        if (! $immediateOutcome) {
            return null;
        }

        $subOutcomes = $immediateOutcome->subOutcomes;

        if ($subOutcomes->isEmpty()) {
            return null;
        }

        if ($requestedSubIo) {
            $match = $subOutcomes->first(fn ($subOutcome) => strtoupper($subOutcome->code) === strtoupper($requestedSubIo));

            if ($match) {
                return $match;
            }
        }

        return $subOutcomes->first();
    }

    protected function documentsForSelection(?EffectivenessImmediateOutcome $immediateOutcome, $selectedSubOutcome): LengthAwarePaginator
    {
        if (! $this->documentTableExists()) {
            return $this->emptyDocumentsPaginator();
        }

        $query = EffectivenessDocument::query()->with($this->documentRelations());

        if ($selectedSubOutcome && $this->documentHasSubIoColumn()) {
            $query->where('effectiveness_sub_io_id', $selectedSubOutcome->id);
        } elseif ($selectedSubOutcome && ! $this->documentHasSubIoColumn() && $this->documentHasSubIoCodeColumn()) {
            $query->where('io_sub_code', $selectedSubOutcome->code);
        } elseif (! $selectedSubOutcome && $immediateOutcome && ! $this->documentHasSubIoColumn() && ! $this->documentHasSubIoCodeColumn() && $this->documentHasMainIoCodeColumn()) {
            $query->where('io_main_code', $immediateOutcome->code);
        } else {
            $query->whereRaw('1 = 0');
        }

        if ($this->documentHasStatusColumn()) {
            $query->orderByRaw($this->documentStatusOrderSql());
        }

        return $query
            ->latest($this->documentsLatestColumn())
            ->paginate(15)
            ->withQueryString();
    }

    protected function emptyDocumentsPaginator(): LengthAwarePaginator
    {
        return EffectivenessDocument::query()
            ->whereRaw('1 = 0')
            ->paginate(15)
            ->withQueryString();
    }

    /**
     * Provide optional eager-loaded relations only when the model supports them.
     *
     * @return array<int, string>
     */
    protected function documentRelations(): array
    {
        if (! $this->documentTableExists()) {
            return [];
        }

        $relations = [];

        if (method_exists(EffectivenessDocument::class, 'institution')) {
            $relations[] = 'institution';
        }

        if (method_exists(EffectivenessDocument::class, 'subImmediateOutcome')) {
            $relations[] = 'subImmediateOutcome';
        }

        return $relations;
    }

    protected function subIoDocumentCounts(): array
    {
        if (! $this->documentTableExists()) {
            return [];
        }

        if ($this->documentHasSubIoColumn()) {
            return EffectivenessDocument::query()
                ->selectRaw('effectiveness_sub_io_id, count(*) as aggregate')
                ->whereNotNull('effectiveness_sub_io_id')
                ->groupBy('effectiveness_sub_io_id')
                ->pluck('aggregate', 'effectiveness_sub_io_id')
                ->map(fn ($count) => (int) $count)
                ->all();
        }

        return [];
    }

    protected function documentsPerSubIo(array $subIoIds): array
    {
        if (empty($subIoIds) || ! $this->documentTableExists()) {
            return [];
        }

        if ($this->documentHasSubIoColumn()) {
            return EffectivenessDocument::query()
                ->selectRaw('effectiveness_sub_io_id, count(*) as aggregate')
                ->whereIn('effectiveness_sub_io_id', $subIoIds)
                ->groupBy('effectiveness_sub_io_id')
                ->pluck('aggregate', 'effectiveness_sub_io_id')
                ->map(fn ($count) => (int) $count)
                ->all();
        }

        return [];
    }

    protected function documentsLatestColumn(): string
    {
        if (! $this->documentTableExists()) {
            return 'id';
        }

        $table = (new EffectivenessDocument())->getTable();

        foreach (['created_at', 'date_logged', 'submitted_at', 'id'] as $column) {
            if ($this->columnExists($table, $column)) {
                return $column;
            }
        }

        return 'id';
    }

    protected function documentStatusOrderSql(): string
    {
        return "case
                when status = 'submitted' then 1
                when status = 'under_review' then 2
                when status = 'revision_requested' then 3
                when status = 'approved' then 4
                when status = 'archived' then 5
                else 6
            end";
    }

    protected function documentTableExists(): bool
    {
        static $exists;

        return $exists ??= Schema::hasTable((new EffectivenessDocument())->getTable());
    }

    protected function documentHasStatusColumn(): bool
    {
        return $this->documentTableExists() && $this->columnExists((new EffectivenessDocument())->getTable(), 'status');
    }

    protected function documentHasSubIoColumn(): bool
    {
        return $this->documentTableExists() && $this->columnExists((new EffectivenessDocument())->getTable(), 'effectiveness_sub_io_id');
    }

    protected function documentHasSubIoCodeColumn(): bool
    {
        return $this->documentTableExists() && $this->columnExists((new EffectivenessDocument())->getTable(), 'io_sub_code');
    }

    protected function documentHasMainIoCodeColumn(): bool
    {
        return $this->documentTableExists() && $this->columnExists((new EffectivenessDocument())->getTable(), 'io_main_code');
    }

    protected function columnExists(string $table, string $column): bool
    {
        static $cache = [];

        $key = $table.'.'.$column;

        if (array_key_exists($key, $cache)) {
            return $cache[$key];
        }

        return $cache[$key] = Schema::hasColumn($table, $column);
    }
}