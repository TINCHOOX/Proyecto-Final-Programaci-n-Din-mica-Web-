<?php

namespace Database\Seeders;

use App\Models\Dinosaur;
use Illuminate\Database\Seeder;

class DinosaurSeeder extends Seeder
{
    public function run(): void
    {
        $dinosaurs = [
            ['explorador', 'Dino explorador', 0, 'dino-3.webp'],
            ['clasico', 'Dino clásico', 100, 'dino-1.webp'],
            ['destino', 'Dino destino', 150, 'dino-2.webp'],
            ['corredor', 'Dino corredor', 200, 'dino-5.webp'],
            ['con-estilo', 'Dino con estilo', 250, 'dino-4.webp'],
            ['ciclista', 'Dino ciclista', 300, 'dino-6.webp'],
            ['conductor', 'Dino conductor', 400, 'dino-7.webp'],
            ['conquistador', 'Dino conquistador', 500, 'dino-8.webp'],
            ['cafetero', 'Dino cafetero', 100, 'dino-cafetero.webp'],
            ['gourmet', 'Dino gourmet', 150, 'dino-gourmet.webp'],
            ['mochilero', 'Dino mochilero', 200, 'dino-mochilero.webp'],
            ['guia-urbano', 'Dino guía urbano', 200, 'dino-guia-urbano.webp'],
            ['ribereno', 'Dino ribereño', 250, 'dino-ribereno.webp'],
            ['montanista', 'Dino montañista', 300, 'dino-montanista.webp'],
            ['hotelero', 'Dino hotelero', 350, 'dino-hotelero.webp'],
            ['guardaparques', 'Dino guardaparques', 450, 'dino-guardaparques.webp'],
        ];

        foreach ($dinosaurs as $index => [$slug, $name, $price, $image]) {
            Dinosaur::updateOrCreate(['slug' => $slug], [
                'name' => $name,
                'price' => $price,
                'image_path' => $image === null ? null : 'images/dinomaps/'.$image,
                'is_starter' => $index === 0,
                'sort_order' => $index + 1,
            ]);
        }
    }
}
