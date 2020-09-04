<?php
 
namespace App\Http\Middleware;
 
use Closure;
 
class Cors
{
    public function handle($request, Closure $next) {
        header('Access-Control-Allow-Origin:  *');
        header('Access-Control-Allow-Headers:  Content-Type, X-Auth-Token, Authorization, Origin, X-API-KEY , X-Requested-With, Accept , Access-Control-Request-Method');
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        header("Allow: GET, POST, OPTIONS, PUT, DELETE");
        $method = $_SERVER['REQUEST_METHOD'];
        if($method == "OPTIONS") {
            die();
        }
        return $next($request);
    }
}