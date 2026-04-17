# PRD - Simulador Ads (MVP)

## 1. Visão Geral do Produto
O **Simulador Ads** é uma aplicação web (SaaS) projetada para prever resultados de campanhas no Meta Ads. O objetivo principal é fornecer previsibilidade financeira e alinhamento de expectativas, evitando que os usuários gastem dinheiro de forma desnecessária em campanhas não otimizadas. 

## 2. Stack Tecnológico
- **Frontend:** Vue.js (Configurado como PWA - Progressive Web App utilizando Vite).
- **Estilização:** Tailwind CSS (seguindo estritamente o arquivo `DESIGN.md`).
- **Backend:** PHP com framework Laravel.
- **Banco de Dados:** MySQL (Relacional).
- **Infraestrutura Local:** Docker (Laravel Sail ou Docker Compose customizado).
- **Versionamento:** Git e repositório no GitHub.
- **Produção:** VPS rodando CloudPanel.

### 2.1. Regras Estritas de Ambiente e Execução (⚠️ LEITURA OBRIGATÓRIA PARA A IA)
A infraestrutura local deste projeto roda EXCLUSIVAMENTE via Docker utilizando o **Laravel Sail**. O ambiente host (máquina local) não possui as dependências do Node ou PHP configuradas globalmente.

Portanto, aplique a seguinte regra absoluta para a execução de qualquer comando:
- **NUNCA** tente rodar `npm`, `npx`, `php` ou `composer` diretamente na raiz do projeto (host).
- **SEMPRE** prefixe os comandos de gerenciamento de pacotes e build com o executável do Sail.
- **Exemplos Corretos (USE ESTES):** - `./vendor/bin/sail npm install`
  - `./vendor/bin/sail npm run dev`
  - `./vendor/bin/sail npx tailwindcss -i ...`
  - `./vendor/bin/sail artisan migrate`
- **Exemplos Incorretos (NÃO USE):** `npm run dev`, `npx tailwindcss`, `php artisan...`.
- **Arquivos de Referência:** Sempre consulte a pasta `_references/stitch/` para obter o código HTML/Tailwind base das telas antes de iniciar qualquer desenvolvimento de interface.

## 3. Funcionalidades Principais do MVP
A lógica de negócios baseia-se nas seguintes funcionalidades centrais:
1. **Calculadora de Custo Real (Ajuste Tributário 2026 via Toggle):** A interface terá um interruptor (Toggle) de "Deduzir Impostos (12,15%)". Quando ativado, e se a conta for configurada como pré-paga ou pós-paga, o sistema deduz 12,15% (PIS/Cofins e ISS) do orçamento bruto, fazendo os cálculos de mídia apenas sobre o valor líquido.
2. **Lógica de Benchmarks e Maturidade da Campanha:** 
   - A matemática roda 100% no navegador (Vue.js). O cálculo baseia-se em:
     - *Impressões* = (Orçamento / CPM) * 1000.
     - *Cliques* = Impressões * CTR.
     - *Leads* = Cliques * Taxa de conversão.
     - *CPA* = Orçamento / Conversões.
   - **Multiplicador de Maturidade:** A lógica do Vue.js deve ajustar as métricas base (CTR e Conversão) dependendo do nível da campanha:
     - *Iniciante:* Reduz o CTR e a Taxa de Conversão base em 15%.
     - *Intermediário:* Mantém a média (0% de alteração).
     - *Avançado:* Aumenta o CTR e a Taxa de Conversão base em 10%.
   - **Fator Objetivo:** O objetivo da campanha altera o foco das métricas (ex: "Alcance" foca em CPM otimizado, "Tráfego" foca em CPC/CTR, "Leads/Conversões" focam na Taxa de Conversão).
3. **Projeção em 3 Cenários (Reatividade em Tempo Real):** Apresentar as métricas em cenários: Conservador, Realista e Otimista (variando CTR e Taxa de Conversão para cima e para baixo). Ao arrastar *sliders* ou mudar inputs, o Vue.js recalcula tudo instantaneamente sem sobrecarregar o servidor.
### 3.1. Detalhamento da Regra 3: Ajuste Tributário 2026 (OBRIGATÓRIO)
Esta regra é mandatória para a precisão do simulador e deve preceder qualquer outro cálculo de mídia.
- **Parâmetro de Imposto:** Retenção fixa de 12,15% (PIS/Cofins e ISS).
- **Lógica de Aplicação:** 1. O sistema recebe o `Orçamento_Bruto` do usuário.
    2. Deve haver um campo (Toggle/Switch) para ativar "Deduzir Impostos Retidos".
    3. Se ativo: `Orçamento_Líquido = Orçamento_Bruto * (1 - 0.1215)`.
    4. **Regra de Ouro:** Todos os cálculos subsequentes (Impressões, Cliques, Leads, Conversões) devem utilizar obrigatoriamente o `Orçamento_Líquido` como base, e não o valor bruto inserido.
- **Exibição na UI:** O resumo de resultados deve mostrar claramente o valor que será efetivamente investido em mídia após a dedução tributária.
4. **Alertas Inteligentes:** Gatilhos condicionais no frontend (ex: "Orçamento baixo para a região" ou "Público alcançável pequeno") baseados na população cruzada com a penetração de 70% a 80% das redes sociais.
5. **Módulo de Administração e Importação de Dados:**
Uma área restrita (Painel Admin) onde o administrador do sistema pode fazer o upload de arquivos .csv ou .xlsx.
O sistema deve ler a planilha e atualizar automaticamente as tabelas segments (Benchmarks por Segmento) e regions (Inteligência de Região).
A interface deve fornecer um "Template de Planilha" para download, garantindo que o administrador preencha as colunas com os nomes exatos que o banco de dados espera.
6. **Suporte PWA (Progressive Web App):** - A aplicação deve ser instalável em desktops (Chrome/Edge) e dispositivos móveis (iOS/Android).
   - O frontend deve incluir a geração automática de um `manifest.json` com nome, descrição, cor de tema (`#0668E1`) e ícones.
   - Deve possuir um Service Worker básico configurado para realizar o cache de assets estáticos, garantindo carregamento rápido em conexões lentas e exibição da interface mesmo offline.

## 4. Modelagem do Banco de Dados (MySQL)
O banco de dados relacional deve conter as seguintes tabelas principais:

- **`users`**: `id`, `name`, `email`, `password`, `role` (enum: 'user', 'admin', valor padrão 'user'), `created_at`, `updated_at`.
- **`segments` (Benchmarks por Segmento)**: 
  - `id`, `name` (ex: Saúde, Imobiliário).
  - `avg_ctr` (%).
  - `avg_cpc` (R$).
  - `avg_conversion_rate` (%).
- **`regions` (Inteligência de Região)**: 
  - `id`, `name` (Estado/Município).
  - `total_population`.
  - `reachable_audience` (70-80% da população).
  - `avg_cpm` (R$).
- **`simulations` (Histórico)**: 
  - `id`, `user_id`, `budget`, `payment_type`, `campaign_days`, `region_id`, `segment_id`, `goal`, **`maturity_level`**.
  - `results_json` (armazenar os cálculos finais para histórico).
  - `created_at`.

## 5. API e Rotas (Laravel)
O backend funcionará como uma API RESTful enxuta consumida pelo Vue.js, servindo dados base e armazenando o histórico.

**Rotas de Autenticação (Sanctum ou JWT):**
- `POST /api/register` - Criar conta grátis.
- `POST /api/login` - Autenticação de usuário.
- `POST /api/logout` - Encerrar sessão.

**Rotas da Aplicação:**
- `GET /api/parameters` - Retorna a lista completa de `segments` (com seus benchmarks) e `regions` (com população e CPM). O Vue.js deve chamar essa rota **apenas uma vez** ao carregar a página e guardar no estado (ex: Pinia/Vuex).
- `POST /api/simulations` - Recebe o payload do painel com os resultados que o Vue.js já calculou e salva no banco de dados para compor o histórico do usuário.
- `GET /api/simulations` - Retorna o histórico de simulações do usuário autenticado.

**Rotas de Administração:**
- `POST /api/admin/import/segments` - Recebe o arquivo CSV/Excel de segmentos e faz o upsert (atualiza ou insere) no banco de dados. (Sugestão para a IA: Usar a biblioteca Maatwebsite/Laravel-Excel ou o parser nativo do PHP para ler CSV).
- `POST /api/admin/import/regions` - Recebe a planilha do IBGE/Meta com as regiões, população e CPM, atualizando a base.

## 6. Estratégia de Deploy (Produção VPS + CloudPanel)
O aplicativo será hospedado em uma VPS com CloudPanel usando o GitHub para Integração Contínua (CI/CD) simples.

1. **Configuração CloudPanel:**
   - Criar um novo site PHP/Laravel no painel do CloudPanel.
   - Configurar o banco de dados MySQL diretamente pelo CloudPanel e guardar as credenciais.
   - Apontar o *Document Root* para o diretório `/public` do Laravel.
2. **Integração com GitHub:**
   - Gerar uma chave SSH no servidor VPS e adicioná-la aos *Deploy Keys* do repositório no GitHub.
   - Configurar um script de deploy no CloudPanel (aba *Deployment* ou webhook via GitHub Actions) para que, a cada push na *main*, o servidor execute:
     ```bash
     git pull origin main
     composer install --no-dev --optimize-autoloader
     npm install
     npm run build
     php artisan migrate --force
     php artisan config:cache
     php artisan route:cache
     ```

## 7. Fases de Execução do Projeto (Para Agentes de IA)
Para não sobrecarregar o contexto e economizar tokens, o projeto deve ser executado seguindo estritamente as fases abaixo.
⚠️ **Regra de Execução:** A IA deve OBRIGATORIAMENTE ativar as skills listadas em cada fase antes de escrever qualquer linha de código.

- **FASE 1 e 2: Infraestrutura, Banco de Dados e Modelagem**
  - **Skills Obrigatórias:** `@laravel-expert`, `@database-architect`
  - Inicializar projeto Laravel e configurar ambiente Docker (Laravel Sail) com MySQL.
  - Criar Migrations para as tabelas `users`, `segments`, `regions` e `simulations`.
  - Criar Models e relacionamentos garantindo normalização e índices para alta performance.
  - Criar Seeders para popular `segments` e `regions` com dados iniciais.

- **FASE 3: Backend da API e Lógica de Negócios**
  - **Skills Obrigatórias:** `@laravel-expert`, `@uncle-bob-craft` (Clean Code)
  - Implementar Autenticação (Login/Register).
  - Criar o `SimulationController` com os endpoints `GET /api/parameters` e `POST /api/simulations`.
  - Aplicar a **Regra 3 (Impostos 2026)** no cálculo. O código deve ser modular, separando a lógica matemática em um *Service* dedicado.

- **FASE 4: Setup do Frontend (Vue.js PWA) e Motor de Cálculo Local**
  - **Skills Obrigatórias:** `@vue3-composition-api`, `@tailwind-master`
  - Instalar Vue.js e Tailwind CSS. Configurar o projeto como PWA utilizando Vite (manifest, ícones, service worker).
  - Ingerir as regras visuais do arquivo `DESIGN.md`.
  - Criar o Store no Vue.js (Pinia) para centralizar a lógica matemática e os multiplicadores de maturidade (-15%, 0%, +10%).

- **FASE 5 e 5.5: UI/UX Reativa e Módulo Admin**
  - **Skills Obrigatórias:** `@vue3-composition-api`, `@frontend-accessibility`
  - **Fonte de UI (Obrigatório):** Ler a pasta `_references/stitch/` e extrair as classes Tailwind dos HTMLs para converter em componentes Vue limpos (Login, Dashboard e Admin).
  - Integrar os sliders e botões com a loja Pinia para reatividade em tempo real.
  - Backend Admin: Criar middleware IsAdmin e endpoints de upload CSV.
  - Frontend Admin: Criar tela de Drag & Drop para planilhas.

- **FASE 6: Segurança, Auditoria e Deploy**
  - **Skills Obrigatórias:** `@security-review`, `@web-performance`
  - Analisar endpoints de upload CSV contra injeção de arquivos maliciosos.
  - Verificação da Auditoria PWA (Lighthouse) para garantir requisitos de instalação.
  - Compilar assets (`./vendor/bin/sail npm run build`).