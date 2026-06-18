<?php

namespace Tests\Feature;

use App\Filament\Exports\LeadExporter;
use App\Models\User;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeadExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_export_table_is_available_and_lead_export_runs_synchronously(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $export = Export::create([
            'user_id' => $admin->id,
            'file_disk' => 'local',
            'file_name' => 'test.csv',
            'exporter' => LeadExporter::class,
            'total_rows' => 0,
        ]);

        $this->assertDatabaseHas('exports', [
            'id' => $export->id,
            'exporter' => LeadExporter::class,
        ]);

        $exporter = new LeadExporter($export, [], []);

        $this->assertSame('sync', $exporter->getJobConnection());
    }
}
