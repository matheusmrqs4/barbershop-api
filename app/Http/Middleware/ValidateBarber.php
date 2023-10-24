<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class ValidateBarber
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
            'name' => 'required|unique:barbers|min:3|max:100',
            'description' => 'required|min:10|max:255',
            'start_time' => 'required|date_format:H:i:s',
            'end_time' => 'required|date_format:H:i:s',
            ],
            [
            'required' => 'O campo :attribute é obrigatório',
            'max' => 'Esse :attribute ultrapassou o limite de caracteres',
            'date_format' => 'O campo :attribute deve estar no formato H:i:s',

            'name.unique' => 'Esse :attribute já existe',
            'name.min' => 'O campo :attribute deve conter pelo menos 3 caracteres',
            'description.min' => 'O campo :attribute deve conter pelo menos 10 caracteres',
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
