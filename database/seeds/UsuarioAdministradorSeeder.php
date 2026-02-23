<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsuarioAdministradorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('empresa')->insert([

            'nombre'=>'AQUAPROGRAMMER', 
            'tipo_documento'=>'NIT', 
            'documento'=>1130629762,
            'activo'=>'1',
                       

        ]);
        
        DB::table('empleado')->insert([

            'nombres'=>'Jhonnathan', 
            'apellidos'=>'Castro Galeano',
            'tipo_documento'=>'CC',
            'documento'=>1130629762,
            'pais'=>'Colombia', 
            'ciudad'=>'Cali', 
            'barrio'=>'Marroquin 1', 
            'direccion'=>'Cra 26', 
            'celular'=>'3175018125', 
            'telefono'=>'5243909', 
            'activo'=>'1',
            'empresa_id'=>1
            

        ]);

        DB::table('empleado')->insert([

            
            'nombres'=>'Juan Diego', 
            'apellidos'=>'Castro',
            'tipo_documento'=>'CC',
            'documento'=>123456789,
            'pais'=>'Colombia', 
            'ciudad'=>'Cali', 
            'barrio'=>'Calibella', 
            'direccion'=>'Cra 7', 
            'celular'=>'3001234567', 
            'telefono'=>'5243900', 
            'activo'=>'1',
            'empresa_id'=>1
            

        ]);
        
        DB::table('usuario')->insert([

            'usuario'=>'jcastro', 
            'password'=>bcrypt('123456'),
            'remenber_token'=>bcrypt('123456'),
            'tipo_de_usuario'=>'office', 
            'email'=>'castrokof@gmail.com',
            'activo'=>1,
            'empleado_id'=>1
            

        ]);
        
        DB::table('usuario')->insert([

            'usuario'=>'jdcastro', 
            'password'=>bcrypt('123456'),
            'remenber_token'=>bcrypt('123456'),
            'tipo_de_usuario'=>'office', 
            'email'=>'jdcastro@gmail.com',
            'activo'=>1,
            'empleado_id'=>2

        ]);

        DB::table('usuario_rol')->insert([

            'rol_id'=>1, 
            'usuario_id'=>1, 
             
                      

        ]);
        DB::table('usuario_rol')->insert([

            'rol_id'=>1, 
            'usuario_id'=>2, 
             
                      

        ]);
    
        //Creación de menu

        DB::table('menu')->insert([

            'menu_id'=> 0, 
            'nombre'=>'Admin',
            'url'=>'#',
            'orden'=>1, 
            'icono'=>'far fa-building'
        ]);
        DB::table('menu')->insert([

            'menu_id'=> 1, 
            'nombre'=>'Lista de Menus',
            'url'=>'admin/menu',
            'orden'=>1, 
            'icono'=>'fa fa-cog fa-spin fa-3x fa-fw'
        ]);
        DB::table('menu')->insert([

            'menu_id'=> 1, 
            'nombre'=>'Crear_menu',
            'url'=>'admin/menu/crear',
            'orden'=>2, 
            'icono'=>'fas fa-clipboard-list'
        ]);
        DB::table('menu')->insert([

            'menu_id'=> 1, 
            'nombre'=>'Roles',
            'url'=>'admin/rol',
            'orden'=>3, 
            'icono'=>'fa fa-list'
        ]);
        DB::table('menu')->insert([

            'menu_id'=> 1, 
            'nombre'=>'Asignar Menus',
            'url'=>'admin/menu-rol',
            'orden'=>4, 
            'icono'=>'fa fa-tasks'
        ]);
        DB::table('menu')->insert([

            'menu_id'=> 0, 
            'nombre'=>'Usuario',
            'url'=>'#',
            'orden'=>4, 
            'icono'=>'fa fa-users'
        ]);
        DB::table('menu')->insert([

            'menu_id'=> 6, 
            'nombre'=>'Listar usuarios',
            'url'=>'usuario',
            'orden'=>1, 
            'icono'=>'fa fa-user'
        ]);
        DB::table('menu')->insert([

            'menu_id'=> 1, 
            'nombre'=>'Empresa',
            'url'=>'admin/empresa',
            'orden'=>5, 
            'icono'=>'fas fa-industry'
        ]);
       
        DB::table('menu')->insert([

            'menu_id'=> 0, 
            'nombre'=>'Empleado',
            'url'=>'empleado',
            'orden'=>3, 
            'icono'=>'fas fa-motorcycle'
        ]);
        DB::table('menu')->insert([

            'menu_id'=> 15, 
            'nombre'=>'Cliente',
            'url'=>'cliente',
            'orden'=>1, 
            'icono'=>'fas fa-handshake'
        ]);
        DB::table('menu')->insert([

            'menu_id'=> 15, 
            'nombre'=>'Prestamos',
            'url'=>'prestamo',
            'orden'=>3, 
            'icono'=>'fas fa-money-bill-alt'
        ]);
        DB::table('menu')->insert([

            'menu_id'=> 0, 
            'nombre'=>'Tablero de control',
            'url'=>'tablero',
            'orden'=>2, 
            'icono'=>'fas fa-tachometer-alt'
        ]);
        DB::table('menu')->insert([

            'menu_id'=> 15, 
            'nombre'=>'Ordenar ruta',
            'url'=>'cliente/ruta',
            'orden'=>2, 
            'icono'=>'fas fa-route'
        ]);
        DB::table('menu')->insert([

            'menu_id'=> 0, 
            'nombre'=>'Admin-Clientes',
            'url'=>'#',
            'orden'=>5, 
            'icono'=>'fa fa-cog fa-spin fa-3x fa-fw'
        ]);
        DB::table('menu')->insert([

            'menu_id'=> 15, 
            'nombre'=>'Ruta de cobro',
            'url'=>'pago',
            'orden'=>4, 
            'icono'=>'fas fa-cash-register'
        ]);

        //Relación menu-rol

        DB::table('menu_rol')->insert([

            'rol_id'=> 1, 
            'menu_id'=> 1
        ]);
        DB::table('menu_rol')->insert([

            'rol_id'=> 1, 
            'menu_id'=> 2
        ]);
        DB::table('menu_rol')->insert([

            'rol_id'=> 1, 
            'menu_id'=> 3
        ]);
        DB::table('menu_rol')->insert([

            'rol_id'=> 1, 
            'menu_id'=> 4
        ]);
        DB::table('menu_rol')->insert([

            'rol_id'=> 1, 
            'menu_id'=> 5
        ]);
        DB::table('menu_rol')->insert([

            'rol_id'=> 1, 
            'menu_id'=> 6
        ]);
        DB::table('menu_rol')->insert([

            'rol_id'=> 1, 
            'menu_id'=> 7
        ]);
        DB::table('menu_rol')->insert([

            'rol_id'=> 1, 
            'menu_id'=> 8
        ]);
        
        DB::table('menu_rol')->insert([

            'rol_id'=> 1, 
            'menu_id'=> 10
        ]);
       
        DB::table('menu_rol')->insert([

            'rol_id'=> 2, 
            'menu_id'=> 6
        ]);
        DB::table('menu_rol')->insert([

            'rol_id'=> 2, 
            'menu_id'=> 7
        ]);
        DB::table('menu_rol')->insert([

            'rol_id'=> 2, 
            'menu_id'=> 10
        ]);
       
        DB::table('menu_rol')->insert([

            'rol_id'=> 1, 
            'menu_id'=> 11
        ]);
        DB::table('menu_rol')->insert([

            'rol_id'=> 1, 
            'menu_id'=> 12
        ]);
        DB::table('menu_rol')->insert([

            'rol_id'=> 2, 
            'menu_id'=> 11
        ]);
        DB::table('menu_rol')->insert([

            'rol_id'=> 3, 
            'menu_id'=> 11
        ]);
        DB::table('menu_rol')->insert([

            'rol_id'=> 2, 
            'menu_id'=> 12
        ]);
        DB::table('menu_rol')->insert([

            'rol_id'=> 1, 
            'menu_id'=> 13
        ]);
        DB::table('menu_rol')->insert([

            'rol_id'=> 1, 
            'menu_id'=> 14
        ]);
        DB::table('menu_rol')->insert([

            'rol_id'=> 1, 
            'menu_id'=> 15
        ]);
        DB::table('menu_rol')->insert([

            'rol_id'=> 1, 
            'menu_id'=> 16
        ]);
        DB::table('menu_rol')->insert([

            'rol_id'=> 2, 
            'menu_id'=> 15
        ]);
        DB::table('menu_rol')->insert([

            'rol_id'=> 2, 
            'menu_id'=> 14
        ]);
        DB::table('menu_rol')->insert([

            'rol_id'=> 2, 
            'menu_id'=> 16
        ]);
        DB::table('menu_rol')->insert([

            'rol_id'=> 3, 
            'menu_id'=> 15
        ]);
        
    }
}
