<?php
namespace App\Filament\Resources\PropertyLocationsResource\Pages;

use App\Models\PropertyLocations;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\Page;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\DB;

class TreeView extends Page
{
    protected static string $resource = \App\Filament\Resources\PropertyLocationsResource::class;

    protected static string $view = 'filament.resources.property-locations-resource.pages.tree-view';

    public $treeData = [];

    public function mount(): void
    {
        $this->loadTreeData();
    }

    #[On('updateTreeOrder')]
    public function updateTree(array $tree)
    {
        DB::transaction(function () use ($tree) {

                PropertyLocations::where('id', $tree['id'])->update([
                    'parent_id' => $tree['parent_id'],
                ]);

        });

        $this->dispatch('notify', type: 'success', message: 'Árbol actualizado correctamente');
        $this->loadTreeData();
    }

    /*public function loadTreeData()
    {
        $this->treeData = PropertyLocations::get()->map(fn($loc) => [
            'id' => $loc->id,
            'parent' => $loc->parent_id === null || $loc->parent_id == 0 ? '#' : $loc->parent_id,
            'text' => $loc->location_name,
        ]);

    }*/

    public function loadTreeData()
    {

        $tree = PropertyLocations::defaultOrder()->get()->toTree();

        $this->treeData = $this->formatTreeNodes($tree);
    }

    private function formatTreeNodes($nodes)
    {
        return $nodes->map(function ($node) {
            return [
                'id' => (string) $node->id,
                'text' => $node->location_name,
                'children' => $this->formatTreeNodes($node->children),
                'type' => 'default',
            ];
        });
    }
}
