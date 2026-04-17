# Feature Specification: Simulador Ads MVP

**Feature Branch**: `001-simulador-ads-mvp`
**Created**: 2026-04-16
**Status**: Draft
**Input**: User description: "Simulador Ads MVP — Calculadora de cenários de campanhas Meta Ads com matriz de maturidade, dedução tributária 2026 e painel administrativo de importação de benchmarks."

## User Scenarios & Testing *(mandatory)*

### User Story 1 — Simulação de Campanha com Cenários (Priority: P1)

Um gestor de tráfego acessa o Dashboard, preenche os parâmetros da campanha (orçamento, região, segmento, objetivo e nível de maturidade) e obtém instantaneamente projeções de métricas em 3 cenários: Conservador, Realista e Otimista.

**Why this priority**: É o núcleo do produto. Sem a calculadora de cenários, o Simulador Ads não entrega valor algum. Toda receita e retenção dependem desta funcionalidade.

**Independent Test**: Inserir R$ 5.000 de orçamento com segmento "E-commerce Moda", região "Sudeste", objetivo "Tráfego" e maturidade "Intermediário". Os 3 cards de cenário DEVEM exibir valores distintos de Impressões, Cliques, Leads e CPA, todos recalculados instantaneamente ao arrastar sliders.

**Acceptance Scenarios**:

1. **Given** o usuário está autenticado e na tela de Dashboard, **When** seleciona segmento, região, objetivo e maturidade, e insere R$ 10.000 de orçamento, **Then** os 3 cards de cenário (Conservador, Realista, Otimista) são renderizados com métricas distintas em menos de 200ms.
2. **Given** o cenário Realista está visível, **When** o usuário altera o orçamento de R$ 10.000 para R$ 5.000, **Then** todos os valores dos 3 cenários são recalculados em tempo real sem recarregar a página.
3. **Given** o nível de maturidade é "Iniciante", **When** o mesmo orçamento e segmento são usados, **Then** o CTR e Taxa de Conversão exibidos DEVEM ser 15% menores do que o cenário com maturidade "Intermediário".
4. **Given** maturidade "Avançado", **When** comparado com "Intermediário", **Then** CTR e Taxa de Conversão DEVEM ser 10% maiores.

---

### User Story 2 — Dedução Tributária 2026 (Priority: P1)

O gestor ativa o toggle "Deduzir Impostos (12,15%)" e todos os cálculos de mídia passam a usar o `Orçamento_Líquido` como base, exibindo claramente a diferença entre o valor bruto e o valor efetivamente investido.

**Why this priority**: A precisão tributária é obrigatória para o mercado brasileiro em 2026. Simulações sem essa dedução geram projeções infladas e perda de confiança do usuário.

**Independent Test**: Com toggle desativado e orçamento R$ 10.000, os cálculos usam R$ 10.000. Ao ativar o toggle, o orçamento base DEVE mudar para R$ 8.785,00 (10.000 × 0.8785) e todas as métricas DEVEM ser proporcionalmente menores.

**Acceptance Scenarios**:

1. **Given** o toggle de impostos está desativado, **When** o usuário insere R$ 10.000, **Then** o orçamento utilizado nos cálculos é R$ 10.000,00.
2. **Given** o toggle de impostos está ativado, **When** o usuário insere R$ 10.000, **Then** o card de resumo exibe: Orçamento Bruto = R$ 10.000, Dedução = R$ 1.215, Orçamento Líquido = R$ 8.785.
3. **Given** o toggle é ativado, **When** os cenários são recalculados, **Then** Impressões = (8.785 / CPM) × 1000 (e não 10.000 / CPM × 1000).

---

### User Story 3 — Alertas Inteligentes (Priority: P2)

O sistema exibe banners de alerta contextuais baseados na combinação orçamento × região × segmento, como "Orçamento abaixo do mínimo recomendado" ou sugestões de otimização de período.

**Why this priority**: Diferencia o produto de calculadoras simples, mas não é bloqueante para o MVP funcional.

**Independent Test**: Inserir um orçamento de R$ 500 para segmento "E-commerce Moda" na região "Sudeste" e verificar se o alerta "Orçamento Crítico" é exibido.

**Acceptance Scenarios**:

1. **Given** o orçamento líquido é inferior ao CPM × 1000 mínimo para o segmento selecionado, **When** os resultados são calculados, **Then** um banner amarelo "Orçamento Crítico" é exibido na seção de Alertas.
2. **Given** o período escolhido é inferior a 14 dias, **When** os resultados são exibidos, **Then** uma sugestão de "Otimização de Período" é exibida informando potencial redução de CPA.

---

### User Story 4 — Autenticação e Registro (Priority: P2)

Um novo usuário acessa a tela de Login/Registro, cria uma conta gratuita e é redirecionado ao Dashboard. Usuários existentes fazem login com e-mail e senha.

**Why this priority**: Necessária para proteger dados e habilitar histórico por usuário, mas a lógica de cálculo pode ser validada antes da autenticação estar completa.

**Independent Test**: Criar conta com e-mail válido → Login com credenciais → Acesso ao Dashboard → Logout → Tentativa de acessar Dashboard sem autenticação resulta em redirecionamento ao Login.

**Acceptance Scenarios**:

1. **Given** o visitante está na tela de registro, **When** preenche nome, e-mail e senha válidos e clica em "Criar conta grátis", **Then** a conta é criada e o usuário é redirecionado ao Dashboard.
2. **Given** o usuário tem conta existente, **When** insere e-mail e senha corretos, **Then** é autenticado e redirecionado ao Dashboard.
3. **Given** o usuário não está autenticado, **When** tenta acessar qualquer rota protegida, **Then** é redirecionado para a tela de Login.

---

### User Story 5 — Painel Admin: Importação de Benchmarks (Priority: P2)

O administrador acessa o painel Admin, faz upload de planilhas CSV/Excel para atualizar os benchmarks de segmentos e dados de regiões. O sistema valida colunas obrigatórias, exibe feedback de sucesso ou erro, e atualiza o banco de dados.

**Why this priority**: Sem dados atualizados de benchmarks, as simulações ficam desatualizadas. Porém, dados iniciais podem ser inseridos via Seeders.

**Independent Test**: Fazer upload de um CSV com 50 segmentos válidos → Mensagem "Sucesso! 50 segmentos atualizados" exibida. Fazer upload de CSV sem coluna obrigatória → Mensagem de erro exibida com a coluna faltante.

**Acceptance Scenarios**:

1. **Given** o admin está na tela de importação, **When** faz upload de CSV de segmentos com todas as colunas obrigatórias (name, avg_ctr, avg_cpc, avg_conversion_rate), **Then** o sistema realiza upsert e exibe banner verde com contagem de registros atualizados.
2. **Given** o CSV de regiões não contém a coluna `avg_cpm`, **When** o upload é processado, **Then** um banner vermelho é exibido: "Erro: Colunas obrigatórias (avg_cpm) ausentes na planilha."
3. **Given** o admin clica em "Baixar Template Padrão", **When** o download é concluído, **Then** o CSV contém exatamente os cabeçalhos que o sistema espera.

---

### User Story 6 — Histórico de Simulações (Priority: P3)

O usuário autenticado pode salvar simulações realizadas e consultá-las posteriormente em uma lista de histórico.

**Why this priority**: Funcionalidade de valor, mas a proposta central (simular) funciona sem persistência de histórico.

**Independent Test**: Realizar uma simulação → Clicar em "Salvar" → Navegar para o Histórico → A simulação salva aparece listada com data, orçamento e cenário Realista.

**Acceptance Scenarios**:

1. **Given** o usuário completou uma simulação, **When** clica em salvar, **Then** os parâmetros e resultados calculados são persistidos no banco vinculados ao seu usuário.
2. **Given** o usuário tem simulações salvas, **When** acessa a tela de Histórico, **Then** vê uma lista ordenada por data com orçamento, segmento e métricas resumidas.

---

### User Story 7 — PWA: Instalação e Offline (Priority: P3)

O usuário pode instalar o simulador como aplicativo no desktop ou celular, e usar a calculadora de cenários offline (com dados previamente carregados).

**Why this priority**: Diferencial importante, mas o produto entrega valor imediato como web app mesmo sem PWA.

**Independent Test**: Acessar pelo Chrome → Botão "Instalar" aparece → Instalar → Desconectar internet → Acessar o app → A interface carrega e os cálculos funcionam com os dados cacheados.

**Acceptance Scenarios**:

1. **Given** o usuário acessa o app pelo navegador, **When** clica em "Instalar", **Then** o app é adicionado ao sistema com ícone, nome e cor de tema (#0668E1).
2. **Given** o app está instalado e os dados de segmentos/regiões foram cacheados, **When** a internet é desconectada, **Then** a interface carrega e os cálculos funcionam normalmente com dados locais.

---

### Edge Cases

- O que acontece quando o orçamento inserido é R$ 0 ou negativo? → Exibir validação impedindo cálculo.
- O que acontece quando o CPM da região selecionada é 0? → Exibir alerta de dados incompletos e impedir divisão por zero.
- Como lidar com CSV que contém linhas duplicadas no campo `name`? → Upsert por nome; a última linha prevalece.
- O que acontece se o usuário manipula o JSON do Service Worker para injetar dados falsos? → Os dados DEVEM ser validados pelo backend ao salvar simulações. O frontend é apenas para visualização.

## Requirements *(mandatory)*

### Functional Requirements

- **FR-001**: O sistema DEVE calcular Impressões, Cliques, Leads e CPA usando as fórmulas: Impressões = (Orçamento / CPM) × 1000; Cliques = Impressões × CTR; Leads = Cliques × Taxa de Conversão; CPA = Orçamento / Conversões.
- **FR-002**: O sistema DEVE aplicar multiplicadores de maturidade ao CTR e Taxa de Conversão base do segmento: Iniciante (-15%), Intermediário (0%), Avançado (+10%).
- **FR-003**: O sistema DEVE gerar 3 cenários simultâneos variando CTR e Taxa de Conversão: Conservador (base × 0.8), Realista (base × 1.0), Otimista (base × 1.3).
- **FR-004**: O sistema DEVE aplicar dedução tributária de 12,15% quando o toggle de impostos estiver ativo, calculando Orçamento_Líquido = Orçamento_Bruto × (1 - 0.1215).
- **FR-005**: Quando a dedução estiver ativa, TODOS os cálculos subsequentes DEVEM usar obrigatoriamente o Orçamento_Líquido.
- **FR-006**: O administrador DEVE poder fazer upload de CSV/Excel para atualizar benchmarks de segmentos e dados de regiões via upsert.
- **FR-007**: O sistema DEVE validar colunas obrigatórias no CSV antes de processar e exibir feedback claro de sucesso ou erro.
- **FR-008**: O sistema DEVE carregar segmentos e regiões do backend uma única vez (GET /api/parameters) e armazená-los localmente no estado da aplicação.
- **FR-009**: O sistema DEVE recalcular todos os cenários em tempo real (< 200ms) ao alterar qualquer parâmetro de entrada.
- **FR-010**: O sistema DEVE exibir alertas inteligentes baseados na combinação orçamento × região × segmento.
- **FR-011**: A aplicação DEVE ser instalável como PWA com manifest, ícones e Service Worker para cache offline.
- **FR-012**: O sistema DEVE fornecer templates de planilha para download com os cabeçalhos exatos esperados.

### Key Entities

- **User**: Representa a pessoa que utiliza o simulador. Possui nome, e-mail, senha e papel (usuário comum ou administrador).
- **Segment**: Um nicho de mercado (ex: Saúde, Imobiliário) com benchmarks médios de CTR, CPC e Taxa de Conversão.
- **Region**: Uma unidade geográfica (Estado/Município) com população total, público alcançável e CPM médio.
- **Simulation**: Um registro histórico de simulação contendo parâmetros de entrada, nível de maturidade e resultados calculados em JSON.

## Success Criteria *(mandatory)*

### Measurable Outcomes

- **SC-001**: O usuário consegue completar uma simulação completa (preenchimento de parâmetros + visualização de resultados) em menos de 30 segundos.
- **SC-002**: Ao alterar qualquer parâmetro, os 3 cenários são recalculados e re-renderizados em menos de 200ms de latência percebida.
- **SC-003**: A dedução tributária de 12,15% é aplicada corretamente em 100% dos casos quando o toggle está ativo, sem exceções.
- **SC-004**: O administrador consegue importar uma planilha de até 500 registros e receber feedback em menos de 5 segundos.
- **SC-005**: A aplicação obtém score mínimo de 90 na auditoria PWA do Lighthouse.
- **SC-006**: O simulador funciona offline com dados previamente cacheados, permitindo uso em campo sem internet.
- **SC-007**: 100% dos alertas inteligentes são disparados corretamente quando as condições de gatilho são satisfeitas.

## Assumptions

- Os usuários possuem conexão com internet para o primeiro acesso e carregamento de dados de segmentos/regiões.
- Os benchmarks de mercado (CTR, CPC, Taxa de Conversão) são atualizados periodicamente pelo administrador via planilhas.
- O fator de dedução tributária (12,15%) é fixo para o MVP e não requer configuração dinâmica.
- Os multiplicadores de variação por cenário (Conservador ×0.8, Realista ×1.0, Otimista ×1.3) são fixos para o MVP.
- O público alcançável é calculado como 70-80% da população total da região.
- O sistema de autenticação utiliza Laravel Sanctum com tokens de sessão.
- Os dados de segmentos e regiões iniciais são populados via Seeders no banco de dados.
- O objetivo da campanha (Tráfego, Leads, Conversões) altera pesos relativos nos benchmarks, mas as fórmulas base permanecem as mesmas.
