<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class ValidateService
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
            'description' => 'required|min:10|max:255',
            'duration' => 'required|integer',
            'price' => 'required|integer',
            'barbers_id' => 'required'
            ],
            [
            'required' => 'O campo :attribute é obrigatório',
            'integer' => 'O campo :attribute deve ser um número inteiro',

            'description.min' => 'O campo :attribute deve conter pelo menos 10 caracteres',
            'description.max' => 'Esse :attribute ultrapassou o limite de caracteres',
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
