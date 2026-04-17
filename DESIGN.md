# Sistema de Design: Simulador Ads Campaign Predictor

## 1. Visão Geral e Direção Criativa (A Estrela Guia)
Nossa Estrela Guia Criativa: **"O Arquiteto da Precisão"**

No mundo das ferramentas de marketing de alto nível, os dados muitas vezes podem parecer frios ou opressores. Nosso objetivo é transformar a complexa simulação de anúncios em uma experiência editorial que pareça tanto com uma revista de moda premium quanto com um SaaS orientado a dados. Vamos além do "painel de controle padrão" utilizando assimetria intencional — colocando métricas de destaque fora do centro para atrair os olhos — e aproveitando o amplo espaço em branco para dar aos dados espaço para "respirar". Esta não é apenas uma ferramenta; é um ambiente de alta performance onde cada pixel parece curado, não apenas posicionado.

## 2. Paleta de Cores e Tonalidade
A paleta tem raízes no azul característico da Meta (Facebook Ads), mas é elevada através de uma hierarquia sofisticada de luz e profundidade.

*   **Cor Primária Customizada:** `#0668E1`
*   **Fundo Principal (Background):** `#faf9ff`
*   **Superfícies Baixas (Surface Container Low):** `#f2f3fe`
*   **Superfícies Altas (Surface Container Lowest):** `#ffffff` (Cartões)
*   **Texto Principal:** `#191b23`
*   **Cor de Erro:** `#ba1a1a`
*   **Cor de Erro (Texto/Ícone):** `#ba1a1a`
*   **Cor de Erro (Fundo/Container):** `#ffdad6` (Vermelho pastel para banners)
*   **Cor de Sucesso (Texto/Ícone):** `#0f5223`
*   **Cor de Sucesso (Fundo/Container):** `#d8f5e1` (Verde pastel para banners)

### A Regra "Sem Linhas"
Para alcançar uma sensação premium, **bordas sólidas de 1px são proibidas para definir seções exclusivas**. Os limites devem ser definidos por meio de mudanças na cor de fundo (ex: usar `#f2f3fe` do container assente sobre um fundo base `#faf9ff` para separar componentes). 

### A Regra do "Vidro e Gradiente" (Glass & Gradient)
Para "Call to Actions" (CTAs) primários e cabeçalhos principais (Hero), utilize um gradiente linear sutil (135°) de azul escuro (`#0051b3`) para azul vivo (`#0668e1`). Para overlays "flutuantes" ou barras de navegação, implemente o efeito Glassmorphism: cor da superfície a 80% de opacidade atrelada ao `backdrop-blur: 20px`.

## 3. Tipografia Editorial
Combinamos a fonte geométrica **Manrope** para um impacto visual elegante com a **Inter** para facilitar a leitura.

*   **Display e Cabeçalhos (Manrope):** Usada para a voz de marca com alto impacto visual. Uma fonte ampla em `display-lg` ajuda na apresentação de resultados de simulação com elegância para criar momentos de prestígio.
*   **Corpo de Texto e Rótulos (Inter):** A Interstate é utilizada pela sua legibilidade na visualização matemática e de painéis cheios de números e métricas essenciais.
*   **Contraste Tipográfico:** Uma combinação moderna utiliza o emparelhamento do Manrope com os sub-rótulos em Inter (Maiúsculas, espaçamento 0.05em) - como a "legenda" clássica encontrada em painéis editoriais sofisticados.

## 4. Componentes e Estilização

### Elevação e Camadas Naturais (Sombras)
Para evitar o visual datado dos sistemas antigos (com bordas de sombra pesada), utilizamos definições baseadas em mudança tonal e sombras sutis para objetos flutuantes.

*   **Sombras de Pop-ups:** `box-shadow: 0 24px 48px -12px rgba(25, 27, 35, 0.08);` (A cor carrega um tom escuro do painel para refletir a iluminação natural, e não cinza desbotado).
*   **A "Borda Fantasma":** Caso algo exija acessibilidade rígida, uma borda muito leve (cor `#c2c6d6` com apenas 20% de opacidade) serve muito bem, unindo a página inteira.

### Botões Reativos
*   **Primários:** Cor do fundo em Gradiente radial/esférico (Azul escuro para Azul vivo), curvas em `xl` (3rem / 48px - muito redondos, do tipo formato de pílula) e `padding` de 2rem na horizontal.
*   **Interatividade (Hover):** Ao passar o mouse, a intensidade do gradiente sobe e os tons ficam mais fortes, sem utilizar escurecimento barato.

### Campos de Formulário (Inputs) e Interatividade
*   O fundo deve possuir um azul claro de surface sem borda agressiva. O ativo em foco é sinalizado com 100% de azul com limite de `2px` ao redor, preservando um design arredondado e macio.
*   Uso de cápsulas inteiramente pílulas (Raio de Pílula) nos Chips de seleção (filtros meta).

### Cartões Premium (O Container de Resultados "Vidro de Métrica")
*   Ao exibir o "Alcance da Campanha", o cartão atua como o **Centro das Atenções**. Construído em `#ffffff` e um `backdrop-blur` mínimo com uma borda semi-transparente mínima (10%). Fontes muito grandes combinadas para destacar o orçamento vs o alcance.
*   O arredondamento da interface como um todo gira no **Modelo XL**: as pontas tendem a possuir raios super macios e amigáveis, na escala entre 32px e 48px.

### Áreas de Upload (Drag & Drop)
* **Estilo Base:** Utiliza a cor de fundo secundária (`#f2f3fe`).
* **Borda Tracejada:** Aplicação direta da regra "Borda Fantasma" (Ghost Border), porém com estilo `dashed` (`border-dashed`), espessura de 2px e cor `#c2c6d6` para criar a área de "soltar" o arquivo.
* **Raio e Espaçamento:** Curvatura generosa (escala `xl` ou `2xl` do Tailwind) para manter a maciez da interface. Internamente, deve ter bastante "respiro" (padding elevado) para o ícone de nuvem e as instruções.

### Alertas e Feedback (Banners de Sucesso/Erro)
* Seguindo a regra "Sem Linhas", os banners de status abaixo da área de upload **não devem ter bordas**.
* A diferenciação ocorre apenas pelo contraste do fundo pastel (Verde para sucesso, Vermelho para erro) em relação ao cartão branco (`#ffffff`).
* Utilizar cantos arredondados (`rounded-xl` ou pílula) e tipografia Inter.

### Cartões de Resumo (Pills)
* Como visto na seção "Integridade do Banco", métricas rápidas de rodapé devem ser exibidas em cartões ultra-arredondados (`rounded-full` ou pílula), com fundo branco e sombras ambientais super difusas.

---

## 5. Práticas de Layout das Telas (Do's and Don'ts)

### O que fazer:
*   ✅ **Fazer:** Utilizar margens assimétricas em recursos visuais de marketing do ERP para deixar uma sensação "Feito sob medida" e quebras na estrutura em bloco tradicional da web.
*   ✅ **Fazer:** Textos secundários devem ser inseridos num grisalho suave (#424754) para criar contraste altivo.

### O que NÃO fazer:
*   ❌ **Não Fazer:** Letras pretas escuras. Em nenhuma circunstância o texto base deve ser puramente preto (`#000000`). Utiliza-se um tom escuro/carvão (Como `#191b23`).
*   ❌ **Não Fazer:** O vermelho em erros nunca deve ser vibrante. Caso apresente alguma recusa no Simulador de Tráfego, deve possuir as variações vermelhas do painel Meta que garantam calma para o usuário (`#ba1a1a`).
*   ❌ **Não Fazer:** Usar a divisória de '1px'. Para dividir duas opções de listas, a preferência é utilizar um respiro (White Space) de ~16px ao invés da divisão por linhas grossas que arruínam a arte.

## 6. Estrutura de Layout das Telas
* **Tela de Login (Split View):** Layout dividido em 50/50. O lado esquerdo é um painel visual (gradiente azul escuro com imagem/cartões de marketing) e o lado direito é um container limpo e branco centralizando o formulário.
* **Tela de Dashboard (Sidebar Flexível):** Um layout de aplicação moderna. À esquerda, um painel fixo/sticky branco com cantos arredondados contendo todos os inputs e parâmetros. À direita, uma área mais ampla (fundo levemente acinzentado/azulado) onde os resultados, cards de cenário e gráficos são renderizados.
* **Tela 3: Painel Admin (Importação de Dados):**
  - **Sidebar (Menu Principal):** Uma barra lateral fixa à esquerda, fina e limpa, com fundo branco ou cinza levíssimo, contendo os links de navegação e o ícone do sistema. Diferente do Dashboard do usuário, o menu admin é mais utilitário.
  - **Área de Conteúdo (Main):** Fundo base `background` (`#faf9ff`). O cabeçalho possui tipografia Manrope com destaque visual para a função atual.
  - **Grid de Importação:** Dois grandes cartões principais de importação (Segmentos e Regiões) dispostos lado a lado (50/50 em telas grandes) utilizando a elevação de cartão premium.
  - **Rodapé de Dados (Status):** Um container largo na base da tela agrupando mini-cartões (pills) com os dados de integridade atual do banco (Média CPM, Média CTR, etc.).