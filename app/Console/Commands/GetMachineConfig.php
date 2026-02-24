<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GetMachineConfig extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'machine:config {id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get machine configuration by ID';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $id = $this->argument('id');
        $sheet = new \App\Exports\MissingItemsDetailSheet($id, 'Test');
        $data = $sheet->collection();
        foreach ($data->take(20) as $row) {
            $this->info(json_encode($row));
        }
        return 0;
    }
}
