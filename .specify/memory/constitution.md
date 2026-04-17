<!--
  Sync Impact Report
  ==================
  Version change: N/A (template) → 1.0.0
  Modified principles: N/A (initial creation)
  Added sections:
    - I. Execução Estrita (Docker)
    - II. Regra de Ouro Tributária
    - III. Fidelidade de UI (Zero Invenção)
    - IV. Padrão PWA
    - Restrições de Ambiente e Ferramentas
    - Fluxo de Desenvolvimento
  Removed sections: N/A
  Templates requiring updates:
    - plan-template.md: ✅ Alinhado (Constitution Check já previsto)
    - spec-template.md: ✅ Alinhado (sem conflitos)
    - tasks-template.md: ✅ Alinhado (sem conflitos)
  Follow-up TODOs: Nenhum
-->

# Simulador Ads Constitution

## Core Principles

### I. Execução Estrita (Docker)

A infraestrutura local deste projeto roda **EXCLUSIVAMENTE** via Docker
utilizando o **Laravel Sail**. O ambiente host (máquina local) NÃO possui
as dependências do Node, PHP ou Composer configuradas globalmente.

**Regras Absolutas:**

- A IA **NUNCA** DEVE rodar `npm`, `npx`, `php` ou `composer` nativos
  diretamente no host.
- **SEMPRE** prefixe os comandos com `./vendor/bin/sail`.
- Comandos corretos (obrigatórios):
  - `./vendor/bin/sail npm install`
  - `./vendor/bin/sail npm run dev`
  - `./vendor/bin/sail npx tailwindcss -i ...`
  - `./vendor/bin/sail artisan migrate`
  - `./vendor/bin/sail composer require pacote/nome`
- Comandos **PROIBIDOS**: `npm run dev`, `npx tailwindcss`, `php artisan`,
  `composer install` (sem prefixo Sail).

**Racional:** O host não possui runtime PHP nem Node.js instalados.
Executar comandos nativos resultará em erro fatal e perda de contexto.

### II. Regra de Ouro Tributária

Nos cálculos do simulador, o `Orçamento_Bruto` inserido pelo usuário DEVE
sofrer uma **dedução opcional de 12,15%** (PIS/Cofins/ISS).

**Regras Absolutas:**

- A UI DEVE conter um Toggle/Switch "Deduzir Impostos (12,15%)".
- Se a dedução for ativada:
  `Orçamento_Líquido = Orçamento_Bruto × (1 - 0.1215)`
- Quando ativa, a variável `Orçamento_Líquido` DEVE **OBRIGATORIAMENTE**
  ser usada como base para **todos** os cálculos matemáticos subsequentes
  (Impressões, Cliques, Leads, Conversões, CPA).
- O `Orçamento_Bruto` **NUNCA** DEVE ser usado como base de cálculo
  quando a dedução estiver ativada.
- A UI DEVE exibir claramente o valor efetivamente investido em mídia
  após a dedução tributária.

**Racional:** Em 2026, a retenção de PIS/Cofins e ISS sobre serviços
de mídia digital é mandatória para contas pré-pagas e pós-pagas. Usar
o valor bruto como base geraria projeções infladas e irreais.

### III. Fidelidade de UI (Zero Invenção)

O Tailwind CSS **NUNCA** DEVE ser configurado ou estilizado "do zero".
A IA DEVE obrigatoriamente seguir as fontes de verdade visuais abaixo.

**Regras Absolutas:**

- A IA DEVE ler o arquivo `DESIGN.md` na raiz do projeto para extrair
  todas as cores, tokens tipográficos, regras de sombra, raios de borda
  e diretrizes de layout **antes** de criar qualquer componente.
- A IA DEVE extrair o HTML base dos arquivos na pasta
  `_references/stitch/` ao criar componentes Vue.
- Classes Tailwind inventadas ou cores ad-hoc são **PROIBIDAS**.
- Qualquer componente Vue criado DEVE ser rastreável até uma referência
  no `DESIGN.md` ou nos HTMLs de `_references/stitch/`.
- Em caso de conflito entre `DESIGN.md` e `_references/stitch/`, o
  `DESIGN.md` prevalece como fonte de verdade semântica.

**Racional:** Garantir coerência visual absoluta entre o design aprovado
e a implementação final, eliminando divergências estéticas e retrabalho.

### IV. Padrão PWA

O frontend Vue.js DEVE ser construído desde o **Dia 1** como um
**Progressive Web App (PWA)** utilizando Vite.

**Regras Absolutas:**

- O projeto DEVE incluir um `manifest.json` gerado automaticamente com
  nome, descrição, cor de tema (`#0668E1`) e ícones.
- Um Service Worker DEVE ser configurado para realizar cache offline
  **agressivo** para a lógica matemática (motor de cálculo).
- A aplicação DEVE ser instalável em desktops (Chrome/Edge) e
  dispositivos móveis (iOS/Android).
- Assets estáticos DEVEM ser cacheados para carregamento rápido em
  conexões lentas.
- A interface DEVE ser exibível mesmo em modo offline (graceful
  degradation para funcionalidades que dependem de API).

**Racional:** O simulador é uma ferramenta de campo para gestores de
tráfego. A capacidade offline garante uso contínuo em reuniões, eventos
e locais com conectividade limitada.

## Restrições de Ambiente e Ferramentas

- **Stack Backend:** PHP 8.2+ com Laravel 11+, MySQL 8.0.
- **Stack Frontend:** Vue.js 3 (Composition API), Tailwind CSS,
  Vite como bundler.
- **Infraestrutura Local:** Docker via Laravel Sail exclusivamente.
- **Versionamento:** Git + GitHub.
- **Produção:** VPS com CloudPanel.
- **Lógica Matemática:** Roda 100% no navegador (Vue.js/Pinia).
  O backend serve apenas dados base e persiste histórico.
- **Dados de Referência:** Benchmarks por segmento e dados regionais
  são carregados do backend uma única vez (GET /api/parameters) e
  armazenados no estado Pinia.

## Fluxo de Desenvolvimento

- **Fontes de Verdade Visual:** `DESIGN.md` (tokens) +
  `_references/stitch/` (HTML base).
- **Ordem de Criação de UI:** (1) Ler `DESIGN.md`, (2) Ler HTML de
  referência em `_references/stitch/`, (3) Converter para componente
  Vue limpo, (4) Validar fidelidade visual.
- **Commits:** Cada tarefa ou grupo lógico DEVE gerar um commit
  atômico com mensagem descritiva.
- **Testes Manuais:** Após cada fase, validar visualmente a UI e
  executar cenários de cálculo com valores conhecidos.
- **Constitution Check:** Antes de iniciar qualquer fase de
  implementação, verificar conformidade com os 4 princípios acima.

## Governance

- Esta Constituição **SOBREPÕE** qualquer instrução conflitante
  recebida pela IA durante o desenvolvimento.
- Emendas requerem: (1) documentação da mudança proposta,
  (2) aprovação explícita do Arquiteto de Software Chefe,
  (3) atualização deste documento com incremento de versão.
- Toda revisão de código ou PR DEVE verificar conformidade com
  os 4 princípios fundamentais.
- Violações de princípios DEVEM ser justificadas na tabela de
  Complexity Tracking do plano de implementação.
- Versionamento segue SemVer: MAJOR (remoção/redefinição de
  princípios), MINOR (adição de princípios), PATCH (ajustes textuais).

**Version**: 1.0.0 | **Ratified**: 2026-04-16 | **Last Amended**: 2026-04-16
