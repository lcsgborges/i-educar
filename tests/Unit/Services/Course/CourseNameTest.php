<?php

namespace Tests\Unit\Services\Course;

use Tests\TestCase;
use App\Services\ValidateCourseName;

class ValidateCourseNameTest extends TestCase
{
    public function test_deve_rejeitar_nome_apenas_com_numeros()
    {
        $this->assertFalse(ValidateCourseName::validar('123456'));
    }

     public function test_deve_rejeitar_nome_com_simbolos()
    {
        $this->assertFalse(ValidateCourseName::validar('@@@!!'));
        $this->assertFalse(ValidateCourseName::validar('Ciência#1'));
        $this->assertFalse(ValidateCourseName::validar('História!'));
    }

     public function test_deve_rejeitar_nome_com_menos_de_3_caracteres()
    {
        $this->assertFalse(ValidateCourseName::validar('Ma')); // 2 caracteres
        $this->assertFalse(ValidateCourseName::validar('')); // vazio
        $this->assertFalse(ValidateCourseName::validar(' ')); // espaço só
    }

    public function test_deve_rejeitar_nome_com_mais_de_50_caracteres()
{
    $nomeMuitoLongo = str_repeat('a', 51); // 51 caracteres
    $this->assertFalse(ValidateCourseName::validar($nomeMuitoLongo));

    $nomeExatamente50 = str_repeat('a', 50);
    $this->assertTrue(ValidateCourseName::validar($nomeExatamente50)); // Deve aceitar
}
}
