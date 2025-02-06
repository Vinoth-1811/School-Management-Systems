<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('icon')->nullable();
            $table->string('route')->nullable();
            $table->json('active_routes')->nullable();
            $table->string('pattern')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Insert data after creating the table
        $this->insertMenuData();
    }
    
    protected function insertMenuData()
    {
        // Insert the "Dashboard" menu
        $dashboardMenuId = DB::table('menus')->insertGetId([
            'title' => 'Dashboard',
            'icon'  => 'fas fa-tachometer-alt',
            'route' => null,
            'active_routes' => json_encode(['home','student/dashboard']),
            'pattern'   => null,
            'parent_id' => null,
            'order'     => 1,
            'is_active' => true,
        ]);

        // Insert submenu items under "Dashboard"
        DB::table('menus')->insert([
            [
                'title' => 'Admin Dashboard',
                'icon'  => null,
                'route' => 'home',
                'active_routes' => json_encode(['home']),
                'pattern'   => null,
                'parent_id' => $dashboardMenuId,
                'order'     => 1,
                'is_active' => true,
            ],
            [
                'title' => 'Student Dashboard',
                'icon'  => null,
                'route' => 'student/dashboard',
                'active_routes' => json_encode(['student/dashboard']),
                'pattern'   => null,
                'parent_id' => $dashboardMenuId,
                'order'     => 3,
                'is_active' => true,
            ],
        ]);

        // Insert the "User Management" menu
        $userManagementMenuId = DB::table('menus')->insertGetId([
            'title' => 'User Management',
            'icon'  => 'fas fa-shield-alt',
            'route' => null,
            'active_routes' => json_encode(['list/users']),
            'pattern'   => 'view/user/edit/*',
            'parent_id' => null,
            'order'     => 2,
            'is_active' => true,
        ]);

        // Insert submenu for "User Management"
        DB::table('menus')->insert([
            [
                'title' => 'List Users',
                'icon'  => null,
                'route' => 'list/users',
                'active_routes' => json_encode(['list/users']),
                'pattern'   => 'view/user/edit/*',
                'parent_id' => $userManagementMenuId,
                'order'     => 1,
                'is_active' => true,
            ],
        ]);


        // Insert the "Students" menu
        $studentMenuId = DB::table('menus')->insertGetId([
            'title' => 'Students',
            'icon'  => 'fas fa-graduation-cap',
            'route' => null,
            'active_routes' => json_encode(['student/list', 'student/grid', 'student/add/page']),
            'pattern'   => 'student/edit/*|student/profile/*',
            'parent_id' => null,
            'order'     => 4,
            'is_active' => true,
        ]);

        DB::table('menus')->insert([
            [
                'title' => 'Student List',
                'icon'  => null,
                'route' => 'student/list',
                'active_routes' => json_encode(['student/list', 'student/grid']),
                'pattern'   => null,
                'parent_id' => $studentMenuId,
                'order'     => 1,
                'is_active' => true,
            ],
            [
                'title' => 'Student Add',
                'icon'  => null,
                'route' => 'student/add/page',
                'active_routes' => json_encode(['student/add/page']),
                'pattern'   => null,
                'parent_id' => $studentMenuId,
                'order'     => 2,
                'is_active' => true,
            ],
            [
                'title' => 'Student Edit',
                'icon'  => null,
                'route' => null,
                'active_routes' => json_encode([]),
                'pattern'   => 'student/edit/*',
                'parent_id' => $studentMenuId,
                'order'     => 3,
                'is_active' => true,
            ],
        ]);

        // Insert the "Departments" menu
        $departmentMenuId = DB::table('menus')->insertGetId([
            'title' => 'Departments',
            'icon'  => 'fas fa-building',
            'route' => null,
            'active_routes' => json_encode(['department/list/page']),
            'pattern'   => null,
            'parent_id' => null,
            'order'     => 6,
            'is_active' => true,
        ]);

        DB::table('menus')->insert([
            [
                'title' => 'Department List',
                'icon'  => null,
                'route' => 'department/list/page',
                'active_routes' => json_encode(['department/list/page']),
                'pattern'   => null,
                'parent_id' => $departmentMenuId,
                'order'     => 1,
                'is_active' => true,
            ],
            [
                'title' => 'Department Add',
                'icon'  => null,
                'route' => 'department/add/page',
                'active_routes' => json_encode(['department/add/page']),
                'pattern'   => null,
                'parent_id' => $departmentMenuId,
                'order'     => 2,
                'is_active' => true,
            ],
        ]);

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};
