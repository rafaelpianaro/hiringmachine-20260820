# 🍳 HiringMachine — Documentação do Projeto para Entrevista

## 📋 Visão Geral

O **HiringMachine** é uma plataforma fullstack focada em receitas culinárias, desenvolvida como desafio técnico para demonstrar competências em desenvolvimento de APIs RESTful, autenticação JWT, testes automatizados e frontend moderno.

### Stack Tecnológica

| Camada | Tecnologia | Versão |
|--------|-----------|--------|
| **Backend** | Laravel (PHP) | ^13.0 |
| **Frontend** | Vue.js | ^3.4.21 |
| **Banco de Dados** | PostgreSQL | 16 |
| **Autenticação** | JWT (tymon/jwt-auth) | ^2.0 |
| **Estado Global** | Pinia | ^2.1.7 |
| **Estilo** | Tailwind CSS | ^3.4.3 |
| **Build Tool** | Vite | ^5.2.0 |
| **Containerização** | Docker + Docker Compose | - |
| **Testes** | PHPUnit | ^12.0 |

---

## 🏗️ Arquitetura do Sistema

```
┌─────────────────────────────────────────────────────────────┐
│                      Docker Environment                      │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────────┐  │
│  │   Laravel     │  │  PostgreSQL  │  │     Redis        │  │
│  │   App (:5757) │  │  (:5858)     │  │     (:6379)      │  │
│  │   + Nginx     │  │              │  │                  │  │
│  └──────┬───────┘  └──────────────┘  └──────────────────┘  │
│         │                                                    │
│  ┌──────┴───────┐  ┌──────────────┐                        │
│  │  Mailpit     │  │  Frontend    │                        │
│  │  (:8025)     │  │  Vue.js      │                        │
│  └──────────────┘  └──────────────┘                        │
└─────────────────────────────────────────────────────────────┘
```

### Estrutura de Diretórios

```
hiring-api/                          # Backend Laravel
├── app/
│   ├── Actions/                     # Ações de negócio (Action Pattern)
│   │   ├── UserLogin.php
│   │   ├── UserCreate.php
│   │   ├── UserChangePassword.php
│   │   ├── UserUpdateProfile.php
│   │   ├── RecipeList.php
│   │   ├── RecipeListByCategory.php
│   │   ├── RecipeListByDifficulty.php
│   │   └── RecipeRate.php
│   ├── Http/
│   │   ├── Controllers/Api/         # Controllers RESTful
│   │   │   ├── AuthController.php
│   │   │   ├── RecipeController.php
│   │   │   └── CommentController.php
│   │   └── Requests/                # Form Requests (validação)
│   ├── Models/                      # Eloquent Models
│   │   ├── User.php
│   │   ├── Recipe.php
│   │   ├── RecipeRating.php
│   │   └── Comment.php
│   └── Traits/
│       └── ApiResponser.php         # Trait para respostas JSON padronizadas
├── database/
│   ├── migrations/                  # Migrations focadas em recipes + auth
│   ├── seeders/                     # Dados de teste e receitas
│   └── database.sqlite              # DB para testes
├── routes/
│   └── api.php                      # Rotas da API
├── tests/Feature/                   # Testes de integração
│   ├── Auth/                        # Login, registro, refresh, logout, perfil
│   └── Recipe/                      # Lista, criação, atualização, avaliação, busca
├── docker-compose.yml               # 4 serviços
└── Dockerfile

hiring-front/                        # Frontend Vue.js
├── src/
│   ├── views/                       # 4 páginas
│   │   ├── HomeView.vue
│   │   ├── RecipeDetailView.vue
│   │   ├── RecipeEditView.vue
│   │   └── MyRecipesView.vue
│   ├── components/
│   │   ├── auth/LoginModal.vue      # Modal de login/registro
│   │   ├── recipes/                 # Componentes de receitas
│   │   │   ├── RecipeCard.vue
│   │   │   ├── RecipeGrid.vue
│   │   │   ├── RecipeForm.vue
│   │   │   ├── RecipeDetailModal.vue
│   │   │   └── StarRating.vue
│   │   ├── home/                    # 3 componentes
│   │   │   ├── Hero.vue
│   │   │   ├── CategoryGrid.vue
│   │   │   └── CtaSection.vue
│   │   └── layout/                  # Header + Footer
│   ├── stores/                      # 3 stores Pinia
│   │   ├── userStore.js
│   │   ├── recipeStore.js
│   │   └── toastStore.js
│   ├── services/                    # 2 services (API layer)
│   │   ├── authService.js
│   │   └── recipeService.js
│   ├── router/index.js              # Vue Router
│   └── App.vue
├── tailwind.config.js               # Design system customizado
└── vite.config.js                   # Proxy API configurado
```

---

## 🗃️ Modelo de Dados

### Diagrama de Entidades

```
┌─────────────┐       ┌─────────────────┐       ┌─────────────┐
│    Users     │──1:N──│    Recipes      │──1:N──│ RecipeRatings │
│             │       │                 │       │              │
│ id          │       │ id              │       │ id           │
│ name        │       │ user_id (FK)    │       │ recipe_id(FK)│
│ email       │       │ title           │       │ user_id(FK)  │
│ password    │       │ description     │       │ stars        │
│ role        │       │ ingredients     │       └──────────────┘
│ phone       │       │ instructions    │
│ company     │       │ prep_time       │       ┌─────────────┐
│ position    │       │ cook_time       │       │   Comments  │
│ avatar      │       │ servings        │       │             │
│ is_active   │       │ difficulty      │       │ id          │
└──────┬──────┘       │ category        │       │ user_id(FK) │
       │              │ image           │       │ recipe_id   │
       │              │ is_published    │       │ content     │
       │              │ created_at      │       │ rating      │
       │              └─────────────────┘       └─────────────┘
```

### Tabela `users`
- **Roles**: `admin`, `user`
- Senhas hasheadas com bcrypt
- Campo de perfil/ativo mantido para sessão e controle de acesso

### Tabela `recipes`
- `ingredients` e `instructions` armazenados como **JSON** (PostgreSQL)
- `difficulty`: enum (`easy`, `medium`, `hard`)
- `category`: string livre (Bolos, Carnes, Sobremesas, etc.)
- `created_by`/`user_id` para autoria da receita

### Tabela `recipe_ratings`
- **Constraint único**: `(recipe_id, user_id)` — um usuário avalia uma receita no máximo uma vez
- `stars`: integer (1-5)
- Média calculada no frontend e no payload da listagem da API

### Tabela `comments`
- Comentários e avaliação opcional da receita
- Relacionados diretamente com `recipes` e `users`

---

## 🔐 Autenticação & Autorização

### Fluxo de Autenticação (JWT)

```
┌──────────┐     POST /auth/login      ┌──────────┐
│  Client   │ ──────────────────────▶   │   API    │
│           │    {email, password}      │          │
│           │ ◀──────────────────────   │          │
│           │    {access_token, user}   │          │
│           │                           │          │
│           │  GET /recipes             │          │
│           │  Authorization: ******  │          │
│           │       <token>             │          │
└──────────┘                            └──────────┘
```

### Rotas Públicas vs Protegidas

**Públicas** (prefixo `/api/v1/`):
- `POST /auth/login` — Login
- `POST /auth/register` — Registro
- `GET /recipes` — Listar receitas
- `GET /recipes/{id}` — Detalhe da receita
- `GET /health` — Health check

**Protegidas** (requer `auth:api` middleware):
- `POST /auth/logout`, `POST /auth/refresh`, `GET /auth/me`
- `PUT /auth/profile`, `PUT /auth/password`
- CRUD da receita
- `POST /recipes/{id}/ratings` — Avaliar receita
- CRUD de comentários

### Regras de Autorização

| Ação | Quem pode fazer |
|------|-----------------|
| Criar receita | Qualquer usuário autenticado |
| Editar/Excluir receita | Apenas o autor da receita |
| Avaliar receita | Qualquer usuário autenticado, exceto o autor |
| Excluir comentário | Autor do comentário ou admin |

---

## 🛠️ Padrões de Design & Boas Práticas

### 1. Actions (Command Pattern)
Cada operação de negócio complexa é encapsulada em uma classe `Action`:

```php
final readonly class RecipeRate
{
    public function handle(Recipe $recipe, int $userId, int $stars): array
    {
        if ($recipe->user_id === $userId) {
            throw new \RuntimeException('Você não pode avaliar sua própria receita.');
        }

        return DB::transaction(function () use ($recipe, $userId, $stars): array {
            $recipe->ratings()->updateOrCreate(
                ['user_id' => $userId],
                ['stars' => $stars]
            );

            return [
                'average' => $recipe->fresh()->averageRating(),
                'count' => $recipe->fresh()->ratings()->count(),
            ];
        });
    }
}
```

**Vantagens:**
- Separação de responsabilidades (Single Responsibility)
- Fácil teste unitário
- Reutilização entre controllers
- Transações de banco centralizadas

### 2. Trait `ApiResponser`
Padroniza todas as respostas da API:

```php
trait ApiResponser
{
    protected function successResponse($data, string $message = 'Sucesso', int $code = 200): JsonResponse
    protected function errorResponse(string $message, int $code = 400, $errors = null): JsonResponse
    protected function notFoundResponse(string $message): JsonResponse
    protected function forbiddenResponse(string $message): JsonResponse
    protected function unauthorizedResponse(string $message): JsonResponse
}
```

Formato padrão de resposta:
```json
{
    "status": "success|error",
    "message": "Mensagem descritiva",
    "data": { ... }
}
```

### 3. Form Requests (Validação)
Controllers usam Form Requests para validação automática:

```php
class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $user = (new UserLogin)->handle($request->email, $request->password);
    }
}
```

### 4. Frontend — Arquitetura de Stores (Pinia)

```
┌─────────────────────────────────────────┐
│              userStore.js                │
│  - currentUser (ref)                    │
│  - token (ref)                          │
│  - isLoggedIn (computed)                │
│  - login(), register(), logout()        │
│  - Persistência: localStorage           │
└─────────────────────────────────────────┘

┌─────────────────────────────────────────┐
│             recipeStore.js              │
│  - recipes (ref)                        │
│  - myRecipes (ref)                      │
│  - filteredRecipes (computed)           │
│  - fetchRecipes(), createRecipe()       │
│  - rateRecipe()                         │
└─────────────────────────────────────────┘

┌─────────────────────────────────────────┐
│             toastStore.js               │
│  - show(message, type)                  │
│  - Auto-dismiss após 3 segundos         │
└─────────────────────────────────────────┘
```

### 5. Service Layer (Frontend)
Separação entre lógica de API e estado:

```javascript
// recipeService.js — chamadas HTTP
export const recipeService = {
  async getAllRecipes() { ... },
  async createRecipe(formData) { ... },
  async rateRecipe(recipeId, stars) { ... }
}

// recipeStore.js — gerenciamento de estado
export const useRecipeStore = defineStore('recipe', () => {
  async function rateRecipe(recipeId, stars) {
    const updated = await recipeService.rateRecipe(recipeId, stars)
    const index = recipes.value.findIndex(r => r.id === recipeId)
    if (index >= 0) recipes.value[index] = { ...recipes.value[index], ...updated }
  }
})
```

---

## 🌐 API — Resumo dos Endpoints

### Auth (6 endpoints)
| Método | Rota | Descrição |
|--------|------|-----------|
| `POST` | `/auth/login` | Login com JWT |
| `POST` | `/auth/register` | Novo usuário |
| `POST` | `/auth/logout` | Invalidar token |
| `POST` | `/auth/refresh` | Renovar token |
| `GET` | `/auth/me` | Perfil do usuário |
| `PUT` | `/auth/profile` | Atualizar perfil |
| `PUT` | `/auth/password` | Alterar senha |

### Recipes (8 endpoints)
| Método | Rota | Descrição |
|--------|------|-----------|
| `GET` | `/recipes` | Listar receitas (com filtros) |
| `GET` | `/recipes/{id}` | Detalhe da receita |
| `GET` | `/recipes/category/{cat}` | Por categoria |
| `GET` | `/recipes/difficulty/{d}` | Por dificuldade |
| `POST` | `/recipes` | Criar receita (auth) |
| `PUT` | `/recipes/{id}` | Atualizar receita (auth) |
| `DELETE` | `/recipes/{id}` | Excluir receita (auth) |
| `POST` | `/recipes/{id}/ratings` | Avaliar receita (auth) |
| `GET` | `/recipes/my-recipes` | Minhas receitas |

### Comments (4 endpoints)
| Método | Rota | Descrição |
|--------|------|-----------|
| `GET` | `/recipes/{id}/comments` | Listar comentários |
| `POST` | `/recipes/{id}/comments` | Criar comentário |
| `PUT` | `/comments/{id}` | Atualizar comentário |
| `DELETE` | `/comments/{id}` | Excluir comentário |

---

## 🐳 Infraestrutura Docker

### Serviços

| Serviço | Imagem | Porta | Função |
|---------|--------|-------|--------|
| `app` | Custom (Dockerfile) | 5757 | Laravel + Nginx |
| `pgsql` | postgres:16-alpine | 5858 | Banco de dados |
| `redis` | redis:7-alpine | 6379 | Cache/Fila |
| `mailpit` | axllent/mailpit | 8025 (UI) | Captura de emails |

### Comandos Úteis

```bash
# Setup completo do zero
./up-from-zero.sh

# Iniciar ambiente
docker-compose up -d

# Rodar migrations
docker-compose exec app php artisan migrate

# Rodar testes
docker-compose exec app php artisan test

# Logs em tempo real
docker-compose logs -f
```

---

## 💡 Decisões Técnicas & Justificativas

### 1. JWT vs Session-based Auth
**Escolha**: JWT (tymon/jwt-auth)
**Justificativa**: API stateless, fácil integração com frontend SPA, suporte a refresh tokens.

### 2. JSON para Arrays no PostgreSQL
**Escolha**: Campos `ingredients` e `instructions` como JSON
**Justificativa**: PostgreSQL suporta JSON nativamente, simplifica o modelo, permite queries com `@>` e `?` operators.

### 3. Actions Pattern
**Escolha**: Extrair lógica de negócio em classes Action
**Justificativa**: Controllers finos, testes unitários mais simples, reutilização de lógica.

### 4. Pinia vs Vuex
**Escolha**: Pinia
**Justificativa**: API mais simples, TypeScript-friendly, recomendado pelo time Vue.js.

### 5. Prefixo Único da API (/api/v1/)
**Escolha**: Manter apenas `/api/v1/`
**Justificativa**: URL consistente, menos ambiguidade, documentação mais limpa e frontend mais previsível.

---

## 📊 Dados de Teste (Seed)

| Usuário | Email | Role | Senha |
|---------|-------|------|-------|
| Rafael Pianaro | rafaelpianaro@mail.com | admin | password |
| Ana Souza | ana.souza@email.com | user | password |
| Carlos Lima | carlos.lima@email.com | user | password |
| Fernanda Rocha | fernanda.rocha@email.com | user | password |

O seed inclui receitas, comentários, avaliações e perfis de usuários para validar listagem, filtros e rating.

---

## 🔑 Pontos Fortes para Destacar em Entrevista

1. **Testes Automatizados**: cobertura de auth, receitas, busca, comentários e ratings
2. **Segurança**: validação server-side, autorização por ownership, proteção contra auto-avaliação
3. **Design Patterns**: Actions, Traits, Service Layer, Form Requests
4. **Frontend Moderno**: Composition API, Pinia, Tailwind CSS, modal e grid responsivos
5. **Docker**: ambiente reproduzível com 4 serviços (app, postgres, redis, mailpit)
6. **API RESTful**: padronização de respostas, filtros por categoria/dificuldade e payloads enriquecidos
7. **Validação Completa**: backend + frontend com tratamento granular de erros
8. **Experiência do Usuário**: estrelas, avaliação persistente e sincronização do estado após rate

---

## 🚀 Possíveis Melhorias

- [ ] Implementar upload de imagens com S3/Cloudflare R2
- [ ] Adicionar cache com Redis para consultas frequentes
- [ ] Implementar fila para envio de emails
- [ ] Adicionar rate limiting na API
- [ ] Implementar testes E2E com Playwright
- [ ] CI/CD com GitHub Actions
- [ ] Monitoramento com Sentry
- [ ] Documentação interativa com Swagger/OpenAPI
