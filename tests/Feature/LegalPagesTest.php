<?php

namespace Tests\Feature;

use Tests\TestCase;

class LegalPagesTest extends TestCase
{
    public function test_privacy_policy_is_public(): void
    {
        $this->get('/privacidade')->assertOk()->assertSee('Política de Privacidade')->assertSee('ramon@oakz.org');
    }

    public function test_terms_are_public(): void
    {
        $this->get('/termos')->assertOk()->assertSee('Termos de Serviço');
    }
}
