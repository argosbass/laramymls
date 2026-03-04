<x-filament::page>
    <div wire:ignore>
        <div id="location-tree" style="min-height: 400px;"></div>
    </div>
</x-filament::page>

@push('styles')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.3.12/themes/default/style.min.css" rel="stylesheet" />
@endpush

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.3.12/jstree.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // render inicial
            initTree(@json($treeData));

            // cuando backend confirme y mande data fresca
            Livewire.on('tree-synced', (payload) => {
                const fresh = payload?.treeData ?? [];
                const inst = $('#location-tree').jstree(true);
                inst?.destroy();
                initTree(fresh);
            });

            function initTree(data) {


                    $('#location-tree').jstree(

                        {
                        core: {
                            check_callback: true,
                            data: data
                        },
                        plugins: ['dnd','types'],
                            types: {
                                root: {
                                    icon: "fas fa-globe"
                                },
                                default: {
                                    icon: "fas fa-map-marker-alt"
                                }
                            }
                    })
                        .on('ready.jstree', function () {
                            $(this).jstree('open_all'); // ✅ abre todo el árbol
                        });



                // ✅ move_node cubre reorder dentro del mismo parent y mover entre parents
                $('#location-tree').off('move_node.jstree').on('move_node.jstree', function (e, data) {
                    const inst = data.instance;

                    const newParentId = data.parent === '#' ? null : data.parent;
                    const oldParentId = data.old_parent === '#' ? null : data.old_parent;

                    // Orden final del NUEVO parent
                    const newChildren = inst.get_node(data.parent).children;

                    Livewire.dispatch('persistOrder', {
                        payload: {
                            parent_id: newParentId,
                            children: newChildren,
                        }
                    });

                    // Si cambió de parent, también actualiza el ORDEN del parent anterior
                    if (data.parent !== data.old_parent) {
                        const oldChildren = inst.get_node(data.old_parent).children;

                        Livewire.dispatch('persistOrder', {
                            payload: {
                                parent_id: newParentId,
                                children: newChildren,
                            }
                        });

                    }
                });
            }
        });
    </script>
@endpush
