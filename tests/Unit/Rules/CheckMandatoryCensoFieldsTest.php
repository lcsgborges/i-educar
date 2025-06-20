<?php

namespace Tests\Unit\Rules;

use App\Rules\CheckMandatoryCensoFields;
use Tests\TestCase;
use Mockery;

class CheckMandatoryCensoFieldsTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_campo_estrutura_curricular_valida_preenchimento_tipode_atendimento()
    {
        $mandatoryFields = new CheckMandatoryCensoFields;

        $param = new \stdClass;
        $param->tipo_atendimento = '0';
        $param->estrutura_curricular = null;

        $result = $mandatoryFields->validaCampoEstruturaCurricular($param);

        $expectedMessage = 'Campo "Estrutura Curricular" é obrigatório quando o campo tipo de turma é "Curricular (etapa de ensino)".';

        $this->assertEquals($expectedMessage, $mandatoryFields->message());
        $this->assertFalse($result);
    }

    private function callProtectedValidaCampoLocalFuncionamentoDiferenciado($rule, $params)
    {
        $reflection = new \ReflectionClass($rule);
        $method = $reflection->getMethod('validaCampoLocalFuncionamentoDiferenciado');
        $method->setAccessible(true);
        return $method->invoke($rule, $params);
    }

    public function test_ct1_socioeducativo_sem_permissao()
    {
        $mockSchool = new \stdClass();
        $mockSchool->local_funcionamento = ['8', '10'];
        \Mockery::mock('alias:App\Models\LegacySchool')
            ->shouldReceive('find')
            ->with(123)
            ->andReturn($mockSchool);

        $rule = new \App\Rules\CheckMandatoryCensoFields();

        $params = new \stdClass();
        $params->ref_ref_cod_escola = 123;
        $params->local_funcionamento_diferenciado = 2; // Socioeducativo

        $result = $this->callProtectedValidaCampoLocalFuncionamentoDiferenciado($rule, $params);

        $this->assertFalse($result);
        $this->assertEquals(
            'Não é possível selecionar a opção: Unidade de atendimento socioeducativo quando o local de funcionamento da escola não for: Unidade de atendimento socioeducativo.',
            $rule->message()
        );
    }

    public function test_ct2_socioeducativo_com_permissao()
    {
        $mockSchool = new \stdClass();
        $mockSchool->local_funcionamento = '{9,11}';
        \Mockery::mock('alias:App\Models\LegacySchool')
            ->shouldReceive('find')
            ->with(123)
            ->andReturn($mockSchool);

        $rule = new \App\Rules\CheckMandatoryCensoFields();

        $params = new \stdClass();
        $params->ref_ref_cod_escola = 123;
        $params->local_funcionamento_diferenciado = 2; // Socioeducativo

        $result = $this->callProtectedValidaCampoLocalFuncionamentoDiferenciado($rule, $params);

        $this->assertTrue($result);
    }

    public function test_ct3_prisional_com_permissao()
    {
        $mockSchool = new \stdClass();
        $mockSchool->local_funcionamento = ['8', '10'];
        \Mockery::mock('alias:App\Models\LegacySchool')
            ->shouldReceive('find')
            ->with(123)
            ->andReturn($mockSchool);

        $rule = new \App\Rules\CheckMandatoryCensoFields();

        $params = new \stdClass();
        $params->ref_ref_cod_escola = 123;
        $params->local_funcionamento_diferenciado = 3; // Prisional

        $result = $this->callProtectedValidaCampoLocalFuncionamentoDiferenciado($rule, $params);

        $this->assertTrue($result);
    }

    public function test_ct4_prisional_sem_permissao()
    {
        $mockSchool = new \stdClass();
        $mockSchool->local_funcionamento = '{9,11}';
        \Mockery::mock('alias:App\Models\LegacySchool')
            ->shouldReceive('find')
            ->with(123)
            ->andReturn($mockSchool);

        $rule = new \App\Rules\CheckMandatoryCensoFields();

        $params = new \stdClass();
        $params->ref_ref_cod_escola = 123;
        $params->local_funcionamento_diferenciado = 3; // Prisional

        $result = $this->callProtectedValidaCampoLocalFuncionamentoDiferenciado($rule, $params);

        $this->assertFalse($result);
        $this->assertEquals(
            'Não é possível selecionar a opção: Unidade prisional quando o local de funcionamento da escola não for: Unidade prisional.',
            $rule->message()
        );
    }
}