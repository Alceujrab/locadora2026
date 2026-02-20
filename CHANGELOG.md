# CHANGELOG — Locadora 2026

Registro de todos os movimentos e passos do sistema.

---

## [2026-02-20] FASE 1.1 — Fundação do Projeto

### Criação do Projeto

- **09:59** — Projeto Laravel 12 (v12.11.2) criado via `composer create-project`
- **10:05** — Pacotes Composer instalados:
  - `moonshine/moonshine` v4.8 (Admin Panel)
  - `laravel/sanctum` v4.3 (Auth SPA)
  - `spatie/laravel-permission` v7.2 (Roles/Permissions)
  - `laravel/horizon` v5.44 (Queue Dashboard)
  - `laravel/telescope` v5.17 (Debug Dev)
  - `barryvdh/laravel-dompdf` v3.1 (PDF Generation)
  - `inertiajs/inertia-laravel` v2.0 (SPA Bridge)
  - `tightenco/ziggy` v2.6 (JS Routes)
  - `spatie/laravel-activitylog` v4.11 (Audit Logs)
  - `artesaos/seotools` v1.3 (SEO)
  - `spatie/laravel-medialibrary` v11.20 (Media/Upload)
  - `spatie/laravel-backup` v10.0 (Backups)
- **10:10** — NPM packages instalados: Vue 3, @inertiajs/vue3, @vitejs/plugin-vue, Bootstrap 5, Chart.js, flatpickr, sass (212 packages)
- **10:15** — MoonShine v4.8 instalado:
  - Autenticação habilitada
  - Notificações habilitadas
  - Palette: Purple
  - Super usuário: <admin@locadora2026.com.br>
  - Migrations do MoonShine executadas
- **10:19** — `.env` configurado:
  - Banco de dados: **MySQL** (alterado de PostgreSQL a pedido do usuário)
  - Cache: Redis
  - Filas: Redis
  - Locale: pt_BR
  - Placeholders para Mercado Pago e Evolution API

### Arquivos Criados

- `.env` — Configuração do ambiente
- `CHANGELOG.md` — Este arquivo de registro
- `app/MoonShine/Pages/Dashboard.php` — Dashboard MoonShine (auto-gerado)

### Decisões

- MySQL escolhido em vez de PostgreSQL (solicitação do usuário)
- MoonShine v4.8 para admin panel (v3 não compatível com Laravel 12)
- Purple palette para o tema do admin

---

## [2026-02-20] FASE 1.2 — Migrations e Models

### Enums Criados (8)

- `VehicleStatus` — disponivel, locado, reservado, manutencao, inativo
- `CustomerType` — pf, pj
- `ReservationStatus` — pendente, confirmada, em_andamento, concluida, cancelada, no_show
- `ContractStatus` — rascunho, aguardando_assinatura, ativo, encerrado, cancelado, suspenso
- `InvoiceStatus` — aberta, paga, vencida, cancelada, estornada
- `PaymentMethod` — pix, cartao_credito, cartao_debito, boleto, dinheiro, transferencia
- `InspectionType` — saida, retorno
- `ServiceOrderStatus` — aberta, em_andamento, aguardando_pecas, concluida, cancelada

### Migrations Criadas (17 arquivos, 28+ tabelas)

1. `create_branches_table` — Filiais
2. `create_settings_table` — Configurações key-value por branch
3. `modify_users_table` — +branch_id, phone, whatsapp, avatar, is_active, soft deletes
4. `create_vehicle_categories_table` — Categorias com preços e km
5. `create_vehicles_table` — Veículos com override de preços
6. `create_vehicle_related_tables` — Fotos, documentos, acessórios
7. `create_customers_table` — Clientes UUID, PF/PJ, CNH
8. `create_customer_related_tables` — Documentos e motoristas adicionais
9. `create_suppliers_table` — Fornecedores
10. `create_rental_extras_table` — Extras de locação
11. `create_reservations_table` — Reservas + extras
12. `create_contracts_tables` — Templates, contratos UUID, extras, logs
13. `create_inspections_tables` — Vistorias + itens
14. `create_financial_tables` — Faturas UUID, itens, pagamentos UUID
15. `create_financial_extra_tables` — Cauções, contas a pagar/receber, fluxo caixa
16. `create_maintenance_tables` — OS, itens OS, alertas
17. `create_operational_tables` — Multas, audit, templates notificação, tickets

### Models Criados (28)

- Branch, Setting, User (atualizado), Vehicle, VehicleCategory
- VehiclePhoto, VehicleDocument, VehicleAccessory
- Customer (UUID), CustomerDocument, AdditionalDriver
- Supplier, RentalExtra
- Reservation, ReservationExtra
- ContractTemplate, Contract (UUID + assinatura digital), ContractExtra, ContractLog
- VehicleInspection, InspectionItem
- Invoice (UUID + cálculo multa/juros), InvoiceItem, Payment (UUID + Mercado Pago), Caution
- ServiceOrder (recalculateTotal), ServiceOrderItem, MaintenanceAlert
- FineTraffic, AccountPayable, SupportTicket, SupportTicketMessage, NotificationTemplate

### Pendente

- ⚠️ MySQL precisa estar rodando para executar `php artisan migrate`
