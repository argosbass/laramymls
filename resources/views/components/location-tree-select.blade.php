@php
    use App\Models\PropertyLocations;

    function buildTree($nodes)
    {
        return $nodes->map(function ($node) {
            return [
                'id' => $node->id,
                'text' => $node->location_name,
                'children' => buildTree($node->children),
                'type' =>  'default',
            ];
        });
    }

    $treeData = buildTree(PropertyLocations::defaultOrder()->get()->toTree());
    $selectedId = $property_location_id ?? null;
@endphp

<div wire:ignore>
    <label class="block font-medium text-sm text-gray-700 mb-1">Seleccionar ubicación</label>
    <input type="text" id="location-search" placeholder="Buscar..." class="mb-2 px-2 py-1 border rounded w-full" />
    <div id="location-tree"></div>
</div>

@push('styles')
    <!-- jsTree CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.3.12/themes/default/style.min.css" rel="stylesheet" />
    <!-- Font Awesome CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
@endpush

@push('scripts')
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- jsTree JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.3.12/jstree.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const treeData = @json($treeData);
            const selectedId = @json($selectedId);

            const $tree = $('#location-tree');

            $tree.jstree({
                core: {
                    data: treeData,
                    multiple: false,
                    check_callback: true,
                    themes: {

                    }
                },
                plugins: ['search', 'types', 'wholerow'],
                types: {
                    "default": {
                        "icon": "fas fa-map-marker-alt"
                    },
                    "folder": {
                        "icon": "fas fa-folder-open"
                    }
                }
            });

            $tree.on('ready.jstree', function () {
                if (selectedId) {
                    $tree.jstree('open_node', selectedId, function () {
                        $tree.jstree('select_node', selectedId);
                    });
                }
            });

            $tree.on("select_node.jstree", function (e, data) {
                const locationId = data.node.id;
            @this.set('property_location_id', locationId);
            });

            let to = false;
            $('#location-search').on('keyup', function () {
                if (to) clearTimeout(to);
                to = setTimeout(() => {
                    $tree.jstree(true).search(this.value);
                }, 250);
            });
        });
    </script>
@endpush
