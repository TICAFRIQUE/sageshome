<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ResidenceType;
use App\Models\Residence;

class CheckResidenceData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'residence:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check residence and residence type data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== Types de résidences ===');
        
        foreach (ResidenceType::all() as $type) {
            $count = $type->residences()->count();
            $this->line($type->id . ' - ' . $type->name . ' (' . $count . ' résidences)');
        }

        $this->info("\n=== Résidences avec leur type ===");
        
        foreach (Residence::with('residenceType')->get() as $residence) {
            $typeName = $residence->residenceType ? $residence->residenceType->name : 'N/A';
            $this->line($residence->name . ' - Type: ' . $typeName);
        }

        $this->info("\n=== Statistiques ===");
        $this->line('Total des types de résidences: ' . ResidenceType::count());
        $this->line('Total des résidences: ' . Residence::count());
        
        return 0;
    }
}
