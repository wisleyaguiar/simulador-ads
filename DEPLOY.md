Com a sua VPS rodando CloudPanel e o domínio já apontado, você está a poucos passos de ter o **Simulador Ads** no ar. 

Uma distinção técnica muito importante antes de começarmos: embora você tenha usado o Docker (Laravel Sail) no ambiente de desenvolvimento local (o que é perfeito), **em produção no CloudPanel, a melhor prática é usar a opção "Criar um site PHP" nativa**. O CloudPanel foi construído exatamente para gerenciar o Nginx, o PHP-FPM e os certificados SSL de forma nativa e ultra-rápida, dispensando a sobrecarga de rodar containers Docker na VPS para projetos Laravel padrão.

Aqui está o passo a passo assertivo para subir a aplicação hoje e a versão atualizada do seu `DEPLOY.md` para configurar o CI/CD (Deploy Automático via GitHub).

---

### PASSO 1: Configuração no CloudPanel (A Base)
1. Acesse o CloudPanel e clique em **Add Site** > **Create a PHP Site**.
2. **Domain Name:** `simuladorads.agenciatobe.com.br`.
3. **PHP Version:** Escolha a versão que você usou no projeto (provavelmente 8.2 ou 8.3).
4. **Vhost / Document Root:** ⚠️ **MUITO IMPORTANTE!** O padrão do CloudPanel é a pasta `htdocs`. Para o Laravel, você deve adicionar `/public` no final. O caminho correto deve ficar: `simuladorads.agenciatobe.com.br/htdocs/public`.
5. Clique em **Create**.

### PASSO 2: Banco de Dados e SSL (Segurança do PWA)
1. Vá no menu esquerdo do CloudPanel em **Databases** e clique em **Add Database**.
2. Crie o banco de dados (ex: `simulador_ads_db`), um usuário e uma senha forte. **Anote esses dados**.
3. Vá no menu **SSL/TLS**, aba **Let's Encrypt**, e clique em **Issue Certificate**. Isso é obrigatório para que o Service Worker do seu PWA funcione no navegador dos usuários.

### PASSO 3: O Primeiro Deploy Manual (Via SSH)
O CloudPanel cria um usuário SSH isolado para cada site. Vamos usá-lo para clonar o repositório.

1. No CloudPanel, vá em **SSH/FTP** e crie uma senha para o usuário do site (geralmente o nome de usuário é parecido com o domínio, ex: `simuladorads`).
2. Acesse sua VPS via Terminal (SSH) usando este usuário: 
   `ssh agenciatobe-simuladorads@147.93.66.129`
3. Navegue até a pasta raiz:
   `cd htdocs`
4. Apague a pasta padrão que o CloudPanel cria e clone o seu repositório:
   ```bash
   rm -rf *
   git clone git@github.com:wisleyaguiar/simulador-ads.git .
   ```
5. Configure o ambiente:
   ```bash
   cp .env.example .env
   nano .env
   ```
6. Dentro do `.env`, altere:
   * `APP_ENV=production`
   * `APP_DEBUG=false`
   * `APP_URL=https://simuladorads.agenciatobe.com.br`
   * Coloque os dados do Banco de Dados que você anotou no Passo 2.
   * Salve e saia (`Ctrl+O`, `Enter`, `Ctrl+X`).
7. Instale tudo e gere as chaves:
   ```bash
   composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev
   npm ci
   npm run build
   php artisan key:generate
   php artisan storage:link
   php artisan migrate --force
   ```

Se a tela de login abrir no seu domínio agora, o MVP está vivo!

---

### PASSO 4: O Novo DEPLOY.md (Deploy Automático via GitHub)

A forma mais profissional de configurar o deploy automático é usando o **GitHub Actions**. Toda vez que você der um `git push` na branch `main` do seu computador, o GitHub vai acessar o CloudPanel via SSH e rodar os comandos de atualização sozinho.

[cite_start]Para isso, substitua todo o conteúdo do seu atual `DEPLOY.md` [cite: 1] por este novo abaixo:

```markdown
# Deploy Contínuo: Simulador Ads (CloudPanel + GitHub Actions)

Este documento descreve a arquitetura de CI/CD (Continuous Integration / Continuous Deployment) da aplicação no CloudPanel.

## 1. Configurando o GitHub Actions (O Segredo da Automação)
Para que o GitHub atualize a VPS automaticamente ao receber novos códigos, utilizamos uma Action de SSH.

### A. Preparar os Secrets no GitHub
Vá no repositório do GitHub em **Settings > Secrets and variables > Actions** e adicione as seguintes `Repository secrets`:
* `HOST`: O endereço IP da sua VPS.
* `USERNAME`: O usuário SSH do site no CloudPanel (ex: `simuladorads`).
* `PASSWORD`: A senha SSH desse usuário (ou a `SSH_KEY` privada, caso tenha configurado chaves públicas).
* `PORT`: `22` (Ou a porta SSH customizada da sua VPS).

### B. Criar o Workflow
No código fonte do projeto (no seu Mac), crie o seguinte arquivo: `.github/workflows/deploy.yml` e cole o código abaixo:

```yaml
name: Deploy Automático para CloudPanel

on:
  push:
    branches:
      - main # O deploy roda sempre que houver push na main

jobs:
  deploy:
    runs-on: ubuntu-latest
    steps:
      - name: Executando Deploy via SSH
        uses: appleboy/ssh-action@master
        with:
          host: ${{ secrets.HOST }}
          username: ${{ secrets.USERNAME }}
          password: ${{ secrets.PASSWORD }}
          port: ${{ secrets.PORT }}
          script: |
            cd htdocs
            
            # Puxa as atualizações do Git (descartando edições locais acidentais)
            git fetch --all
            git reset --hard origin/main
            
            # Atualiza o backend (PHP) sem pacotes de dev
            composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev
            
            # Atualiza o frontend (Vue.js / PWA)
            npm ci --legacy-peer-deps
            npm run build
            
            # Roda as migrações de banco (se houver)
            php artisan migrate --force
            
            # Limpa e otimiza o cache geral da aplicação para máxima performance
            php artisan config:cache
            php artisan route:cache
            php artisan view:cache
            php artisan event:cache
```

## 2. Acesso Administrativo Inicial (Tinker)
Se precisar criar a conta Master-Admin após o banco de dados estar limpo na produção:

1. Acesse o CloudPanel via SSH com o usuário do site.
2. Navegue até o root (`cd htdocs`).
3. Rode `php artisan tinker` e execute:

```php
\App\Models\User::firstOrCreate(
    ['email' => 'wisley@tobe.ppg.br'],
    [
        'name' => 'Wisley Aguiar',
        'password' => \Illuminate\Support\Facades\Hash::make('Tobe!@#13net'),
        'role' => 'admin'
    ]
);
```

## 3. Checklist de Auditoria Pós-Deploy
- [ ] O arquivo `.env` na VPS possui `APP_ENV=production` e `APP_DEBUG=false`.
- [ ] A URL acessada possui o cadeado verde (Certificado Let's Encrypt ativo no CloudPanel), necessário para o PWA.
- [ ] O Vhost Document Root no CloudPanel termina em `/htdocs/public`.
- [ ] O comando `php artisan storage:link` foi rodado ao menos uma vez para garantir exibição de arquivos.
```

### O que você ganha com este novo fluxo?
A partir de agora, toda vez que o Antigravity ajustar um componente Vue, consertar um bug ou adicionar a funcionalidade da *sazonalidade* que arquitetamos, basta você fazer o commit e o push.

O GitHub Action vai entrar na sua VPS silenciosamente, puxar o código novo, rodar o Node (compilando os assets novos do PWA e otimizando o Tailwind) e limpar os caches do Laravel. Em 30 segundos, seu cliente já estará vendo a versão nova no ar sem você encostar no painel da hospedagem.