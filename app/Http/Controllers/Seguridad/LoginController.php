<?php

namespace App\Http\Controllers\Seguridad;
use Illuminate\Notifications\Notifiable;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin\Empleado;
use App\Models\Admin\Empresa;
use App\Models\Admin\Rol;
use Illuminate\Support\Facades\DB;

class LoginController extends Controller
{
    //use Notifiable;
    use AuthenticatesUsers;
    
    protected $redirectTo = '/tablero';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

     
    public function index()
    {
            
           return view('seguridad.index');
    }

    
    
    protected function authenticated(Request $request, $user)
    {   

        $useractivo = $user->where([
            ['usuario', '=', $request->usuario],
            ['activo', '=', 1]
        
        ])->count();
      

        $roles1 = $user->roles1()->get();
       
        if ($roles1->isNotEmpty() && $useractivo >= 1) {
            $user->setSession();
        }else{
            $this->guard()->logout();
            $request->session()->invalidate();
            return redirect('seguridad/login')->withErrors(['error'=>'Este usuario no esta activo y no tiene rol ']);
        }
    }

    public function username()
    {
        return 'usuario';
    }
    public function loginMovil(Request $request)
    {   
        
         if(Auth::attempt($request->only('usuario','password'))){

            $user = Auth::user();
            
           $userlogin = DB::table('usuario')->Join('usuario_rol', 'usuario.id', '=', 'usuario_rol.usuario_id')
           ->Join('empleado', 'usuario.empleado_id', '=', 'empleado.ide')
           ->where('usuario.usuario', '=', $user->usuario)->get();
            
            
                
            $useractivo = $user->where([
            ['usuario', '=', $request->usuario],
            ['activo', '=', 1]
        
            ])->count();
            
            
            
            if($useractivo >= 1){  
            
            return Response()->json([
            'user' => $userlogin
        ], 200);

            } else {
                
                if($useractivo == 0)
                return response()->json(['error'=> 'Usuario creado sin activación'], 401);
            }

         }else{
             return response()->json(['error'=> 'Unauthorised'], 400);
             
         }

        

    }


}
