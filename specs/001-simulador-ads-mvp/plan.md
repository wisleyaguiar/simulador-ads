# Implementation Plan: Simulador Ads MVP (v2)

**Branch**: `001-simulador-ads-mvp` | **Date**: 2026-04-16 | **Updated**: 2026-04-16 (v2)
**Spec**: [spec.md](./spec.md)
**Input**: PRD.md v2 + Feature specification v2

## Summary

O Simulador Ads MVP é uma aplicação web SaaS para previsão de resultados de campanhas Meta Ads. O motor de cálculo roda 100% no navegador (Vue.js + Pinia), consumindo dados de benchmarks de uma API Laravel REST.

**Novidades v2**: Lógica de Sazonalidade do Leilão (multiplicadores mensais no CPM/CPC), campo `confidence_score` em segments e regions, e módulo "Saúde dos Dados" no painel Admin.

**Pipeline de cálculo**: (1) Dedução Tributária → (2) Sazonalidade no CPM/CPC → (3) Maturidade no CTR/ConvRate → (4) Geração dos 3 Cenários.

## Technical Context

**Language/Version**: PHP 8.2+ (Backend), JavaScript/TypeScript ES2022+ (Frontend)
**Primary Dependencies**: Laravel 11+, Vue.js 3 (Composition API), Pinia, Vite, Tailwind CSS, Laravel Sail
**Storage**: MySQL 8.0 (via Docker/Sail)
**Testing**: PHPUnit (backend), Vitest (frontend), Lighthouse (PWA)
**Target Platform**: Web (Desktop + Mobile PWA)
**Performance Goals**: Recálculo dos 3 cenários em <200ms, importação de 500 registros em <5s
**Constraints**: Todos os comandos via `./vendor/bin/sail`, Tailwind exclusivamente via DESIGN.md, PWA instalável
**Scale/Scope**: MVP com 3 telas (Login, Dashboard, Admin), 4 tabelas MySQL, ~18 componentes Vue

## Constitution Check

| Princípio | Regra | Verificação |
|-----------|-------|-------------|
| I. Execução Estrita (Docker) | NUNCA rodar npm/php/composer nativos | ✅ Todos os comandos prefixados com `./vendor/bin/sail` |
| II. Regra de Ouro Tributária | Orçamento_Líquido = Bruto × (1 - 0.1215) quando toggle ativo | ✅ Lógica no Pinia store; precede sazonalidade no pipeline |
| III. Fidelidade de UI (Zero Invenção) | Ler DESIGN.md + _references/stitch/ antes de criar componentes | ✅ Cada componente Vue rastreável ao HTML Stitch |
| IV. Padrão PWA | Frontend como PWA desde o Dia 1 | ✅ Manifest + Service Worker na Fase 4 |

## Project Structure

### Documentation

```text
specs/001-simulador-ads-mvp/
├── spec.md              # Especificação funcional (v2)
├── plan.md              # Este arquivo (v2)
├── tasks.md             # Tarefas de implementação (v2)
└── checklists/
    └── requirements.md  # Checklist de qualidade
```

### Source Code

```text
# Backend (Laravel)
app/
├── Http/
│   ├── Controllers/
│   │   ├── Auth/
│   │   │   ├── RegisterController.php
│   │   │   └── LoginController.php
│   │   ├── ParameterController.php
│   │   ├── SimulationController.php
│   │   └── Admin/
│   │       └── ImportController.php
│   ├── Middleware/
│   │   └── IsAdmin.php
│   └── Requests/
│       ├── SimulationRequest.php
│       └── ImportRequest.php
├── Models/
│   ├── User.php
│   ├── Segment.php
│   ├── Region.php
│   └── Simulation.php
├── Services/
│   ├── SimulationService.php
│   ├── ImportService.php
│   └── DataHealthService.php          # [NEW v2] Saúde dos Dados
database/
├── migrations/
│   ├── xxxx_create_users_table.php
│   ├── xxxx_create_segments_table.php     # + confidence_score
│   ├── xxxx_create_regions_table.php      # + confidence_score
│   └── xxxx_create_simulations_table.php  # + campaign_month
├── seeders/
│   ├── SegmentSeeder.php                  # + confidence_score = 0.7
│   └── RegionSeeder.php                   # + confidence_score = 0.7
routes/
└── api.php                                # + GET /api/admin/data-health

# Frontend (Vue.js PWA)
resources/js/
├── app.js
├── router/
│   └── index.js
├── stores/
│   ├── useAuthStore.js
│   ├── useParameterStore.js
│   └── useSimulationStore.js
├── composables/
│   ├── useCalculator.js          # + applySeasonality()
│   ├── useAlerts.js              # + alerta de sazonalidade intensa
│   └── useSeasonality.js         # [NEW v2] Constantes e helper sazonal
├── components/
│   ├── layout/
│   │   ├── TopAppBar.vue
│   │   ├── AdminSidebar.vue
│   │   └── BottomNavBar.vue
│   ├── auth/
│   │   ├── LoginForm.vue
│   │   └── RegisterForm.vue
│   ├── dashboard/
│   │   ├── ParameterPanel.vue    # + seletor de Mês da Campanha
│   │   ├── BudgetSummaryCard.vue
│   │   ├── ScenarioCard.vue
│   │   ├── AlertBanner.vue
│   │   └── MetricGlassCard.vue
│   └── admin/
│       ├── UploadZone.vue
│       ├── ImportCard.vue
│       ├── StatusBanner.vue
│       ├── IntegrityFooter.vue
│       └── DataHealthPanel.vue   # [NEW v2] Saúde dos Dados
├── pages/
│   ├── LoginPage.vue
│   ├── DashboardPage.vue
│   ├── HistoryPage.vue
│   └── AdminPage.vue
└── assets/
    └── css/
        └── app.css

# PWA Assets
public/
├── manifest.json
├── sw.js
└── icons/
    ├── icon-192x192.png
    └── icon-512x512.png
```

---

## Fases de Execução (Aderente ao PRD §7)

### FASE 1–2: Infraestrutura, Banco de Dados e Modelagem

**Skills Obrigatórias**: `@laravel-expert`, `@database-architect`

**Objetivo**: Levantar ambiente Docker, criar o projeto Laravel, migrations, models e seeders.

**Entregáveis**:
- Projeto Laravel inicializado com Sail (MySQL + Redis)
- 4 migrations: `users`, `segments` (+ `confidence_score`), `regions` (+ `confidence_score`), `simulations` (+ `campaign_month`)
- 4 Models com relationships e casts
- Seeders com 10+ segmentos e 27 estados (todos com `confidence_score = 0.7`)
- Índices de performance

---

### FASE 3: Backend da API e Lógica de Negócios

**Skills Obrigatórias**: `@laravel-expert`, `@uncle-bob-craft` (Clean Code)

**Objetivo**: API RESTful completa com autenticação, endpoints de dados e lógica modular.

**Entregáveis**:
- Autenticação via Sanctum (register, login, logout)
- `GET /api/parameters` — segments + regions com confidence_score
- `POST /api/simulations` — persiste simulação (+ campaign_month)
- `GET /api/simulations` — histórico do usuário
- `POST /api/admin/import/segments` — upsert CSV com confidence_score
- `POST /api/admin/import/regions` — upsert CSV com confidence_score
- `GET /api/admin/data-health` — [NEW v2] retorna status de atualização e médias de confidence_score
- Middleware IsAdmin + SimulationService + ImportService + DataHealthService

---

### FASE 4: Setup do Frontend (Vue.js PWA) e Motor de Cálculo Local

**Skills Obrigatórias**: `@vue3-composition-api`, `@tailwind-master`

**Objetivo**: Configurar Vue.js como PWA, implementar motor de cálculo completo no Pinia, INCLUINDO o Multiplicador de Sazonalidade.

**Entregáveis**:
- Vue.js 3 + Vue Router + Pinia instalados via Sail
- Tailwind CSS com tokens do DESIGN.md
- Manifest + Service Worker via `vite-plugin-pwa`
- `useSeasonality` composable com constantes de multiplicadores mensais
- `useCalculator` composable com pipeline completo:
  1. `calculateNetBudget()` — Dedução tributária
  2. `applySeasonality(cpm, cpc, month)` — [NEW v2] Multiplicador sazonal
  3. `applyMaturityMultiplier()` — Ajuste por maturidade
  4. `calculateMetrics()` — Fórmulas base
  5. `generateScenarios()` — 3 cenários (×0.8, ×1.0, ×1.3)
- `useSimulationStore` com `campaignMonth` no state
- `useAlerts` com alerta de sazonalidade intensa

---

### FASE 5–5.5: UI/UX Reativa e Módulo Admin

**Skills Obrigatórias**: `@vue3-composition-api`, `@frontend-accessibility`

**Objetivo**: Componentes Vue extraídos dos HTMLs Stitch, integração com Pinia, módulo Admin com Saúde dos Dados.

**Fonte de UI**: `_references/stitch/login.html`, `dashboard.html`, `admin.html`

**Entregáveis**:
- **LoginPage.vue**: Split View, glassmorphism, gradient-button
- **DashboardPage.vue**: Grid 35/65, ParameterPanel (+ seletor de Mês da Campanha), ScenarioCards
- **AdminPage.vue**: Sidebar + UploadZone + DataHealthPanel [NEW v2] + IntegrityFooter
- **DataHealthPanel.vue** [NEW v2]: Alertas de tabelas desatualizadas + média de confidence_score
- Dropdown/seletor de "Mês da Campanha" no ParameterPanel
- Drag & Drop para upload de planilhas
- Integração reativa com Pinia stores

---

### FASE 6: Segurança, Auditoria e Deploy

**Skills Obrigatórias**: `@security-review`, `@web-performance`

**Objetivo**: Hardening, auditoria PWA, build e deploy.

**Entregáveis**:
- Validação de uploads CSV (MIME, tamanho, extensão, range de confidence_score)
- Rate limiting em endpoints sensíveis
- Auditoria Lighthouse PWA (score ≥ 90)
- Build de produção e documentação de deploy

---

## Complexity Tracking

| Violation | Why Needed | Simpler Alternative Rejected Because |
|-----------|------------|--------------------------------------|
| N/A | — | — |
