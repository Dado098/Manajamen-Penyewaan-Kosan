<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Location;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Location::create([
            'name' => 'Kos Ibu Senang',
            'address' => 'Jl. XYZ No.10, Bogor', // Sesuaikan alamat
            'latitude' => -6.595231878326835,
            'longitude' => 106.80808580678847
        ]);

    }
}
