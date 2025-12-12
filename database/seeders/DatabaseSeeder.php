<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Plan;
use App\Models\ContactMessageTemplate;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $defaultPlan = Plan::firstOrCreate(
            ['name' => 'Padrão'],
            [
                'monthly_prospect_quota' => 500,
                'daily_prospect_quota' => 60,
                'price' => 0,
                'is_active' => true,
            ]
        );

        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'teste@teste.com',
            'password' => Hash::make('123456'),
        ]);

        if (!$user->plan_id) {
            $user->plan()->associate($defaultPlan);
            $user->save();
        }

        // Mensagens padrão de contato
        ContactMessageTemplate::firstOrCreate(
            ['key' => 'default_whatsapp_prospect'],
            [
                'channel' => 'whatsapp',
                'name' => 'Mensagem padrão WhatsApp (prospects)',
                'content' => 'Olá, tudo bem? Encontrei o contato da sua empresa e gostaria de falar com você sobre uma oportunidade para aumentar os resultados do seu negócio.',
                'is_active' => true,
            ]
        );

        ContactMessageTemplate::firstOrCreate(
            ['key' => 'default_email_prospect'],
            [
                'channel' => 'email',
                'name' => 'Mensagem padrão E-mail (prospects)',
                'content' => "Olá,\n\nEncontrei o contato da sua empresa e gostaria de compartilhar uma oportunidade que pode ajudar a alavancar seus resultados.\n\nPodemos conversar rapidamente esta semana?\n\nAtenciosamente,\n{{user_name}}",
                'is_active' => true,
            ]
        );

        // Seed Super Admin
        $this->call(SuperAdminSeeder::class);
        
        // Seed Sales Scripts
        $this->call(SalesScriptsSeeder::class);
    }
}
