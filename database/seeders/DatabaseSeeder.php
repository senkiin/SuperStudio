<?php

namespace Database\Seeders;

use App\Models\Album;
use App\Models\Appointment;
use App\Models\Offer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Photo;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ServiceType;
use App\Models\User;
use App\Models\Video;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role as SpatieRole;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {


        // --------------------------------------------------------------------
        // 2. Crear Usuarios (Admin y Clientes)
        // --------------------------------------------------------------------
        // Crear un usuario Administrador específico
        $adminUser = User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com', // Email fijo para login fácil
            // 'password' => bcrypt('password'), // La factory usualmente ya hashea
        ]);
        $roleClient = User::factory()->create([
            'name' => 'Client',
            'email' => 'client@example.com',
            'role' => 'admin' // Email fijo para login fácil
            // 'password' => bcrypt('password'), // La factory usualmente ya hashea
        ]);

        // Crear varios usuarios Clientes
        $clientUsers = User::factory(10)->create();


        // --------------------------------------------------------------------
        // 3. Crear Datos Maestros (sin dependencias complejas)
        // --------------------------------------------------------------------
        $offers = Offer::factory(5)->create(); // Crear 5 ofertas
        $serviceTypes = ServiceType::factory(4)->create(); // Crear 4 tipos de servicio
        Storage::deleteDirectory('images/products');
        Storage::makeDirectory('images/products');
        $productCategories = ProductCategory::factory(5)->create(); // Crear 5 categorías de producto

        // --------------------------------------------------------------------
        // 4. Crear Álbumes (asociados al Admin)
        // --------------------------------------------------------------------
        Storage::deleteDirectory('images/albums');
        Storage::makeDirectory('images/albums');
        $albums = Album::factory(8)->create([
        ]);

        // --------------------------------------------------------------------
        // 5. Crear Fotos y Videos (asociados a los Álbumes creados)
        // --------------------------------------------------------------------
        Storage::deleteDirectory('images/photos');
        Storage::makeDirectory('images/photos');
        Storage::deleteDirectory('videos');
        Storage::makeDirectory('videos');
        foreach ($albums as $album) {
            // Crear entre 5 y 15 fotos por álbum

            Photo::factory(rand(5, 15))->create([
            ]);
            // Crear entre 0 y 3 videos por álbum

            Video::factory(rand(0, 3))->create([
            ]);
        }

        // --------------------------------------------------------------------
        // 6. Crear Productos (asociados a Categorías existentes)
        // --------------------------------------------------------------------
        Storage::deleteDirectory('images/products');
        Storage::makeDirectory('images/products');
        $products = Product::factory(20)->create();


        // --------------------------------------------------------------------
        // 7. Crear Pedidos (Orders) y sus Items (OrderItems)
        // --------------------------------------------------------------------
        if ($clientUsers->isNotEmpty() && $products->isNotEmpty()) {
            foreach ($clientUsers as $client) {
                // Crear entre 0 y 4 pedidos por cliente
                Order::factory(rand(0, 4))
                    ->for($client) // Asocia el pedido al cliente
                    ->has(OrderItem::factory()->count(rand(1, 5)) // Cada pedido tiene entre 1 y 5 items
                        ->state(function (array $attributes, Order $order) use ($products) {
                             // Asigna un producto aleatorio existente a cada item
                             $product = $products->random();
                             return [
                                'product_id' => $product->id,
                                // Usa el nombre de columna correcto (ej: unit_price)
                                'unit_price' => $product->price, // Asumiendo que la columna en products es 'price'
                                // Opcional: 'product_name' si realmente necesitas guardar el nombre aquí
                                // y si la columna existe en la tabla order_items.
                                // 'product_name' => $product->name, // Asumiendo que la columna en products es 'name'

                                // ¡Elimina las claves con nombres incorrectos como 'precio_unitario' y 'nombre_producto'!
                               ];
                        })
                    )
                    ->create(); // Crea la orden y sus items asociados
                    // Nota: El total del pedido ('total' en tabla Orders) habría que calcularlo
                    // después de crear los items, quizás con un state en OrderFactory o un Observer.
            }
        }

        // --------------------------------------------------------------------
        // 8. Crear Citas (Appointments)
        // --------------------------------------------------------------------
         if ($clientUsers->isNotEmpty() && $serviceTypes->isNotEmpty()) {
            foreach ($clientUsers as $client) {
                // Crear entre 0 y 2 citas por cliente
                Appointment::factory(rand(0, 2))
                    ->for($client) // Asocia la cita al cliente
                    ->state(function (array $attributes) use ($serviceTypes) {
                         // Asigna un tipo de servicio aleatorio existente
                         return ['service_type_id' => $serviceTypes->random()->id];
                     })
                    ->create();
            }
         }

        // --------------------------------------------------------------------
        // 9. Poblar Tabla Pivot user_offers (¡Importante!)
        // --------------------------------------------------------------------
        // Obtenemos todos los usuarios y ofertas de nuevo (por si acaso)
        $allUsers = User::all();
        $allOffers = Offer::all();

        if ($allUsers->isNotEmpty() && $allOffers->isNotEmpty()) {
            foreach($allUsers as $user) {
                // A cada usuario le asignamos entre 0 y 2 ofertas aleatorias
                // Asegurándonos de no pedir más ofertas de las que existen
                $numberOfOffers = rand(0, min(2, $allOffers->count()));
                if ($numberOfOffers > 0) {
                    $offersToAttach = $allOffers->random($numberOfOffers)->pluck('id');

                    // Usamos attach() en la relación definida en el modelo User
                    $user->offers()->attach($offersToAttach, [
                        'received_at' => now(),
                        // 'accepted_at' => null // O alguna lógica para simular aceptación
                    ]);
                }
            }
        }


    }
}
