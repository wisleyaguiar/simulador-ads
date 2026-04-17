# Deploy do Simulador Ads (CloudPanel)

## Visão Geral
Este documento cobre a rotina de deploy contínuo em produção para ambientes Linux (incluindo painéis de gerenciamento como CloudPanel) para o MVP do Simulador Ads.

## Requisitos de Servidor
- **PHP**: 8.5 (ou versão do Laravel 11/13 suportada)
- **Node.js**: v18/v20+ e NPM
- **Banco de Dados**: MySQL 8.x
- **Extensões**: bcmath, ctype, fileinfo, json, mbstring, openssl, pdo, tokenizer, xml

## Rotina de Deploy Básica

Você pode automatizar esse fluxo utilizando Git Hooks, GitHub Actions ou rodar manualmente via terminal do servidor.

No terminal do seu CloudPanel logado onde fica o root do seu domínio (`/htdocs/seu-dominio.com`):

```bash
# 1. Puxe as atualizações mais recentes do Git
git pull origin main

# 2. Instale as dependências do PHP 
# A flag --no-dev é crítica por questões de segurança (Impede pacotes de teste no servidor vivo)
composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev

# 3. Instale as dependências Frontend e gere a Build de Produção
npm ci # Mais seguro que npm install
npm run build 

# 4. Rodar Migrações do Banco de Dados
# É seguro rodar "--force" porque bloqueia avisos e requerimentos interativos no terminal
php artisan migrate --force

# 5. Otimização e Cache de Rotas e Configurações (Aumenta o desempenho até 40%)
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

## Criação do Admin em Produção
Se esta é a sua primeira vez configurando a instância e você precisar criar um acesso Master-Admin, rode o Tinker nativamente:

```bash
php artisan tinker
```
E digite:
```php
\App\Models\User::firstOrCreate(
    ['email' => 'admin@seu-dominio.com'],
    [
        'name' => 'Root Admin',
        'password' => \Illuminate\Support\Facades\Hash::make('SenhaForte123!'),
        'role' => 'admin'
    ]
);
```
Pressione Enter, caso retorne os dados, a conta logará com sucesso.

## Checklist de Segurança em Produção
- [ ] Confirme se o arquivo `.env` contém o parâmetro obrigatório `APP_ENV=production` e `APP_DEBUG=false`
- [ ] Confirme se seu domínio tem o certificado Let's Encrypt ativado no CloudPanel (necessário para Service Worker / aplicativo offline e PWA).
- [ ] Permita gravações nos logs do laravel garantindo a permissão correta `chmod -R 775 storage bootstrap/cache`
