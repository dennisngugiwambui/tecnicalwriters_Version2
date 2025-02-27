<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\User;
use App\Models\File;

class OrderSeeder extends Seeder
{
    public function run()
    {
        // Find a client user or create one
        $client = User::where('usertype', 'client')->first();
        if (!$client) {
            $client = User::create([
                'name' => 'John Client',
                'email' => 'client@example.com',
                'phone' => '1234567890',
                'usertype' => 'client',
                'status' => 'active',
                'password' => bcrypt('password'),
            ]);
        }

        // Create dummy order
        $order = Order::create([
            'title' => 'Research Paper on Renewable Energy',
            'instructions' => 'Write a 10-page research paper on renewable energy sources. Include at least 5 academic sources. The paper should cover solar, wind, and hydroelectric energy. Format in APA style with double spacing.',
            'price' => 120.50,
            'deadline' => now()->addDays(5),
            'task_size' => '10 pages',
            'type_of_service' => 'Writing',
            'discipline' => 'Environmental Science',
            'software' => null,
            'status' => 'available',
            'client_id' => $client->id,
            'customer_comments' => 'I need this to be high quality work. Will tip for excellent citations.'
        ]);

        // Create a dummy file
        $file = new File([
            'name' => 'instructions.pdf',
            'path' => 'orders/dummy/instructions.pdf',
            'size' => 1024, // 1 KB
            'uploaded_by' => $client->id,
        ]);

        // Attach the file to the order
        $order->files()->save($file);
        
        $this->command->info('Dummy order created successfully.');
    }
}