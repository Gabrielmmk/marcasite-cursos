<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        $courses = [
            [
                'name'        => 'Excel do Básico ao Avançado',
                'description' => 'Domine planilhas, fórmulas avançadas, tabelas dinâmicas e dashboards profissionais.',
                'price'       => 197.00,
                'duration'    => '40 horas',
                'active'      => true,
            ],
            [
                'name'        => 'Power BI na Prática',
                'description' => 'Aprenda a criar relatórios e dashboards interativos do zero usando Power BI.',
                'price'       => 297.00,
                'duration'    => '30 horas',
                'active'      => true,
            ],
            [
                'name'        => 'Word Profissional',
                'description' => 'Formatação avançada, mala direta, índices automáticos e relatórios corporativos.',
                'price'       => 147.00,
                'duration'    => '20 horas',
                'active'      => true,
            ],
            [
                'name'        => 'Pacote Office Completo',
                'description' => 'Word, Excel, PowerPoint e Outlook em um único treinamento intensivo.',
                'price'       => 497.00,
                'duration'    => '80 horas',
                'active'      => true,
            ],
        ];

        foreach ($courses as $course) {
            \App\Models\Course::create($course);
        }
    }
}
