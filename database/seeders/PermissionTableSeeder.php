<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [

            'products',
            'services',
             'roles',
            'orders',
            'reviews',
            'ORDER MANAGEMENT',
            'PRODUCT MANAGEMENT',
            'Refund request',
            'Messages',
            'My shop',
            'My bank Info',
            'BUSINESS SECTION'
    ];
    
    
    
    foreach ($permissions as $permission) {
    
    Permission::create(['name' => $permission]);
    }
    
    
    }
    }
    
