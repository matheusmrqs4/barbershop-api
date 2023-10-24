<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class ValidateUserRegister
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
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            ],
            [
            'required' => 'O campo :attribute é obrigatório.',

            'name.max' => 'O campo nome não pode ter mais de 255 caracteres.',
            'email.email' => 'O campo email deve ser um endereço de email válido.',
            'email.unique' => 'Este email já está sendo usado por outro usuário.',
            'password.min' => 'O campo senha deve ter pelo menos 6 caracteres.',
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
