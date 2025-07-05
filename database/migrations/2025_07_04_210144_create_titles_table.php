<?php

use App\Models\Title;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('titles', function (Blueprint $table) {
            $table->id();
            $table->string('display_title');
            $table->string('csv_title');
            $table->timestamps();
        });

        DB::table('titles')->insert([
            ['display_title' => 'Mr', 'csv_title' => 'mr'],
            ['display_title' => 'Mr', 'csv_title' => 'mister'],
            ['display_title' => 'Mrs', 'csv_title' => 'mrs'],
            ['display_title' => 'Miss', 'csv_title' => 'miss'],
            ['display_title' => 'Ms', 'csv_title' => 'ms'],
            ['display_title' => 'Dr', 'csv_title' => 'dr'],
            ['display_title' => 'Prof', 'csv_title' => 'prof'],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('titles');
    }
};
