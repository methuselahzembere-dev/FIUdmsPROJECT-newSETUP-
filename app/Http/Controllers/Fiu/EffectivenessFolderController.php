<?php

namespace App\Http\Controllers\Fiu;

use App\Http\Controllers\Controller;
use App\Models\EffectivenessDocument;
use App\Models\EffectivenessImmediateOutcome;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class EffectivenessFolderController extends Controller
{
    /**
     * Display the top-level Effectiveness workspace grouped by main IOs 1-11.
     */
public function index(): View
    {
        $immediateOutcomes = EffectivenessImmediateOutcome::query()
            // 🌟 REMOVED Builder type-hint -> handled dynamically
            ->with(['subOutcomes' => fn ($query) => $query->active()->orderBy('sort_order')->orderBy('code')])
            
            // 🌟 REMOVED Relation type-hint -> handled dynamically
            ->withCount(['subOutcomes' => fn ($query) => $query->active()])
            
            ->active()
            ->orderBy('sort_order')
            ->orderBy('number')
            ->get();

        $documentCounts = $this->subIoDocumentCounts();

        return view('fiu.tracks.Effectiveness.index', [
            'immediateOutcomes' => $immediateOutcomes,
            'documentCounts'    => $documentCounts,
        ]);
    }

    /**
     * Display sub-IO drill-down and documents for one main IO.
     */
    public function show(string $code): View
    {
        $immediateOutcome = EffectivenessImmediateOutcome::query()
            ->active()
            ->with(['subOutcomes' => fn (Relation $query) => $query->active()->orderBy('sort_order')->orderBy('code')])
            ->where('code', strtoupper($code))
            ->first();

        if (! $immediateOutcome) {
            throw (new ModelNotFoundException())->setModel(EffectivenessImmediateOutcome::class, [$code]);
        }

        $subIoIds = $immediateOutcome->subOutcomes->pluck('id')->all();

        $documents = EffectivenessDocument::query()
            ->with($this->documentRelations())
            ->when(
                ! empty($subIoIds) && $this->documentHasSubIoColumn(),
                fn (Builder $query) => $query->whereIn('effectiveness_sub_io_id', $subIoIds)
            )
            ->when(
                ! $this->documentHasSubIoColumn() && $this->documentHasMainIoCodeColumn(),
                fn (Builder $query) => $query->where('io_main_code', $immediateOutcome->code)
            )
            ->when($this->documentHasStatusColumn(), fn (Builder $query) => $query->orderByRaw($this->documentStatusOrderSql()))
            ->latest($this->documentsLatestColumn())
            ->paginate(15)
            ->withQueryString();

        $documentsBySubIo = $this->documentsPerSubIo($subIoIds);

        return view('fiu.tracks.Effectiveness.show', [
            'immediateOutcome' => $immediateOutcome,
            'documents' => $documents,
            'documentsBySubIo' => $documentsBySubIo,
        ]);
    }

    /**
     * Provide optional eager-loaded relations only when the model supports them.
     *
     * @return array<int, string>
     */
    protected function documentRelations(): array
    {
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
        if (! $this->documentHasSubIoColumn()) {
            return [];
        }

        return EffectivenessDocument::query()
            ->selectRaw('effectiveness_sub_io_id, count(*) as aggregate')
            ->whereNotNull('effectiveness_sub_io_id')
            ->groupBy('effectiveness_sub_io_id')
            ->pluck('aggregate', 'effectiveness_sub_io_id')
            ->map(fn ($count) => (int) $count)
            ->all();
    }

    protected function documentsPerSubIo(array $subIoIds): array
    {
        if (empty($subIoIds) || ! $this->documentHasSubIoColumn()) {
            return [];
        }

        return EffectivenessDocument::query()
            ->selectRaw('effectiveness_sub_io_id, count(*) as aggregate')
            ->whereIn('effectiveness_sub_io_id', $subIoIds)
            ->groupBy('effectiveness_sub_io_id')
            ->pluck('aggregate', 'effectiveness_sub_io_id')
            ->map(fn ($count) => (int) $count)
            ->all();
    }

    protected function documentsLatestColumn(): string
    {
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

    protected function documentHasStatusColumn(): bool
    {
        return $this->columnExists((new EffectivenessDocument())->getTable(), 'status');
    }

    protected function documentHasSubIoColumn(): bool
    {
        return $this->columnExists((new EffectivenessDocument())->getTable(), 'effectiveness_sub_io_id');
    }

    protected function documentHasMainIoCodeColumn(): bool
    {
        return $this->columnExists((new EffectivenessDocument())->getTable(), 'io_main_code');
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