---
name: correcao-erros
description: "Use quando houver erro de cadastro, erro 500, QueryException SQLSTATE, falhas de validacao, campos null inesperados em Laravel/Filament/Livewire. Inclui fluxo de diagnostico, correcoes seguras e validacao final."
---

# Skill: Correcao de Erros (Laravel/Filament)

## Objetivo
Corrigir erros de execucao com foco em causa raiz e evitar regressao, especialmente em CRUDs.

## Quando usar
- Internal Server Error (500)
- Illuminate\\Database\\QueryException
- SQLSTATE 23000 (integridade/NOT NULL/FK)
- Erro ao cadastrar/editar registros no painel
- Campos que chegam null no banco sem poder

## Fluxo obrigatorio
1. Reproduzir e capturar contexto
- Identificar excecao, SQL, coluna e tabela afetada.
- Registrar rota/tela/acao do usuario.

2. Mapear cadeia de dados
- Formulario (Filament/Livewire): campo existe? required? default?
- Validacao (FormRequest/rules): permite null indevido?
- Persistencia (create/fill/update): campo foi mapeado?
- Model: fillable/casts/attributes coerentes?
- Banco: coluna NOT NULL/default/fk condiz com regra de negocio?

3. Corrigir em camadas (defesa em profundidade)
- Ajustar formulario para enviar valor valido.
- Adicionar fallback seguro no Model (protected $attributes) quando cabivel.
- Ajustar validacao para refletir restricao real do banco.
- Evitar corrigir apenas no banco quando a origem do bug esta na aplicacao.

4. Validar
- Reexecutar fluxo que falhava.
- Rodar testes relacionados (ou criar teste de regressao se faltante).
- Verificar se nao houve impacto colateral.

5. Entregar
- Explicar causa raiz em 1-2 linhas.
- Listar arquivos alterados e por que.
- Informar como validar em homologacao/producao.

## Checklist rapido para SQLSTATE 23000
- Coluna NOT NULL recebeu null por falta de default no form.
- Campo ausente em $fillable bloqueando atribuicao.
- Mutator/cast sobrescrevendo valor para null.
- Relacao/FK sem registro correspondente.
- Migration divergente entre ambientes.

## Padrao de patch recomendado
- Preferir correcao minima e objetiva.
- Nao reformatar arquivo inteiro.
- Se for dado critico de contrato/financeiro, incluir teste de regressao.

## Exemplo pratico (caso comum)
Problema: coluna pickup_mileage NOT NULL recebendo null no insert de contracts.

Correcao sugerida:
- ContractResource form: pickup_mileage com required + default(0)
- Contract model: protected $attributes com pickup_mileage => 0

Resultado esperado:
- Cadastro nao quebra mesmo quando usuario nao preencher KM manualmente.
- Regra de banco e aplicacao ficam consistentes.
