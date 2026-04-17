# Implementation Plan: Simulador Ads MVP

**Branch**: `001-simulador-ads-mvp` | **Date**: 2026-04-16 | **Spec**: [spec.md](./spec.md)
**Input**: Feature specification from `specs/001-simulador-ads-mvp/spec.md`

## Summary

O Simulador Ads MVP é uma aplicação web SaaS para previsão de resultados de campanhas Meta Ads. O motor de cálculo roda 100% no navegador (Vue.js + Pinia), consumindo dados de benchmarks de uma API Laravel REST. A UI segue fielmente o DESIGN.md com componentes extraídos dos HTMLs Stitch. O app é construído como PWA desde o Dia 1 com cache offline agressivo.

## Technical Context

**Language/Version**: PHP 8.2+ (Backend), JavaScript/TypeScript ES2022+ (Frontend)
**Primary Dependencies**: Laravel 11+, Vue.js 3 (Composition API), Pinia, Vite, Tailwind CSS, Laravel Sail
**Storage**: MySQL 8.0 (via Docker/Sail)
**Testing**: PHPUnit (backend), Vitest (frontend), Lighthouse (PWA)
**Target Platform**: Web (Desktop + Mobile PWA)
**Project Type**: Web application (SPA frontend + API backend)
**Performance Goals**: Recálculo dos 3 cenários em <200ms, importação de 500 registros em <5s
**Constraints**: Todos os comandos via `./vendor/bin/sail`, Tailwind exclusivamente via DESIGN.md, PWA instalável
**Scale/Scope**: MVP com 3 telas (Login, Dashboard, Admin), 4 tabelas MySQL, ~15 componentes Vue

## Constitution Check

*GATE: Must pass before Phase 0 research. Re-check after Phase 1 design.*

| Princípio | Regra | Verificação |
|-----------|-------|-------------|
| I. Execução Estrita (Docker) | NUNCA rodar npm/php/composer nativos | ✅ Todos os comandos prefixados com `./vendor/bin/sail` |
| II. Regra de Ouro Tributária | Orçamento_Líquido = Bruto × (1 - 0.1215) quando toggle ativo | ✅ Lógica encapsulada no Pinia store `useSimulationStore` |
| III. Fidelidade de UI (Zero Invenção) | Ler DESIGN.md + _references/stitch/ antes de criar componentes | ✅ Cada componente Vue rastreável ao HTML Stitch correspondente |
| IV. Padrão PWA | Frontend como PWA desde o Dia 1 | ✅ Manifest + Service Worker configurados na Fase 4 |

## Project Structure

### Documentation (this feature)

```text
specs/001-simulador-ads-mvp/
├── spec.md              # Especificação funcional
├── plan.md              # Este arquivo
├── tasks.md             # Tarefas de implementação
└── checklists/
    └── requirements.md  # Checklist de qualidade da spec
```

### Source Code (repository root)

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
│   └── ImportService.php
database/
├── migrations/
│   ├── xxxx_create_users_table.php
│   ├── xxxx_create_segments_table.php
│   ├── xxxx_create_regions_table.php
│   └── xxxx_create_simulations_table.php
├── seeders/
│   ├── SegmentSeeder.php
│   └── RegionSeeder.php
routes/
└── api.php

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
│   ├── useCalculator.js
│   └── useAlerts.js
├── components/
│   ├── layout/
│   │   ├── TopAppBar.vue
│   │   ├── AdminSidebar.vue
│   │   └── BottomNavBar.vue
│   ├── auth/
│   │   ├── LoginForm.vue
│   │   └── RegisterForm.vue
│   ├── dashboard/
│   │   ├── ParameterPanel.vue
│   │   ├── BudgetSummaryCard.vue
│   │   ├── ScenarioCard.vue
│   │   ├── AlertBanner.vue
│   │   └── MetricGlassCard.vue
│   └── admin/
│       ├── UploadZone.vue
│       ├── ImportCard.vue
│       ├── StatusBanner.vue
│       └── IntegrityFooter.vue
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

**Structure Decision**: Aplicação Web com backend Laravel servindo API REST e frontend Vue.js SPA embarcado via Vite. Ambos coexistem no mesmo repositório Laravel com o frontend em `resources/js/`.

---

## Fases de Execução (Aderente ao PRD §7)

### FASE 1–2: Infraestrutura, Banco de Dados e Modelagem

**Objetivo**: Levantar o ambiente Docker, criar o projeto Laravel, implementar todas as migrations, models e seeders.

**Entregáveis**:
- Projeto Laravel inicializado com Sail (MySQL + Redis)
- 4 migrations: `users`, `segments`, `regions`, `simulations`
- 4 Models com relacionamentos (User → hasMany Simulations, Simulation → belongsTo Segment/Region/User)
- Seeders com dados iniciais de 10+ segmentos e 27 estados brasileiros
- Índices de performance em `simulations.user_id`, `segments.name`, `regions.name`

---

### FASE 3: Backend da API e Lógica de Negócios

**Objetivo**: Implementar a API RESTful completa com autenticação e endpoints de dados.

**Entregáveis**:
- Autenticação via Sanctum (register, login, logout)
- `GET /api/parameters` — retorna segments + regions em uma única chamada
- `POST /api/simulations` — persiste simulação calculada pelo frontend
- `GET /api/simulations` — histórico do usuário autenticado
- `POST /api/admin/import/segments` — upload e upsert de CSV de segmentos
- `POST /api/admin/import/regions` — upload e upsert de CSV de regiões
- Middleware `IsAdmin` protegendo rotas admin
- `SimulationService` com lógica modular (separação Clean Code)
- `ImportService` com validação de colunas e parser CSV/Excel

---

### FASE 4: Setup do Frontend (Vue.js PWA) e Motor de Cálculo Local

**Objetivo**: Configurar o projeto Vue.js como PWA com Vite, implementar o motor de cálculo completo no Pinia e integrar Tailwind CSS com tokens do DESIGN.md.

**Entregáveis**:
- Vue.js 3 + Vue Router + Pinia instalados via Sail
- Tailwind CSS configurado com tokens extraídos do DESIGN.md
- `manifest.json` com tema #0668E1 e ícones
- Service Worker com estratégia cache-first para assets estáticos
- `vite-plugin-pwa` configurado
- `useSimulationStore` com a lógica completa:
  - Cálculo de Orçamento_Líquido (toggle 12,15%)
  - Fórmulas: Impressões, Cliques, Leads, CPA
  - Multiplicadores de maturidade (-15%, 0%, +10%)
  - Geração dos 3 cenários (×0.8, ×1.0, ×1.3)
- `useParameterStore` com fetch único de /api/parameters
- `useCalculator` composable para lógica pura de cálculo
- `useAlerts` composable para gatilhos de alertas inteligentes

---

### FASE 5–5.5: UI/UX Reativa e Módulo Admin

**Objetivo**: Construir todos os componentes Vue extraídos dos HTMLs Stitch, integrar com Pinia para reatividade, e implementar o módulo Admin completo.

**Fonte de UI (Obrigatório)**: `_references/stitch/login.html`, `_references/stitch/dashboard.html`, `_references/stitch/admin.html`

**Entregáveis**:
- **LoginPage.vue**: Extraído de `login.html` (Split View 50/50, glassmorphism, gradient-button)
- **DashboardPage.vue**: Extraído de `dashboard.html` (Grid 35/65, ParameterPanel + ScenarioCards)
- **AdminPage.vue**: Extraído de `admin.html` (Sidebar + UploadZone + IntegrityFooter)
- Componentes atômicos: TopAppBar, ParameterPanel, BudgetSummaryCard, ScenarioCard, AlertBanner, UploadZone, StatusBanner, IntegrityFooter, MetricGlassCard
- Integração de sliders e inputs com Pinia store (reatividade em tempo real)
- Middleware IsAdmin no backend + guarda de rota no frontend
- Drag & Drop para upload de planilhas
- Botões de exportação (Excel, PDF, PNG) — estrutura de UI apenas no MVP

---

### FASE 6: Segurança, Auditoria e Deploy

**Objetivo**: Hardening de segurança, auditoria PWA, build de produção e deploy.

**Entregáveis**:
- Validação de arquivos CSV contra injeção (tipo MIME, tamanho, extensão)
- Rate limiting em endpoints sensíveis
- CSRF e sanitização de inputs
- Auditoria Lighthouse PWA (score ≥ 90)
- Build de produção: `./vendor/bin/sail npm run build`
- Documentação de deploy para VPS + CloudPanel
- Script de deploy automatizado (git pull, composer install, npm build, migrate, cache)

---

## Complexity Tracking

> **Fill ONLY if Constitution Check has violations that must be justified**

| Violation | Why Needed | Simpler Alternative Rejected Because |
|-----------|------------|--------------------------------------|
| N/A | — | — |
