<?php

namespace App\Http\Controllers\Fiu;

use App\Http\Controllers\Controller;
use App\Http\Requests\Fiu\StoreEffectivenessDocumentRequest;
use App\Models\EffectivenessDocument;
use App\Models\EffectivenessImmediateOutcome;
use App\Models\EffectivenessSubImmediateOutcome;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class EffectivenessFolderController extends Controller
{
   public function index(): View
    {
        $immediateOutcomes = EffectivenessImmediateOutcome::query()
            // 🌟 FIXED: Removed 'Relation' so Laravel 13 handles the Builder instance dynamically
            ->with(['subOutcomes' => fn ($query) => $query->active()->orderBy('sort_order')->orderBy('code')])
            
            // 🌟 FIXED: Removed 'Relation' here too so it accepts the Relation instance dynamically
            ->withCount(['subOutcomes' => fn ($query) => $query->active()])
            
            ->active()
            ->orderBy('sort_order')
            ->orderBy('number')
            ->get();

        // Safe fallback if this method exists, otherwise you can comment it out
        $documentCounts = method_exists($this, 'subIoDocumentCounts') ? $this->subIoDocumentCounts() : [];

        return view('fiu.tracks.Effectiveness.index', [
            'immediateOutcomes' => $immediateOutcomes,
            'documentCounts'    => $documentCounts,
        ]);
    }

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

        $subOutcomes = $immediateOutcome->subOutcomes->values();
        $selectedSubOutcome = $this->resolveSelectedSubOutcome($immediateOutcome, request('sub_io'));
        $documentsBySubIo = $this->documentsPerSubIo($subOutcomes->pluck('id')->all());

        $documents = $this->documentTableExists()
            ? EffectivenessDocument::query()
                ->with($this->documentRelations())
                ->when(
                    $selectedSubOutcome && $this->documentHasSubIoColumn(),
                    fn (Builder $query) => $query->where('effectiveness_sub_io_id', $selectedSubOutcome->id)
                )
                ->when(
                    ! $selectedSubOutcome && $this->documentHasSubIoColumn(),
                    fn (Builder $query) => $query->whereRaw('1 = 0')
                )
                ->when(
                    $selectedSubOutcome && ! $this->documentHasSubIoColumn() && $this->documentHasSubIoCodeColumn(),
                    fn (Builder $query) => $query->where('io_sub_code', $selectedSubOutcome->code)
                )
                ->when(
                    ! $selectedSubOutcome && ! $this->documentHasSubIoColumn() && $this->documentHasSubIoCodeColumn(),
                    fn (Builder $query) => $query->whereRaw('1 = 0')
                )
                ->when(
                    ! $this->documentHasSubIoColumn() && ! $this->documentHasSubIoCodeColumn() && $this->documentHasMainIoCodeColumn(),
                    fn (Builder $query) => $query->where('io_main_code', $immediateOutcome->code)
                )
                ->when($this->documentHasStatusColumn(), fn (Builder $query) => $query->orderByRaw($this->documentStatusOrderSql()))
                ->latest($this->documentsLatestColumn())
                ->paginate(15)
                ->withQueryString()
            : EffectivenessDocument::query()->whereRaw('1 = 0')->paginate(15);

        return view('fiu.tracks.Effectiveness.show', [
            'immediateOutcome' => $immediateOutcome,
            'subOutcomes' => $subOutcomes,
            'selectedSubOutcome' => $selectedSubOutcome,
            'documents' => $documents,
            'documentsBySubIo' => $documentsBySubIo,
            'documentStatuses' => $this->documentStatusOptions(),
        ]);
    }

    public function create(string $code): View
    {
        $immediateOutcome = EffectivenessImmediateOutcome::query()
            ->active()
            ->with(['subOutcomes' => fn (Relation $query) => $query->active()->orderBy('sort_order')->orderBy('code')])
            ->where('code', strtoupper($code))
            ->first();

        if (! $immediateOutcome) {
            throw (new ModelNotFoundException())->setModel(EffectivenessImmediateOutcome::class, [$code]);
        }

        $selectedSubOutcome = $this->resolveSelectedSubOutcome($immediateOutcome, request('sub_io'));

        return view('fiu.tracks.Effectiveness.create-document', [
            'immediateOutcome' => $immediateOutcome,
            'subOutcomes' => $immediateOutcome->subOutcomes->values(),
            'selectedSubOutcome' => $selectedSubOutcome,
            'documentStatuses' => $this->documentStatusOptions(),
        ]);
    }

    public function store(StoreEffectivenessDocumentRequest $request, string $code): RedirectResponse
    {
        $immediateOutcome = EffectivenessImmediateOutcome::query()
            ->active()
            ->where('code', strtoupper($code))
            ->first();

        if (! $immediateOutcome) {
            throw (new ModelNotFoundException())->setModel(EffectivenessImmediateOutcome::class, [$code]);
        }

        if ($immediateOutcome->id !== $request->integer('immediate_outcome_id')) {
            abort(422, 'The selected main IO does not match the current workspace.');
        }

        $subOutcome = EffectivenessSubImmediateOutcome::query()
            ->whereKey($request->integer('effectiveness_sub_io_id'))
            ->where('immediate_outcome_id', $immediateOutcome->id)
            ->firstOrFail();

        $data = $request->validated();
        $uploadedFile = $request->file('document_file');
        $storedDisk = $data['disk'] ?? 'public';
        $storedPath = $data['external_file_path'] ?? null;
        $storedName = $data['external_file_name'] ?? null;

        if ($uploadedFile) {
            $directory = 'effectiveness-documents/'.Str::slug($immediateOutcome->code).'/'.Str::slug($subOutcome->code);
            $storedPath = $uploadedFile->store($directory, $storedDisk);
            $storedName = $uploadedFile->getClientOriginalName();
        }

        $document = EffectivenessDocument::query()->create([
            'effectiveness_sub_io_id' => $subOutcome->id,
            'title' => $data['title'],
            'name' => $data['name'] ?? $data['title'],
            'reporting_institution' => $data['reporting_institution'],
            'status' => $data['status'],
            'file_name' => $storedName,
            'file_path' => $storedPath,
            'disk' => $storedDisk,
            'date_logged' => $data['date_logged'],
            'document_date' => $data['document_date'] ?? null,
            'submitted_at' => in_array($data['status'], ['submitted', 'under_review', 'approved'], true) ? now() : null,
            'approved_at' => $data['status'] === 'approved' ? now() : null,
            'remarks' => $data['remarks'] ?? null,
            'meta' => array_filter([
                'uploaded_via' => 'split_dashboard_form',
                'main_io_code' => $immediateOutcome->code,
                'sub_io_code' => $subOutcome->code,
                'storage_url' => $storedPath ? Storage::disk($storedDisk)->url($storedPath) : null,
                'mime_type' => $uploadedFile?->getClientMimeType(),
                'size_bytes' => $uploadedFile?->getSize(),
            ], fn ($value) => ! is_null($value) && $value !== ''),
        ]);

        return redirect()
            ->route('fiu.effectiveness.folders.show', [
                'code' => $immediateOutcome->code,
                'sub_io' => $subOutcome->code,
            ])
            ->with('status', 'Document "'.$document->title.'" was added successfully to '.$subOutcome->code.'.');
    }

    protected function resolveSelectedSubOutcome(EffectivenessImmediateOutcome $immediateOutcome, ?string $requestedSubIo)
    {
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
        if (! $this->documentTableExists() || ! $this->documentHasSubIoColumn()) {
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

    protected function documentStatusOptions(): array
    {
        return [
            'logged' => 'Logged',
            'submitted' => 'Submitted',
            'under_review' => 'Under review',
            'revision_requested' => 'Revision requested',
            'approved' => 'Approved',
            'archived' => 'Archived',
        ];
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