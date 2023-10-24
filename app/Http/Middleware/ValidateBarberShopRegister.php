<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class ValidateBarberShopRegister
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
            'address1' => 'min:2|max:255',
            'address2' => 'min:2|max:255',
            'address3' => 'min:2|max:255',
            'bio' => 'min:10|max:255',
            ],
            [
            'required' => 'O campo :attribute é obrigatório.',
            'max' => 'O campo :attribute não pode ter mais de 255 caracteres.',
            'min' => 'O campo :attribute deve conter pelo menos 2 caracteres.',

            'email.email' => 'O campo email deve ser um endereço de email válido.',
            'email.unique' => 'Este email já está sendo usado por outro usuário.',
            'password.min' => 'O campo senha deve ter pelo menos 6 caracteres.',
            'bio.min' => 'O campo bio deve conter pelo menos 10 caracteres.',
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
