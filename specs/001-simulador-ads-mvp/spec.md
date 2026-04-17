# Feature Specification: Simulador Ads MVP (v2)

**Feature Branch**: `001-simulador-ads-mvp`
**Created**: 2026-04-16
**Updated**: 2026-04-16 (v2 — Sazonalidade, Confidence Score, Saúde dos Dados)
**Status**: Draft
**Input**: PRD.md v2 + DESIGN.md + Constitution v1.0.0

## User Scenarios & Testing *(mandatory)*

### User Story 1 — Simulação de Campanha com Cenários (Priority: P1)

Um gestor de tráfego acessa o Dashboard, preenche os parâmetros da campanha (orçamento, região, segmento, objetivo, nível de maturidade e mês previsto) e obtém instantaneamente projeções de métricas em 3 cenários: Conservador, Realista e Otimista.

**Why this priority**: É o núcleo do produto. Sem a calculadora de cenários, o Simulador Ads não entrega valor algum. Toda receita e retenção dependem desta funcionalidade.

**Independent Test**: Inserir R$ 5.000 de orçamento com segmento "E-commerce Moda", região "Sudeste", objetivo "Tráfego", maturidade "Intermediário" e mês "Março". Os 3 cards de cenário DEVEM exibir valores distintos de Impressões, Cliques, Leads e CPA, todos recalculados instantaneamente ao arrastar sliders.

**Acceptance Scenarios**:

1. **Given** o usuário está autenticado e na tela de Dashboard, **When** seleciona segmento, região, objetivo, maturidade e mês, e insere R$ 10.000 de orçamento, **Then** os 3 cards de cenário (Conservador, Realista, Otimista) são renderizados com métricas distintas em menos de 200ms.
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
3. **Given** o toggle é ativado, **When** os cenários são recalculados, **Then** Impressões = (8.785 / CPM_ajustado) × 1000 (e não 10.000 / CPM × 1000).

---

### User Story 3 — Sazonalidade do Leilão (Priority: P1)

O gestor seleciona o "Mês Previsto da Campanha" e o motor de cálculo ajusta automaticamente os custos de CPM e CPC com multiplicadores sazonais que refletem as oscilações reais do leilão do Meta Ads (Black Friday, Natal, Ressaca Comercial).

**Why this priority**: A sazonalidade impacta diretamente a precisão das projeções. Ignorar o aumento de custo de novembro (Black Friday) gera simulações que subestimam o CPA real em até 50%.

**Independent Test**: Com orçamento R$ 10.000, segmento "E-commerce Moda" (CPM base R$ 25), região "SP":
- Mês "Março" → CPM usado = R$ 25,00 (×1.0)
- Mês "Novembro" → CPM usado = R$ 37,50 (×1.5), Impressões menores
- Mês "Janeiro" → CPM usado = R$ 21,25 (×0.85), Impressões maiores

**Acceptance Scenarios**:

1. **Given** o mês selecionado é "Março" (ou qualquer mês neutro), **When** o cálculo é processado, **Then** o CPM e CPC usados são os valores base sem multiplicador (×1.0).
2. **Given** o mês selecionado é "Novembro", **When** o cálculo é processado, **Then** o CPM efetivo = CPM_base × 1.5 e o CPC efetivo = CPC_base × 1.5, resultando em menos Impressões e Cliques para o mesmo orçamento.
3. **Given** o mês selecionado é "Dezembro", **When** o cálculo é processado, **Then** o multiplicador aplicado é 1.3 (CPM e CPC +30%).
4. **Given** o mês selecionado é "Janeiro", **When** o cálculo é processado, **Then** o multiplicador aplicado é 0.85 (CPM e CPC -15%), resultando em mais Impressões e Cliques.
5. **Given** o toggle de impostos está ativado E o mês é "Novembro", **When** os cenários são calculados, **Then** AMBAS as regras são aplicadas: o orçamento usa o valor líquido E o CPM/CPC usam o multiplicador 1.5.

---

### User Story 4 — Alertas Inteligentes (Priority: P2)

O sistema exibe banners de alerta contextuais baseados na combinação orçamento × região × segmento × mês, como "Orçamento abaixo do mínimo recomendado", sugestões de período e avisos de sazonalidade intensa.

**Why this priority**: Diferencia o produto de calculadoras simples, mas não é bloqueante para o MVP funcional.

**Independent Test**: Inserir um orçamento de R$ 500 para segmento "E-commerce Moda" na região "Sudeste" e verificar se o alerta "Orçamento Crítico" é exibido. Selecionar mês "Novembro" e verificar alerta "Sazonalidade Intensa".

**Acceptance Scenarios**:

1. **Given** o orçamento líquido é inferior ao CPM_ajustado × 1000 × 3 do segmento selecionado, **When** os resultados são calculados, **Then** um banner amarelo "Orçamento Crítico" é exibido.
2. **Given** o período escolhido é inferior a 14 dias, **When** os resultados são exibidos, **Then** uma sugestão de "Otimização de Período" é exibida.
3. **Given** o mês selecionado é Novembro ou Dezembro, **When** os resultados são exibidos, **Then** um banner de "Sazonalidade Intensa" é exibido informando o fator de multiplicação ativo.

---

### User Story 5 — Autenticação e Registro (Priority: P2)

Um novo usuário acessa a tela de Login/Registro, cria uma conta gratuita e é redirecionado ao Dashboard. Usuários existentes fazem login com e-mail e senha.

**Why this priority**: Necessário para proteger dados e habilitar histórico por usuário, mas a lógica de cálculo pode ser validada antes da autenticação estar completa.

**Independent Test**: Criar conta com e-mail válido → Login com credenciais → Acesso ao Dashboard → Logout → Tentativa de acessar Dashboard sem autenticação resulta em redirecionamento ao Login.

**Acceptance Scenarios**:

1. **Given** o visitante está na tela de registro, **When** preenche nome, e-mail e senha válidos e clica em "Criar conta grátis", **Then** a conta é criada e o usuário é redirecionado ao Dashboard.
2. **Given** o usuário tem conta existente, **When** insere e-mail e senha corretos, **Then** é autenticado e redirecionado ao Dashboard.
3. **Given** o usuário não está autenticado, **When** tenta acessar qualquer rota protegida, **Then** é redirecionado para a tela de Login.

---

### User Story 6 — Painel Admin: Importação de Benchmarks e Saúde dos Dados (Priority: P2)

O administrador acessa o painel Admin, faz upload de planilhas CSV/Excel para atualizar os benchmarks de segmentos e dados de regiões (incluindo Confidence Score). O sistema valida colunas obrigatórias, exibe feedback, e atualiza o banco de dados. Adicionalmente, o painel exibe a "Saúde dos Dados" com alertas de atualização e média de confiabilidade.

**Why this priority**: Sem dados atualizados e confiáveis, as simulações são imprecisas. O Confidence Score e os alertas de atualização permitem ao admin monitorar a qualidade dos dados.

**Independent Test**: 
1. Upload de CSV com 50 segmentos válidos (incluindo `confidence_score`) → "Sucesso! 50 segmentos atualizados". 
2. Upload de CSV sem coluna `confidence_score` → Erro exibido.
3. Painel "Saúde dos Dados": se `segments.updated_at` > 90 dias, alerta "Dados de Segmentos Desatualizados" exibido.

**Acceptance Scenarios**:

1. **Given** o admin está na tela de importação, **When** faz upload de CSV de segmentos com todas as colunas obrigatórias (name, avg_ctr, avg_cpc, avg_conversion_rate, confidence_score), **Then** o sistema realiza upsert e exibe banner verde com contagem de registros atualizados.
2. **Given** o CSV de regiões não contém a coluna `confidence_score`, **When** o upload é processado, **Then** um banner vermelho é exibido: "Erro: Colunas obrigatórias (confidence_score) ausentes na planilha."
3. **Given** o admin clica em "Baixar Template Padrão", **When** o download é concluído, **Then** o CSV contém exatamente os cabeçalhos que o sistema espera (incluindo confidence_score).
4. **Given** a tabela `segments` não é atualizada há mais de 90 dias, **When** o admin acessa o painel, **Then** o painel "Saúde dos Dados" exibe um alerta amarelo "Dados de Segmentos desatualizados (última atualização há X dias)".
5. **Given** a média de `confidence_score` dos segmentos é inferior a 0.5, **When** o admin acessa o painel, **Then** o painel exibe um alerta vermelho "Confiabilidade dos dados de Segmentos abaixo do aceitável (média: 0.XX)".
6. **Given** os dados estão atualizados e a média de confidence_score é ≥ 0.7, **When** o admin acessa o painel, **Then** o painel "Saúde dos Dados" exibe status verde "Dados saudáveis".

---

### User Story 7 — Histórico de Simulações (Priority: P3)

O usuário autenticado pode salvar simulações realizadas (incluindo mês selecionado e multiplicador de sazonalidade) e consultá-las posteriormente em uma lista de histórico.

**Why this priority**: Funcionalidade de valor, mas a proposta central (simular) funciona sem persistência de histórico.

**Independent Test**: Realizar simulação com mês "Novembro" → Salvar → Navegar para Histórico → A simulação salva aparece com data, orçamento, segmento, mês e cenário Realista.

**Acceptance Scenarios**:

1. **Given** o usuário completou uma simulação, **When** clica em salvar, **Then** os parâmetros (incluindo mês e multiplicador sazonal) e resultados calculados são persistidos no banco vinculados ao seu usuário.
2. **Given** o usuário tem simulações salvas, **When** acessa a tela de Histórico, **Then** vê uma lista ordenada por data com orçamento, segmento, mês da campanha e métricas resumidas.

---

### User Story 8 — PWA: Instalação e Offline (Priority: P3)

O usuário pode instalar o simulador como aplicativo no desktop ou celular, e usar a calculadora de cenários offline (com dados previamente carregados).

**Why this priority**: Diferencial importante, mas o produto entrega valor imediato como web app mesmo sem PWA.

**Independent Test**: Acessar pelo Chrome → Botão "Instalar" → Instalar → Desconectar internet → A interface carrega e os cálculos funcionam com os dados cacheados.

**Acceptance Scenarios**:

1. **Given** o usuário acessa o app pelo navegador, **When** clica em "Instalar", **Then** o app é adicionado ao sistema com ícone, nome e cor de tema (#0668E1).
2. **Given** o app está instalado e os dados de segmentos/regiões foram cacheados, **When** a internet é desconectada, **Then** a interface carrega e os cálculos funcionam normalmente com dados locais.

---

### Edge Cases

- **Orçamento zero ou negativo**: Exibir validação impedindo cálculo.
- **CPM da região = 0**: Exibir alerta de dados incompletos e impedir divisão por zero.
- **CPM/CPC com sazonalidade extrema**: Se o multiplicador sazonal resultar em CPM > limite do orçamento, exibir alerta "Orçamento insuficiente para o período selecionado".
- **CSV com linhas duplicadas no campo `name`**: Upsert por nome; a última linha prevalece.
- **CSV com `confidence_score` fora do range 0.0–1.0**: Rejeitar a linha e informar no feedback.
- **Manipulação do Service Worker**: Dados DEVEM ser validados pelo backend ao salvar simulações. O frontend é apenas para visualização.
- **Dados nunca importados (tabela vazia)**: O painel Saúde dos Dados deve exibir alerta crítico "Sem dados" e impedir simulação.

## Requirements *(mandatory)*

### Functional Requirements

- **FR-001**: O sistema DEVE calcular Impressões, Cliques, Leads e CPA usando as fórmulas: Impressões = (Orçamento / CPM_ajustado) × 1000; Cliques = Impressões × CTR; Leads = Cliques × Taxa de Conversão; CPA = Orçamento / Conversões.
- **FR-002**: O sistema DEVE aplicar multiplicadores de maturidade ao CTR e Taxa de Conversão base do segmento: Iniciante (-15%), Intermediário (0%), Avançado (+10%).
- **FR-003**: O sistema DEVE gerar 3 cenários simultâneos variando CTR e Taxa de Conversão: Conservador (base × 0.8), Realista (base × 1.0), Otimista (base × 1.3).
- **FR-004**: O sistema DEVE aplicar dedução tributária de 12,15% quando o toggle de impostos estiver ativo, calculando Orçamento_Líquido = Orçamento_Bruto × (1 - 0.1215).
- **FR-005**: Quando a dedução estiver ativa, TODOS os cálculos subsequentes DEVEM usar obrigatoriamente o Orçamento_Líquido.
- **FR-006**: O sistema DEVE aplicar multiplicadores de sazonalidade sobre o CPM e CPC base antes dos cálculos de cenário: Novembro (×1.5), Dezembro (×1.3), Janeiro (×0.85), demais meses (×1.0).
- **FR-007**: O pipeline de cálculo DEVE seguir esta ordem estrita: (1) Dedução tributária → (2) Ajuste de sazonalidade no CPM/CPC → (3) Multiplicador de maturidade no CTR/ConvRate → (4) Geração dos 3 cenários.
- **FR-008**: O administrador DEVE poder fazer upload de CSV/Excel para atualizar benchmarks de segmentos e dados de regiões via upsert. As planilhas DEVEM incluir a coluna `confidence_score`.
- **FR-009**: O sistema DEVE validar colunas obrigatórias no CSV (incluindo `confidence_score`) antes de processar e exibir feedback claro.
- **FR-010**: O sistema DEVE carregar segmentos e regiões do backend uma única vez (GET /api/parameters) e armazená-los localmente no estado da aplicação.
- **FR-011**: O sistema DEVE recalcular todos os cenários em tempo real (< 200ms) ao alterar qualquer parâmetro de entrada, incluindo o mês da campanha.
- **FR-012**: O sistema DEVE exibir alertas inteligentes baseados na combinação orçamento × região × segmento × sazonalidade.
- **FR-013**: A aplicação DEVE ser instalável como PWA com manifest, ícones e Service Worker para cache offline.
- **FR-014**: O sistema DEVE fornecer templates de planilha para download com os cabeçalhos exatos esperados (incluindo confidence_score).
- **FR-015**: O painel Admin DEVE exibir um módulo "Saúde dos Dados" com: (a) última data de atualização de cada tabela, (b) alerta se a tabela não for atualizada dentro do prazo esperado (segments: 90 dias, regions: 180 dias), (c) média do confidence_score por tabela.
- **FR-016**: O campo `confidence_score` DEVE ser um decimal entre 0.0 e 1.0 em ambas as tabelas (segments e regions).

### Key Entities

- **User**: Nome, e-mail, senha e papel (user/admin).
- **Segment**: Nicho de mercado com benchmarks médios (CTR, CPC, Taxa de Conversão) e `confidence_score` indicando confiabilidade da amostra. Requer atualização trimestral.
- **Region**: Unidade geográfica com população, público alcançável, CPM médio e `confidence_score`. Requer atualização semestral/anual.
- **Simulation**: Registro histórico contendo parâmetros de entrada (incluindo mês da campanha e multiplicador sazonal aplicado), nível de maturidade e resultados calculados em JSON.

## Success Criteria *(mandatory)*

### Measurable Outcomes

- **SC-001**: O usuário consegue completar uma simulação (com seleção de mês sazonal) em menos de 30 segundos.
- **SC-002**: Ao alterar qualquer parâmetro (incluindo mês), os 3 cenários são recalculados e re-renderizados em menos de 200ms.
- **SC-003**: A dedução tributária de 12,15% é aplicada corretamente em 100% dos casos quando o toggle está ativo.
- **SC-004**: Os multiplicadores de sazonalidade são aplicados corretamente ao CPM e CPC em 100% dos meses (Novembro ×1.5, Dezembro ×1.3, Janeiro ×0.85, demais ×1.0).
- **SC-005**: O administrador consegue importar uma planilha de até 500 registros (com confidence_score) e receber feedback em menos de 5 segundos.
- **SC-006**: A aplicação obtém score mínimo de 90 na auditoria PWA do Lighthouse.
- **SC-007**: O simulador funciona offline com dados previamente cacheados.
- **SC-008**: 100% dos alertas inteligentes (incluindo sazonalidade intensa) são disparados corretamente quando as condições de gatilho são satisfeitas.
- **SC-009**: O painel "Saúde dos Dados" indica corretamente tabelas desatualizadas (segments > 90 dias, regions > 180 dias) e a média de confidence_score.

## Assumptions

- Os usuários possuem conexão com internet para o primeiro acesso e carregamento de dados.
- Os benchmarks são atualizados periodicamente: segmentos a cada 90 dias, regiões a cada 180 dias.
- O fator de dedução tributária (12,15%) é fixo para o MVP.
- Os multiplicadores de sazonalidade (Nov ×1.5, Dec ×1.3, Jan ×0.85) são fixos para o MVP e não requerem configuração dinâmica.
- Os multiplicadores de variação por cenário (Conservador ×0.8, Realista ×1.0, Otimista ×1.3) são fixos para o MVP.
- O público alcançável é calculado como 70-80% da população total da região.
- O sistema de autenticação utiliza Laravel Sanctum com tokens de sessão.
- Os dados de segmentos e regiões iniciais são populados via Seeders.
- O `confidence_score` nos Seeders iniciais é definido como 0.7 (valor padrão razoável para dados estimados).
- O pipeline de cálculo segue a ordem: Tributação → Sazonalidade → Maturidade → Cenários.
