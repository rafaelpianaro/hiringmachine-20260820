# 🍳 HiringMachine — Documentação do Projeto para Entrevista

## 📋 Visão Geral

O **HiringMachine** é uma plataforma fullstack que combina dois domínios distintos em uma única aplicação: um **sistema de vagas de emprego** (hiring) e uma **plataforma de receitas culinárias**. O projeto foi desenvolvido como um desafio técnico, demonstrando competências em desenvolvimento de APIs RESTful, autenticação JWT, testes automatizados e frontend moderno.

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
│   ├── Actions/                     # Ações de negócio (Command Pattern)
│   │   ├── UserLogin.php
│   │   ├── UserCreate.php
│   │   ├── UserChangePassword.php
│   │   ├── UserUpdateProfile.php
│   │   └── RecipeRate.php
│   ├── Http/
│   │   ├── Controllers/Api/         # Controllers RESTful
│   │   │   ├── AuthController.php
│   │   │   ├── RecipeController.php
│   │   │   ├── JobController.php
│   │   │   ├── ApplicationController.php
│   │   │   └── CommentController.php
│   │   ├── Middleware/
│   │   │   └── CheckRole.php        # Middleware de autorização por role
│   │   └── Requests/                # Form Requests (validação)
│   ├── Models/                      # Eloquent Models
│   │   ├── User.php
│   │   ├── Recipe.php
│   │   ├── RecipeRating.php
│   │   ├── Job.php
│   │   ├── Application.php
│   │   └── Comment.php
│   └── Traits/
│       └── ApiResponser.php         # Trait para respostas JSON padronizadas
├── database/
│   ├── migrations/                  # 7 migrations
│   ├── seeders/                     # Dados de teste
│   └── database.sqlite              # DB para testes
├── routes/
│   └── api.php                      # Rotas da API
├── tests/Feature/                   # Testes de integração
│   ├── Auth/                        # 5 arquivos de teste
│   ├── Recipe/                      # 1 arquivo (13 testes)
│   ├── Job/                         # 1 arquivo (12 testes)
│   └── Application/                 # 1 arquivo (8 testes)
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
│   │   ├── recipes/                 # 5 componentes
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
│    Users     │──1:N──│      Jobs       │──1:N──│ Applications │
│             │       │                │       │             │
│ id          │       │ id             │       │ id          │
│ name        │       │ user_id (FK)   │       │ user_id(FK) │
│ email       │       │ title          │       │ job_id (FK) │
│ password    │       │ description    │       │ status      │
│ role        │       │ salary_min     │       │ cover_letter│
│ phone       │       │ salary_max     │       │ resume_path │
│ company     │       │ location       │       │ notes       │
│ position    │       │ remote         │       │ applied_at  │
│ avatar      │       │ type           │       └─────────────┘
│ is_active   │       │ status         │
└──────┬──────┘       │ company_name   │
       │              │ deadline       │
       │              └─────────────────┘
       │
       │──1:N──┌─────────────────┐──1:N──┌─────────────┐
       │       │    Recipes      │       │   Comments   │
       │       │                │       │             │
       │       │ id             │       │ id          │
       │       │ user_id (FK)   │       │ user_id(FK) │
       │       │ title          │       │ recipe_id   │
       │       │ description    │       │ content     │
       │       │ ingredients    │       │ rating      │
       │       │ instructions   │       └─────────────┘
       │       │ prep_time      │
       │       │ cook_time      │       ┌─────────────────┐
       │       │ servings       │──1:N──│ RecipeRatings   │
       │       │ difficulty     │       │                 │
       │       │ category       │       │ id              │
       │       │ image          │       │ recipe_id (FK)  │
       │       │ is_published   │       │ user_id (FK)    │
       │       └─────────────────┘       │ stars (1-5)     │
       │                                 └─────────────────┘
```

### Tabela `users`
- **Roles**: `admin`, `recruiter`, `candidate`, `user`
- Senhas hasheadas com bcrypt
- Campo `is_active` para desativação de contas

### Tabela `recipes`
- `ingredients` e `instructions` armazenados como **JSON** (PostgreSQL)
- `difficulty`: enum (`easy`, `medium`, `hard`)
- `category`: string livre (Bolos, Carnes, Sobremesas, etc.)

### Tabela `recipe_ratings`
- **Constraint único**: `(recipe_id, user_id)` — um usuário só avalia uma receita uma vez
- `stars`: tiny integer (1-5)

### Tabela `applications`
- **Constraint único**: `(user_id, job_id)` — impede candidatura duplicada
- Status: `pending`, `reviewed`, `accepted`, `rejected`

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
│           │  Authorization: Bearer ▶  │          │
│           │       <token>             │          │
└──────────┘                            └──────────┘
```

### Rotas Públicas vs Protegidas

**Públicas** (prefixo `/api/hm/` e `/api/v1/`):
- `POST /auth/login` — Login
- `POST /auth/register` — Registro
- `GET /jobs` — Listar vagas
- `GET /recipes` — Listar receitas
- `GET /recipes/{id}` — Detalhe da receita
- `GET /health` — Health check

**Protegidas** (requer `auth:api` middleware):
- `POST /auth/logout`, `POST /auth/refresh`, `GET /auth/me`
- `PUT /auth/profile`, `PUT /auth/password`
- CRUD completo de vagas, receitas, candidaturas e comentários
- `POST /recipes/{id}/ratings` — Avaliar receita

### Regras de Autorização

| Ação | Quem pode fazer |
|------|-----------------|
| Criar vaga | Qualquer usuário autenticado |
| Editar/Excluir vaga | Apenas o criador da vaga |
| Candidatar-se | Candidatos (não dono da vaga) |
| Alterar status de candidatura | Apenas o dono da vaga |
| Criar receita | Qualquer usuário autenticado |
| Editar/Excluir receita | Apenas o autor da receita |
| Avaliar receita | Qualquer usuário (exceto o autor) |
| Excluir comentário | Autor do comentário ou admin |

---

## 🛠️ Padrões de Design & Boas Práticas

### 1. Actions (Command Pattern)
Cada operação de negócio complexa é encapsulada em uma classe `Action`:

```php
// App/Actions/RecipeRate.php
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
            // ... retorna payload formatado
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
        // Validação já feita pelo LoginRequest
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
│  - fieldErrors (para validação)         │
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
  async function createRecipe(formData) {
    fieldErrors.value = null
    try {
      const newRecipe = await recipeService.createRecipe(formData)
      recipes.value.unshift(newRecipe)
      return newRecipe
    } catch (e) {
      error.value = e.message
      fieldErrors.value = e.fieldErrors || null
      throw e
    }
  }
})
```

---

## 🧪 Testes

### Estrutura de Testes

```
tests/Feature/
├── Auth/
│   ├── LoginTest.php          (6 testes)
│   ├── RegisterTest.php       (8 testes)
│   ├── LogoutTest.php         (3 testes)
│   ├── RefreshTest.php
│   └── ProfileTest.php
├── Recipe/
│   └── RecipeTest.php         (13 testes)
├── Job/
│   └── JobTest.php            (12 testes)
└── Application/
    └── ApplicationTest.php    (8 testes)
```

### Exemplos de Testes

**Teste de Login:**
```php
public function test_user_can_login_with_valid_credentials()
{
    $response = $this->postJson('/api/v1/auth/login', [
        'email' => 'lucas.costa@email.com',
        'password' => 'password',
    ]);

    $response->assertStatus(200)
        ->assertJsonStructure([
            'status', 'data' => [
                'access_token', 'token_type', 'expires_in',
                'user' => ['id', 'name', 'email', 'role'],
            ],
        ]);
}
```

**Teste de Autorização:**
```php
public function test_user_cannot_update_other_users_recipe()
{
    $user = User::where('email', 'lucas.costa@email.com')->first();
    $token = JWTAuth::fromUser($user);
    $recipe = Recipe::where('user_id', '!=', $user->id)->first();

    $response = $this->withHeader('Authorization', "Bearer $token")
        ->putJson("/api/v1/recipes/{$recipe->id}", [
            'title' => 'Hacked Recipe',
        ]);

    $response->assertStatus(403);
}
```

**Teste de Regra de Negócio:**
```php
public function test_user_cannot_rate_their_own_recipe()
{
    $user = User::where('email', 'maria.clara@culinaria.com')->first();
    $recipe = $user->recipes()->first();

    $response = $this->withHeader('Authorization', "Bearer $token")
        ->postJson("/api/v1/recipes/{$recipe->id}/ratings", ['stars' => 5]);

    $response->assertStatus(422)
        ->assertJsonPath('message', 'Você não pode avaliar sua própria receita.');
}
```

### Estratégias de Teste
- **RefreshDatabase**: Cada teste roda com banco limpo
- **WithFaker**: Dados fake para registros
- **Seed**: Dados de teste carregados via `DatabaseSeeder`
- **JWT Auth**: Tokens gerados via `JWTAuth::fromUser()`
- ** assertions**: Status code, JSON structure, database state

---

## 🎨 Design System (Frontend)

### Paleta de Cores Customizada

| Cor | Hex | Uso |
|-----|-----|-----|
| `off-white` | `#FDFDF9` | Background principal |
| `mint` | `#EAF1E4` | Background secundário |
| `graphite` | `#1F2420` | Texto principal |
| `sage` | `#5C6B5A` | Texto secundário |
| `olive` | `#8DB33F` | Cor primária (CTAs) |
| `terracotta` | `#E07A3E` | Acento |
| `gold` | `#F2B705` | Estrelas de avaliação |
| `error` | `#D9534F` | Erros |

### Tipografia
- **Serif**: Playfair Display (títulos)
- **Sans**: Inter (corpo do texto)

### Componentes Chave
- **LoginModal**: Modal com abas (Login/Registro), validação em campo, erros globais
- **RecipeForm**: Formulário multi-step com validação server-side
- **StarRating**: Componente reutilizável (readonly/interactive)
- **RecipeCard**: Card com hover effects, imagem por categoria
- **RecipeDetailModal**: Modal de detalhes com checkbox de ingredientes

---

## 🌐 API — Resumo dos Endpoints

### Auth (6 endpoints)
| Método | Rota | Descrição |
|--------|------|-----------|
| `POST` | `/auth/login` | Login com JWT |
| `POST` | `/auth/register` | Novo usuário |
| `POST` | `/auth/logout` | Invalidar token |
| `POST` | `/auth.refresh` | Renovar token |
| `GET` | `/auth/me` | Perfil do usuário |
| `PUT` | `/auth/profile` | Atualizar perfil |
| `PUT` | `/auth/password` | Alterar senha |

### Recipes (8 endpoints)
| Método | Rota | Descrição |
|--------|------|-----------|
| `GET` | `/recipes` | Listar (com filtros) |
| `GET` | `/recipes/{id}` | Detalhe |
| `GET` | `/recipes/category/{cat}` | Por categoria |
| `GET` | `/recipes/difficulty/{d}` | Por dificuldade |
| `POST` | `/recipes` | Criar (auth) |
| `PUT` | `/recipes/{id}` | Atualizar (auth) |
| `DELETE` | `/recipes/{id}` | Excluir (auth) |
| `POST` | `/recipes/{id}/ratings` | Avaliar (auth) |
| `GET` | `/recipes/my-recipes` | Minhas receitas |

### Jobs (6 endpoints)
| Método | Rota | Descrição |
|--------|------|-----------|
| `GET` | `/jobs` | Listar vagas ativas |
| `GET` | `/jobs/{id}` | Detalhe da vaga |
| `POST` | `/jobs` | Criar vaga (auth) |
| `PUT` | `/jobs/{id}` | Atualizar (auth) |
| `DELETE` | `/jobs/{id}` | Excluir (auth) |
| `GET` | `/jobs/my-jobs` | Minhas vagas |

### Applications (5 endpoints)
| Método | Rota | Descrição |
|--------|------|-----------|
| `GET` | `/applications` | Minhas candidaturas |
| `POST` | `/applications` | Candidatar-se |
| `GET` | `/applications/{id}` | Detalhe |
| `PUT` | `/applications/{id}/status` | Atualizar status |
| `DELETE` | `/applications/{id}` | Cancelar candidatura |

### Comments (4 endpoints)
| Método | Rota | Descrição |
|--------|------|-----------|
| `GET` | `/recipes/{id}/comments` | Listar |
| `POST` | `/recipes/{id}/comments` | Criar |
| `PUT` | `/comments/{id}` | Atualizar |
| `DELETE` | `/comments/{id}` | Excluir |

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

### 5. Dual Routes (/api/v1/ e /api/hm/)
**Escolha**: Duas versões de prefixo
**Justificativa**: `/api/v1/` para o frontend principal, `/api/hm/` como alias (HiringMachine).

---

## 📊 Dados de Teste (Seed)

| Usuário | Email | Role | Senha |
|---------|-------|------|-------|
| Rafael Pianaro | rafaelpianaro@mail.com | admin | password |
| Maria Clara | maria.clara@culinaria.com | recruiter | password |
| João Pedro | joao.pedro@culinaria.com | recruiter | password |
| Ana Souza | ana.souza@email.com | user | password |
| Carlos Lima | carlos.lima@email.com | user | password |
| Fernanda Rocha | fernanda.rocha@email.com | user | password |
| Lucas Costa | lucas.costa@email.com | candidate | password |

O seed inclui também receitas, vagas e candidaturas de exemplo.

---

## 🔑 Pontos Fortes para Destacar em Entrevista

1. **Testes Automatizados**: +39 testes de integração cobrindo auth, receitas, vagas e candidaturas
2. **Segurança**: Validação server-side, autorização por ownership, prevenção de auto-avaliação
3. **Design Patterns**: Actions, Traits, Service Layer, Repository Pattern
4. **Frontend Moderno**: Composition API, Pinia, Tailwind CSS, transições suaves
5. **Docker**: Ambiente reproduzível com 4 serviços (app, postgres, redis, mailpit)
6. **API RESTful**: Padronização de respostas, paginação, filtros, busca
7. **Validação Completa**: Backend + Frontend com error handling granular
8. **Persistência de Sessão**: localStorage com JWT para experiência fluida

---

## 🚀 Possíveis Melhorias

- [ ] Implementar upload de imagens com S3/Cloudflare R2
- [ ] Adicionar cache com Redis para consultas frequentes
- [ ] Implementar fila de jobs para envio de emails
- [ ] Adicionar rates limiting na API
- [ ] Implementar testes E2E com Playwright
- [ ] CI/CD com GitHub Actions
- [ ] Monitoramento com Sentry
- [ ] Documentação interativa com Swagger/OpenAPI
