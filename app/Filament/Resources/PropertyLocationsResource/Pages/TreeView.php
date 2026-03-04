<?php

namespace App\Filament\Resources\PropertyLocationsResource\Pages;

use App\Models\PropertyLocations;
use Filament\Resources\Pages\Page;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\DB;
use Filament\Notifications\Notification;

class TreeView extends Page
{
    protected static string $resource = \App\Filament\Resources\PropertyLocationsResource::class;

    protected static string $view = 'filament.resources.property-locations-resource.pages.tree-view';

    public array $treeData = [];

    public function mount(): void
    {
        $this->loadTreeData();
    }

    public function loadTreeData(): void
    {
        // withDepth() si quieres usar depth (aunque sea calculado)
        $tree = PropertyLocations::query()
            ->withDepth()
            ->defaultOrder()
            ->get()
            ->toTree();

        $this->treeData = $this->formatTreeNodes($tree)->all();
    }

    private function formatTreeNodes($nodes)
    {
        return $nodes->map(function ($node) {
            return [
                'id' => (string) $node->id,
                'text' => $node->location_name,
                'children' => $this->formatTreeNodes($node->children)->all(),
            ];
        });
    }

    /**
     * Payload esperado desde JS:
     * [
     *   'parent_id' => '12' | null,     // null = root "#"
     *   'children'  => ['5','9','2']    // ids en el orden final dentro de ese parent
     * ]
     */

    #[On('persistOrder')]
    public function persistOrder(array $payload): void
    {
        // ✅ define children (tu bug principal)
        $children = $payload['children'] ?? [];

        $parentId = $payload['parent_id'] ?? null;
        if ($parentId === '#' || $parentId === '' || $parentId === 0 || $parentId === '0') {
            $parentId = null; // depth 0
        }

        if (!is_array($children) || empty($children)) {
            return;
        }

        $apply = function ($result): void {
            // kalnoy/nestedset: algunas versiones devuelven Model, otras bool
            if ($result instanceof \Illuminate\Database\Eloquent\Model) {
                $result->save();
                return;
            }

            if (is_bool($result)) {
                if ($result === false) {
                    throw new \RuntimeException('NestedSet operation returned false.');
                }
                return;
            }

            if (is_object($result) && method_exists($result, 'save')) {
                $result->save();
            }
        };

        DB::transaction(function () use ($parentId, $children, $apply) {

            $parent = $parentId ? PropertyLocations::findOrFail($parentId) : null;

            // 🔒 validaciones básicas
            if ($parent) {
                foreach ($children as $childId) {
                    if ((string) $childId === (string) $parent->id) {
                        throw new \RuntimeException('Invalid payload: parent cannot be inside its own children list.');
                    }
                }
            }

            $prev = null;

            foreach ($children as $childId) {
                $child = PropertyLocations::findOrFail($childId);

                // 🔒 no permitir mover un nodo debajo de su descendiente
                if ($parent && ($parent->isDescendantOf($child) || $parent->getKey() === $child->getKey())) {
                    throw new \RuntimeException('Invalid move: cannot move a node under its own descendant.');
                }

                if ($prev === null) {
                    // ✅ primer elemento del grupo
                    if ($parent) {
                        $apply($child->prependToNode($parent));
                    } else {
                        // ✅ ROOT (depth 0)
                        $apply($child->makeRoot());
                    }
                } else {
                    // ✅ siguientes: quedan después del anterior (mismo nivel / mismo parent)
                    $apply($child->insertAfterNode($prev));
                }

                $prev = $child->fresh();
            }
        });

        // si tienes columna depth y quieres persistirla:
        $this->recalcDepthColumn();

        $this->loadTreeData();

        Notification::make()
            ->title('Locations Updated!')
            ->success()
            ->send();

        $this->dispatch('tree-synced', treeData: $this->treeData);
    }

    private function recalcDepthColumn(): void
    {
        // depth = (#ancestros) - 1
        DB::statement("
        UPDATE property_locations AS node
        JOIN (
            SELECT n.id, (COUNT(p.id) - 1) AS depth
            FROM property_locations AS n
            JOIN property_locations AS p
              ON n._lft BETWEEN p._lft AND p._rgt
            GROUP BY n.id
        ) AS d ON d.id = node.id
        SET node.depth = d.depth
    ");
    }
    /**
     * Compatibilidad: en algunas versiones de kalnoy, los métodos retornan bool (ya ejecutó),
     * en otras retornan el modelo y hay que ->save()
     */
    private function applyNestedResult($result): void
    {
        if ($result instanceof \Illuminate\Database\Eloquent\Model) {
            $result->save();
            return;
        }

        if (is_bool($result)) {
            if ($result === false) {
                throw new \RuntimeException('NestedSet operation returned false.');
            }
            return;
        }

        if (is_object($result) && method_exists($result, 'save')) {
            $result->save();
        }
    }
}
