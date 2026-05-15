# Marcasite Cursos

Sistema de inscrições em cursos com pagamento via Stripe.

## Requisitos

- PHP 8.2+
- Composer
- Node.js 18+
- MySQL 8+

## Instalação

1. Clone o repositório e entre na pasta:

```bash
git clone <url-do-repo>
cd marcasite-cursos
```

2. Instale as dependências PHP:

```bash
composer install
```

3. Instale as dependências JS:

```bash
npm install
```

4. Copie o arquivo de ambiente:

```bash
cp .env.example .env
```

5. Gere a chave da aplicação:

```bash
php artisan key:generate
```

6. Configure o banco de dados no `.env`:

```
DB_DATABASE=marcasite_cursos
DB_USERNAME=root
DB_PASSWORD=sua_senha
```

7. Configure o Stripe no `.env`:

```
STRIPE_KEY=pk_test_sua_chave_publica
STRIPE_SECRET=sk_test_sua_chave_secreta
```

> As chaves de teste estao disponiveis no dashboard do Stripe em **Developers > API keys**, com o modo teste ativado.

8. Crie o banco de dados e rode as migrations + seeds:

```bash
php artisan migrate --seed
```

9. Compile os assets:

```bash
npm run build
```

10. Suba o servidor:

```bash
php artisan serve
```

Acesse em: http://localhost:8000

## Credenciais de acesso (admin)

- **E-mail:** admin@marcasite.com.br
- **Senha:** password

Área admin: http://localhost:8000/login

## Cartoes de teste (Stripe)

| Situacao | Numero | Validade | CVV |
|----------|--------|----------|-----|
| Pagamento aprovado | 4242 4242 4242 4242 | qualquer data futura | qualquer |
| Pagamento recusado | 4000 0000 0000 0002 | qualquer data futura | qualquer |
| Autenticacao necessaria | 4000 0025 0000 3155 | qualquer data futura | qualquer |

## Funcionalidades

- Inscricao publica em cursos com pagamento via Stripe
- Area administrativa com listagem de inscritos
- Busca por nome, e-mail, CPF e curso
- Filtro por status de pagamento e data
- Edicao e exclusao de inscricoes
- Exportacao para Excel e PDF
