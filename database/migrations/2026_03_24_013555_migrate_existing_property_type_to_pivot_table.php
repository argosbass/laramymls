<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        $rows = DB::table('properties')
            ->select('id', 'property_type_id')
            ->whereNotNull('property_type_id')
            ->get();

        foreach ($rows as $row) {
            DB::table('property_type_property')->updateOrInsert(
                [
                    'property_id' => $row->id,
                    'property_type_id' => $row->property_type_id,
                ],
                [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }

    public function down(): void
    {
        DB::table('property_type_property')->truncate();
    }
};
