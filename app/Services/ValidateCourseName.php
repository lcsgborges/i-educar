<?php

namespace App\Services;

class ValidateCourseName
{
        public static function validar(string $nome): bool
    {
        $nome = trim($nome);

        if (empty($nome)) {
            return false;
        }

        // Rejeita se só tiver números
        if (preg_match('/^\d+$/', $nome)) {
            return false;
        }

        // Rejeita se tiver símbolos inválidos
        if (!preg_match('/^[a-zA-ZÀ-ÿ0-9 \-\'"]+$/u', $nome)) {
            return false;
        }

        // Rejeita nomes menores que 3 ou maiores que 50 caracteres
        $length = mb_strlen($nome);
        if ($length < 3 || $length > 50) {
            return false;
        }

        return true;
    }
}
