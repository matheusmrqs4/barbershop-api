<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class ValidateUserLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $validator = Validator::make(
            $request->all(),
            [
            'email' => 'required|email|email',
            'password' => 'required|min:6',
            ],
            [
            'required' => 'O campo :attribute é obrigatório.',

            'email.email' => 'O campo email deve ser um endereço de email válido.',
            ]
        );

        if ($validator->fails()) {
            return response()
                    ->json([
                        'errors' => $validator->errors()
                    ], 400);
        }

        return $next($request);
    }
}
