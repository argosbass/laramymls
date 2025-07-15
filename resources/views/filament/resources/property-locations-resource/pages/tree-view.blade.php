<x-filament::page>
    <div wire:ignore>
        <div id="location-tree" style="min-height: 300px;"></div>
    </div>
</x-filament::page>

@push('styles')
    <!-- jsTree -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.3.12/themes/default/style.min.css" rel="stylesheet" />
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />

    <style>
        .jstree-anchor > .jstree-icon.fas {
            background: none !important;
        }

        .jstree-anchor > .fa-map-marker-alt::before {
            content: "\f3c5";
        }

        .jstree-anchor > .fa-folder-open::before {
            content: "\f07c";
        }

        .jstree-anchor > .fa-folder::before {
            content: "\f07b";
        }
    </style>
@endpush
@push('scripts')

    <!-- ✅ jQuery primero -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Luego jsTree y tu script personalizado -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.3.12/jstree.min.js"></script>


    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Espera un poco a que Livewire esté disponible
            setTimeout(() => {
                if (typeof Livewire === 'undefined') {
                    console.error('Livewire no está definido');
                    return;
                }

                function buildTree(data) {
                    $('#location-tree').jstree({
                        core: {
                            check_callback: true,
                            data: data
                        },
                        plugins: ["dnd", "state", "types"],
                        types: {
                            "default": {
                                "icon": "fas fa-map-marker-alt"
                            },
                            "folder": {
                                "icon": "fas fa-folder-open"
                            }
                        }
                    });
                }

                let data = @json($treeData);

                buildTree(data);

                $('#location-tree').on("move_node.jstree", function (e, data) {
                    let moved = [{
                        id: data.node.id,
                        parent_id: data.parent === '#' ? null : data.parent
                    }];
                    Livewire.dispatch('updateTreeOrder', moved);
                });

                Livewire.hook('message.processed', (message, component) => {
                    let data = @json($treeData);
                    $('#location-tree').jstree(true)?.destroy();
                    buildTree(data);
                });
            }, 300); // espera 300ms para asegurarse de que Livewire cargó
        });
    </script>
@endpush
