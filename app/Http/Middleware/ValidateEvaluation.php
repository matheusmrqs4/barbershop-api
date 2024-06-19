<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class ValidateEvaluation
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
                'comment' => 'required|max:255',
                'grade' => 'required|integer|min:1|max:5',
            ],
            [
                'required' => 'O campo :attribute é obrigatório.',
                'integer' => 'O campo :attribute deve ser um número inteiro.',
                'min' => 'O campo :attribute deve ser no mínimo :min.',

                'comment.max' => 'O campo :attribute não pode ter mais de 255 caracteres.',
                'grade.max' => 'O campo :attribute deve ser no máximo :max.',
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
