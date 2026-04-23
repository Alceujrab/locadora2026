<?php

namespace App\Services;

/**
 * @deprecated Mantido apenas para compatibilidade. O backend agora usa WuzAPI.
 *
 * Todas as chamadas como app(EvolutionApiService::class) continuam funcionando
 * e delegam para WuzapiService, que implementa os endpoints do WuzAPI.
 */
class EvolutionApiService extends WuzapiService
{
}
