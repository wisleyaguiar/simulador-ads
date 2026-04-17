# Tasks: Simulador Ads MVP

**Input**: Design documents from `specs/001-simulador-ads-mvp/`
**Prerequisites**: plan.md (required), spec.md (required)
**Constitution**: `.specify/memory/constitution.md` v1.0.0

## Format: `[ID] [P?] [Fase] Description`

- **[P]**: Pode rodar em paralelo (arquivos diferentes, sem dependências)
- **[Fase]**: Fase do PRD à qual a tarefa pertence (F1-F6)

---

## Fase 1–2: Infraestrutura, Banco de Dados e Modelagem

**Purpose**: Levantar ambiente Docker, criar projeto Laravel, migrations, models e seeders.
**⚠️ CRITICAL**: Todas as fases subsequentes dependem desta fase estar 100% concluída.

### Setup do Projeto

- [ ] T001 [F1] Inicializar projeto Laravel via Docker (Sail) com MySQL
  - Comando: `curl -s "https://laravel.build/simulador-ads?with=mysql" | bash`
  - Mover conteúdo para raiz do projeto atual
  - Garantir que `./vendor/bin/sail up -d` suba os containers
- [ ] T002 [F1] Configurar `.env` com credenciais MySQL do Sail
- [ ] T003 [P] [F1] Adicionar entradas ao `.gitignore` (node_modules, .env, vendor)

### Migrations

- [ ] T004 [F2] Criar migration `create_segments_table` com colunas: `id`, `name`, `avg_ctr`, `avg_cpc`, `avg_conversion_rate`, `created_at`, `updated_at`
  - Índice único em `name`
- [ ] T005 [P] [F2] Criar migration `create_regions_table` com colunas: `id`, `name`, `total_population`, `reachable_audience`, `avg_cpm`, `created_at`, `updated_at`
  - Índice único em `name`
- [ ] T006 [F2] Adicionar campo `role` (enum: user/admin) à tabela `users` via migration
- [ ] T007 [F2] Criar migration `create_simulations_table` com colunas: `id`, `user_id` (FK), `budget`, `payment_type`, `campaign_days`, `region_id` (FK), `segment_id` (FK), `goal`, `maturity_level`, `results_json`, `created_at`
  - Índices em `user_id`, `region_id`, `segment_id`

### Models e Relationships

- [ ] T008 [P] [F2] Criar Model `Segment` com fillable fields em `app/Models/Segment.php`
- [ ] T009 [P] [F2] Criar Model `Region` com fillable fields em `app/Models/Region.php`
- [ ] T010 [F2] Criar Model `Simulation` com fillable fields, casts (results_json → array) e relationships (belongsTo User, Segment, Region) em `app/Models/Simulation.php`
- [ ] T011 [F2] Atualizar Model `User` com hasMany Simulations e cast de role

### Seeders

- [ ] T012 [P] [F2] Criar `SegmentSeeder` com pelo menos 10 segmentos de mercado (Saúde, Imobiliário, E-commerce Moda, SaaS B2B, Educação Online, Alimentação, Automotivo, Finanças, Turismo, Fitness)
- [ ] T013 [P] [F2] Criar `RegionSeeder` com os 27 estados brasileiros + dados de população IBGE e CPM estimado
- [ ] T014 [F2] Registrar seeders no `DatabaseSeeder.php` e executar: `./vendor/bin/sail artisan db:seed`

**Checkpoint**: ✅ Banco de dados populado. Validar com `./vendor/bin/sail artisan tinker` que Segment::count() ≥ 10 e Region::count() ≥ 27.

---

## Fase 3: Backend da API e Lógica de Negócios

**Purpose**: API RESTful completa com autenticação, endpoints de dados e lógica modular.

### Autenticação (Sanctum)

- [ ] T015 [F3] Instalar e configurar Laravel Sanctum: `./vendor/bin/sail composer require laravel/sanctum`
- [ ] T016 [F3] Criar `RegisterController` com validação (name, email, password) em `app/Http/Controllers/Auth/`
- [ ] T017 [P] [F3] Criar `LoginController` com autenticação via Sanctum token
- [ ] T018 [F3] Registrar rotas de autenticação em `routes/api.php`: POST /register, POST /login, POST /logout

### Endpoints da Aplicação

- [ ] T019 [F3] Criar `ParameterController@index` em `app/Http/Controllers/` retornando segments + regions agrupados
  - `GET /api/parameters` — rota pública (ou autenticada, decisão do dev)
- [ ] T020 [F3] Criar `SimulationController@store` — recebe payload JSON com resultados calculados e persiste
  - `POST /api/simulations` — rota autenticada
- [ ] T021 [P] [F3] Criar `SimulationController@index` — retorna histórico do usuário autenticado ordenado por data
  - `GET /api/simulations` — rota autenticada

### SimulationService (Clean Code)

- [ ] T022 [F3] Criar `SimulationService` em `app/Services/` com métodos:
  - `calculateNetBudget(float $gross, bool $deductTax): float`
  - `calculateMetrics(float $budget, float $cpm, float $ctr, float $conversionRate): array`
  - `applyMaturityMultiplier(array $metrics, string $level): array`
  - `generateScenarios(array $baseMetrics): array` (conservador ×0.8, realista ×1.0, otimista ×1.3)

### Módulo Admin (Import)

- [ ] T023 [F3] Criar Middleware `IsAdmin` verificando `$request->user()->role === 'admin'`
- [ ] T024 [F3] Criar `ImportController@importSegments` — parse CSV/Excel, valida colunas obrigatórias (name, avg_ctr, avg_cpc, avg_conversion_rate), upsert por name
  - `POST /api/admin/import/segments`
- [ ] T025 [P] [F3] Criar `ImportController@importRegions` — parse CSV/Excel, valida colunas (name, total_population, reachable_audience, avg_cpm), upsert por name
  - `POST /api/admin/import/regions`
- [ ] T026 [F3] Criar `ImportService` em `app/Services/` com lógica de validação de colunas e parsing de CSV/Excel
  - Usar `Maatwebsite/Laravel-Excel` ou parser nativo do PHP
- [ ] T027 [F3] Registrar todas as rotas admin em `routes/api.php` protegidas pelo middleware `IsAdmin`

**Checkpoint**: ✅ API funcional. Testar com Postman/Insomnia: register → login → GET /parameters → POST /simulations.

---

## Fase 4: Setup do Frontend (Vue.js PWA) e Motor de Cálculo Local

**Purpose**: Configurar Vue.js como PWA com Vite, implementar motor de cálculo completo no Pinia.

### ⚡ CHECKLIST RIGOROSO — Fase 4

> Cada item DEVE ser verificado e marcado antes de avançar para a Fase 5.
> Nenhum componente de UI pode ser criado até que este checklist esteja 100%.

#### 4.1 Setup Vue.js + Vite

- [ ] T028 [F4] Instalar Vue.js 3, Vue Router, Pinia via Sail:
  ```bash
  ./vendor/bin/sail npm install vue@3 vue-router@4 pinia
  ```
- [ ] T029 [F4] Instalar e configurar Vite para Laravel:
  ```bash
  ./vendor/bin/sail npm install @vitejs/plugin-vue
  ```
  - Configurar `vite.config.js` com alias `@` para `resources/js`
- [ ] T030 [F4] Criar ponto de entrada `resources/js/app.js` com createApp, use(router), use(pinia)
- [ ] T031 [F4] Criar `resources/js/router/index.js` com rotas: `/login`, `/dashboard`, `/admin`, `/history`
  - Guards de navegação: redirecionar para /login se não autenticado

**☑ GATE 4.1**: Executar `./vendor/bin/sail npm run dev` → Página em branco do Vue renderiza sem erros no console.

#### 4.2 Tailwind CSS (via DESIGN.md)

- [ ] T032 [F4] Instalar Tailwind CSS via Sail:
  ```bash
  ./vendor/bin/sail npm install -D tailwindcss postcss autoprefixer
  ./vendor/bin/sail npx tailwindcss init -p
  ```
- [ ] T033 [F4] Configurar `tailwind.config.js` com TODOS os tokens do DESIGN.md:
  - Cores: copiar exatamente o objeto `colors` dos HTMLs Stitch (primary, on-surface, surface-container-low, etc.)
  - borderRadius: DEFAULT 1rem, lg 2rem, xl 3rem, full 9999px
  - fontFamily: headline [Manrope], body [Inter], label [Inter]
- [ ] T034 [F4] Criar `resources/js/assets/css/app.css` com:
  - `@tailwind base; @tailwind components; @tailwind utilities;`
  - Classes utilitárias globais: `.glass-panel`, `.gradient-button`, `.editorial-shadow`, `.glass-metric`
  - Regra de fonte padrão: `body { font-family: 'Inter', sans-serif; }`
  - Importar Google Fonts (Manrope + Inter + Material Symbols Outlined) no `<head>` do blade
- [ ] T035 [F4] Verificar que `resources/views/app.blade.php` inclui `@vite` directive e div #app

**☑ GATE 4.2**: Classes Tailwind do DESIGN.md (ex: `bg-surface-container-low`, `text-primary`, `font-headline`) funcionam sem erros.

#### 4.3 Configuração PWA

- [ ] T036 [F4] Instalar plugin PWA do Vite:
  ```bash
  ./vendor/bin/sail npm install -D vite-plugin-pwa
  ```
- [ ] T037 [F4] Configurar `vite-plugin-pwa` no `vite.config.js` com:
  - `registerType: 'autoUpdate'`
  - `manifest`: name "Simulador Ads", short_name "SimAds", theme_color "#0668E1", background_color "#faf9ff", display "standalone"
  - `icons`: 192x192 e 512x512
  - `workbox.runtimeCaching`: cache-first para assets estáticos, network-first para API
- [ ] T038 [P] [F4] Criar ícones PWA (192x192 e 512x512) em `public/icons/`
- [ ] T039 [F4] Verificar que o Service Worker cacheia assets estáticos e a lógica JS do motor de cálculo

**☑ GATE 4.3**: No Chrome DevTools → Application → Manifest aparece corretamente. Service Worker registrado e ativo.

#### 4.4 Motor de Cálculo (Pinia Stores)

- [ ] T040 [F4] Criar `useParameterStore` em `resources/js/stores/`:
  - `segments: []`, `regions: []`, `loaded: false`
  - Action `fetchParameters()`: GET /api/parameters → preenche state
  - Getter `getSegmentById(id)`, `getRegionById(id)`
- [ ] T041 [F4] Criar `useAuthStore` em `resources/js/stores/`:
  - `user: null`, `token: null`, `isAuthenticated: boolean`
  - Actions: `register()`, `login()`, `logout()`, `fetchUser()`
- [ ] T042 [F4] Criar composable `useCalculator` em `resources/js/composables/`:
  - **Função `calculateNetBudget(grossBudget, taxEnabled)`**:
    - Se taxEnabled: `grossBudget * (1 - 0.1215)`
    - Senão: `grossBudget`
  - **Função `applyMaturityMultiplier(baseCtr, baseConvRate, maturityLevel)`**:
    - Iniciante: `{ ctr: baseCtr * 0.85, convRate: baseConvRate * 0.85 }`
    - Intermediário: `{ ctr: baseCtr, convRate: baseConvRate }`
    - Avançado: `{ ctr: baseCtr * 1.10, convRate: baseConvRate * 1.10 }`
  - **Função `calculateMetrics(budget, cpm, ctr, conversionRate)`**:
    - `impressions = (budget / cpm) * 1000`
    - `clicks = impressions * ctr`
    - `leads = clicks * conversionRate`
    - `cpa = budget / leads` (guard: leads > 0)
  - **Função `generateScenarios(budget, cpm, adjustedCtr, adjustedConvRate)`**:
    - Conservador: `calculateMetrics(budget, cpm, adjustedCtr * 0.8, adjustedConvRate * 0.8)`
    - Realista: `calculateMetrics(budget, cpm, adjustedCtr, adjustedConvRate)`
    - Otimista: `calculateMetrics(budget, cpm, adjustedCtr * 1.3, adjustedConvRate * 1.3)`
- [ ] T043 [F4] Criar `useSimulationStore` em `resources/js/stores/`:
  - State: `grossBudget`, `taxEnabled`, `campaignDays`, `regionId`, `segmentId`, `goal`, `maturityLevel`, `scenarios: { conservative: {}, realistic: {}, optimistic: {} }`
  - Getters computados: `netBudget`, `adjustedMetrics`, `scenarios` (todos reativos, recalculam automaticamente)
  - Action `saveSimulation()`: POST /api/simulations com resultado calculado
  - Action `loadHistory()`: GET /api/simulations
- [ ] T044 [F4] Criar composable `useAlerts` em `resources/js/composables/`:
  - Gatilhos condicionais:
    - `budgetTooLow`: netBudget < (cpm * 1000 * 3) do segmento selecionado
    - `periodTooShort`: campaignDays < 14
    - `audienceTooSmall`: reachable_audience da região < 50.000

**☑ GATE 4.4**: Criar teste manual no console do navegador — importar `useCalculator`, chamar `generateScenarios(5000, 25, 0.018, 0.025)` e verificar que retorna 3 objetos com valores distintos e coerentes.

#### 4.5 Validação Final da Fase 4

- [ ] T045 [F4] **VALIDAÇÃO DE INTEGRAÇÃO**: Montar uma página temporária de teste que:
  - Carrega parameters do backend
  - Exibe um input de orçamento
  - Ao digitar, os 3 cenários recalculam em tempo real no console
  - Toggle de impostos altera os valores corretamente
  - Multiplicador de maturidade altera CTR e Conv Rate

**☑ GATE FINAL FASE 4**: Todos os 5 gates anteriores passam. O motor de cálculo funciona isoladamente no navegador sem nenhum componente visual final.

---

## Fase 5–5.5: UI/UX Reativa e Módulo Admin

**Purpose**: Construir componentes Vue extraídos dos HTMLs Stitch e integrar com Pinia.

### ⚡ CHECKLIST RIGOROSO — Fase 5

> A criação de cada componente DEVE seguir esta ordem estritamente:
> 1. Abrir o HTML Stitch correspondente em `_references/stitch/`
> 2. Ler o DESIGN.md para confirmar tokens aplicáveis
> 3. Extrair as classes Tailwind do HTML
> 4. Converter para componente Vue limpo
> 5. Integrar props/emits com o Pinia store
> 6. Verificar fidelidade visual comparando com o HTML Stitch

#### 5.1 Tela de Login (Fonte: `_references/stitch/login.html`)

- [ ] T046 [F5] Criar componente `LoginForm.vue` extraído de `login.html` linhas 162-204:
  - Input de e-mail com ícone `mail`
  - Input de senha com ícone `lock` e toggle de visibilidade
  - Botão "Criar conta grátis" com classe `gradient-button`
  - Divisor "ou" e botão Google (visual apenas)
  - Footer com link "Já possui conta? Fazer login"
  - **Integrar com `useAuthStore.register()` e `useAuthStore.login()`**
- [ ] T047 [F5] Criar componente `LoginPage.vue` com layout Split View:
  - Lado esquerdo (55%): Painel visual com gradiente `from-primary to-primary-container`, badge "Inteligência Preditiva", título "Domine o futuro...", card glass-panel com métrica decorativa
  - Lado direito (45%): LoginForm.vue centralizado sobre fundo `bg-surface`
  - Responsivo: lado esquerdo `hidden md:flex`

**☑ GATE 5.1**: Tela de Login visualmente idêntica ao `login.html` Stitch. Fluxo register → login → redirect para /dashboard funciona.

#### 5.2 Componentes de Layout

- [ ] T048 [P] [F5] Criar `TopAppBar.vue` extraído de `dashboard.html` linhas 94-114:
  - Logo "Simulador Ads", links de navegação (Dashboard, Cenários, Histórico, Insights)
  - Ícones de notificação e settings
  - Botão "Nova Simulação" com gradiente
  - Avatar do usuário
  - Classes: `bg-white/80 backdrop-blur-xl`, sombra editorial
- [ ] T049 [P] [F5] Criar `AdminSidebar.vue` extraído de `admin.html` linhas 91-131:
  - Logo com badge "Admin Console"
  - Links: Visão Geral, Gerenciar Usuários, Importar Dados (ativo)
  - Footer: Help Center, Log Out
  - Classes: `bg-slate-50`, `w-64`, `fixed left-0`
- [ ] T050 [P] [F5] Criar `BottomNavBar.vue` extraído de `admin.html` linhas 255-268:
  - 3 botões: Início, Importar, Admin
  - Classes: `md:hidden fixed bottom-0`, `bg-white/80 backdrop-blur-xl`

**☑ GATE 5.2**: Componentes de layout renderizam corretamente no desktop e mobile.

#### 5.3 Dashboard: Painel de Parâmetros (Fonte: `_references/stitch/dashboard.html`)

- [ ] T051 [F5] Criar `ParameterPanel.vue` extraído de `dashboard.html` linhas 118-196:
  - Input "Orçamento Total (R$)" — `v-model` vinculado a `useSimulationStore.grossBudget`
  - Select "Tipo de Conta 2026" (Pré-paga PIX / Pós-paga Cartão)
  - Grid 2 colunas: Input "Período (Dias)" + Select "Região" — dados de `useParameterStore.regions`
  - Select "Segmento de Mercado" — dados de `useParameterStore.segments`
  - Radio group "Objetivo da Campanha" (Tráfego, Leads, Conversões)
  - Radio group "Nível de Maturidade" (Iniciante, Intermediário, Avançado)
  - **Toggle "Deduzir Impostos (12,15%)"** — `v-model` vinculado a `useSimulationStore.taxEnabled`
  - Botão "Simular Resultados"
  - Todas as classes Tailwind extraídas exatamente do HTML Stitch

**☑ GATE 5.3**: Cada campo do painel está conectado ao Pinia store. Alterar qualquer input dispara reatividade.

#### 5.4 Dashboard: Área de Resultados

- [ ] T052 [F5] Criar `BudgetSummaryCard.vue` extraído de `dashboard.html` linhas 214-234:
  - Gradiente `from-primary to-primary-container`
  - Grid de 3 colunas: Orçamento Bruto | Dedução Tributária (12.15%) | Orçamento Real (Líquido)
  - Valores reativos de `useSimulationStore`
  - Blob decorativo com `blur-3xl`
- [ ] T053 [F5] Criar `ScenarioCard.vue` (componente reutilizável) extraído de `dashboard.html` linhas 237-323:
  - Props: `type` (conservative|realistic|optimistic), `data` (impressions, clicks, leads, cpa)
  - Cores dinâmicas por tipo:
    - Conservador: `bg-red-50/50`, `border-red-100`, textos `text-red-900`
    - Realista: `bg-yellow-50/50`, `border-yellow-100`, `ring-2 ring-yellow-400/20`
    - Otimista: `bg-emerald-50/50`, `border-emerald-100`, textos `text-emerald-900`
  - Badge com nome do cenário
  - Métricas: Impressões (destaque), grid 2 cols (Cliques + Leads), CPA Estimado
- [ ] T054 [F5] Criar `AlertBanner.vue` extraído de `dashboard.html` linhas 326-353:
  - Props: `type` (warning|tip), `title`, `description`
  - Ícone e cor dinâmicos (warning → amarelo, tip → verde)
  - Integrado com `useAlerts` composable
- [ ] T055 [P] [F5] Criar `MetricGlassCard.vue` extraído de `dashboard.html` linhas 354-364:
  - Overlay com gradiente sobre imagem
  - Card glass-metric com "Alcance Estimado Total" reativo
- [ ] T056 [F5] Criar botões de exportação (Excel, PDF, PNG) extraídos de `dashboard.html` linhas 201-213:
  - Estrutura visual apenas (funcionalidade pós-MVP)

**☑ GATE 5.4**: Os 3 ScenarioCards exibem valores diferentes e recalculam instantaneamente ao mudar qualquer parâmetro.

#### 5.5 Dashboard: Montagem da Página

- [ ] T057 [F5] Montar `DashboardPage.vue` com layout grid `grid-cols-1 lg:grid-cols-[35%_65%]`:
  - Coluna esquerda: `ParameterPanel.vue`
  - Coluna direita: botões de exportação + `BudgetSummaryCard.vue` + grid 3 colunas de `ScenarioCard.vue` + `AlertBanner.vue` (múltiplos, via useAlerts) + `MetricGlassCard.vue`
  - Carregar `useParameterStore.fetchParameters()` no `onMounted`

**☑ GATE 5.5**: Dashboard completo renderiza, é fiel ao HTML Stitch, e todos os cálculos funcionam em tempo real.

#### 5.6 Módulo Admin (Fonte: `_references/stitch/admin.html`)

- [ ] T058 [F5] Criar `UploadZone.vue` extraído de `admin.html` linhas 179-186:
  - Área de Drag & Drop com borda `border-dashed border-outline-variant/50`
  - Ícone `cloud_upload` com animação no hover (`group-hover:scale-110`)
  - Input file invisível com `opacity-0`
  - Emits: `@fileSelected(file)`
- [ ] T059 [F5] Criar `ImportCard.vue` extraído de `admin.html` linhas 169-197:
  - Props: `title`, `description`, `templateUrl`, `uploadEndpoint`
  - Inclui: título, descrição, link "Baixar Template", UploadZone, StatusBanner, botão "Importar"
  - Integra com API via fetch: POST ao endpoint com FormData
- [ ] T060 [P] [F5] Criar `StatusBanner.vue` extraído de `admin.html` linhas 188-221:
  - Props: `type` (success|error), `message`
  - Sucesso: `bg-emerald-50`, ícone `check_circle` verde
  - Erro: `bg-error-container/40`, ícone `error` vermelho
- [ ] T061 [F5] Criar `IntegrityFooter.vue` extraído de `admin.html` linhas 229-250:
  - "Integridade do Banco" com 3 pills: Média CPM, Média CTR, Cidades
  - Dados carregados de `useParameterStore` (computados a partir de segments e regions)
- [ ] T062 [F5] Montar `AdminPage.vue`:
  - Layout com `AdminSidebar.vue` (esquerda) + área de conteúdo (direita)
  - Header glass com badge "Admin Panel"
  - Grid 2 colunas: 2 × `ImportCard.vue` (Segmentos + Regiões)
  - Footer: `IntegrityFooter.vue`
  - Guard de rota: redirecionar para /dashboard se `user.role !== 'admin'`

**☑ GATE 5.6**: Upload funcional — CSV válido exibe banner verde, CSV inválido exibe banner vermelho com coluna faltante.

#### 5.7 Página de Histórico

- [ ] T063 [F5.5] Criar `HistoryPage.vue`:
  - Lista de simulações salvas ordenadas por data
  - Cada item mostra: data, orçamento, segmento, cenário realista resumido
  - Dados de `useSimulationStore.loadHistory()`
  - Design seguindo tokens do DESIGN.md (cards com `bg-surface-container-lowest`, sombras editoriais)

**☑ GATE 5.7**: Histórico lista simulações salvas e permite visualizar detalhes.

#### 5.8 Validação Final da Fase 5

- [ ] T064 [F5.5] **TESTE DE FIDELIDADE VISUAL**: Para cada tela (Login, Dashboard, Admin):
  1. Abrir o HTML Stitch correspondente em browser
  2. Abrir a tela Vue implementada lado a lado
  3. Verificar que cores, fontes, espaçamentos, raios de borda e sombras são idênticos
  4. Documentar qualquer divergência e corrigir
- [ ] T065 [F5.5] **TESTE DO FLUXO COMPLETO**:
  1. Register → Login → Dashboard
  2. Preencher todos os parâmetros → Cenários calculados
  3. Ativar toggle de impostos → Valores recalculam
  4. Mudar maturidade → CTR e Conv Rate mudam
  5. Salvar simulação → Ir para Histórico → Simulação listada
  6. Navegar para Admin → Upload de CSV → Feedback exibido
  7. Voltar ao Dashboard → Dados atualizados

**☑ GATE FINAL FASE 5**: Todas as 3 telas fidedignas ao Stitch. Fluxo end-to-end funcional.

---

## Fase 6: Segurança, Auditoria e Deploy

**Purpose**: Hardening de segurança, auditoria PWA e preparação para deploy.

### Segurança

- [ ] T066 [P] [F6] Validar uploads CSV no `ImportService`: tipo MIME (text/csv, application/vnd.ms-excel), tamanho máximo 5MB, extensão (.csv, .xlsx)
- [ ] T067 [P] [F6] Implementar rate limiting nos endpoints: `/api/login` (5/min), `/api/admin/import/*` (10/min)
- [ ] T068 [F6] Verificar CSRF protection ativo e sanitização de inputs em todos os FormRequests
- [ ] T069 [F6] Revisar que nenhuma rota admin é acessível sem middleware IsAdmin

### Auditoria PWA

- [ ] T070 [F6] Executar Lighthouse PWA audit no Chrome DevTools
  - Score mínimo requerido: 90
  - Verificar: manifest válido, Service Worker registrado, HTTPS (em produção), ícones corretos
- [ ] T071 [F6] Testar offline: desconectar internet → interface carrega → cálculos funcionam com dados cacheados

### Build e Deploy

- [ ] T072 [F6] Gerar build de produção: `./vendor/bin/sail npm run build`
- [ ] T073 [F6] Verificar que assets compilados estão em `public/build/`
- [ ] T074 [F6] Documentar script de deploy para VPS + CloudPanel:
  ```bash
  git pull origin main
  composer install --no-dev --optimize-autoloader
  npm install && npm run build
  php artisan migrate --force
  php artisan config:cache
  php artisan route:cache
  ```
- [ ] T075 [F6] Criar usuário admin via seeder ou artisan tinker para ambiente de produção

**Checkpoint Final**: ✅ Aplicação pronta para deploy. Build passa, PWA auditada, segurança verificada.

---

## Dependencies & Execution Order

### Phase Dependencies

- **Fase 1–2 (Infra)**: Sem dependências — inicia imediatamente
- **Fase 3 (API)**: Depende de Fase 1–2 (Models e migrations necessários)
- **Fase 4 (Frontend PWA)**: Depende de Fase 3 (API necessária para fetch de parameters)
- **Fase 5 (UI)**: Depende de Fase 4 (stores e composables necessários)
- **Fase 6 (Deploy)**: Depende de Fase 5 (app completo para auditar)

### Parallel Opportunities

- T004 e T005 (migrations de segments e regions) podem rodar em paralelo
- T008 e T009 (models Segment e Region) podem rodar em paralelo
- T012 e T013 (seeders) podem rodar em paralelo
- T048, T049, T050 (componentes de layout) podem rodar em paralelo
- T055 e T056 (MetricGlass e botões export) podem rodar em paralelo
- T066 e T067 (segurança) podem rodar em paralelo

---

## Notes

- [P] tasks = arquivos diferentes, sem dependências
- [F?] label mapeia a tarefa à fase do PRD correspondente
- Comandos DEVEM ser prefixados com `./vendor/bin/sail` (Princípio I da Constituição)
- Tailwind deve seguir DESIGN.md e _references/stitch/ (Princípio III da Constituição)
- Toggle de impostos DEVE usar Orçamento_Líquido em todos os cálculos (Princípio II da Constituição)
- PWA DEVE estar configurada desde a Fase 4 (Princípio IV da Constituição)
- Commit após cada tarefa ou grupo lógico com mensagem descritiva
- Total: 75 tarefas distribuídas em 6 fases
