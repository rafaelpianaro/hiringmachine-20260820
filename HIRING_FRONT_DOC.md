# 🍳 HiringFront — Documentação Completa do Frontend

## 📋 Visão Geral

O **HiringFront** é um SPA (Single Page Application) desenvolvido com **Vue.js 3** para a plataforma de receitas culinárias. O frontend se comunica com uma API RESTful (Laravel) e oferece uma experiência moderna, responsiva e fluida para descobrir, criar e compartilhar receitas.

### Stack Tecnológica

| Tecnologia | Versão | Função |
|-----------|--------|--------|
| Vue.js | ^3.4.21 | Framework reativo (Composition API) |
| Pinia | ^2.1.7 | Gerenciamento de estado |
| Vue Router | ^4.3.0 | Roteamento SPA |
| Vite | ^5.2.0 | Build tool e dev server |
| Tailwind CSS | ^3.4.3 | Utility-first CSS |
| PostCSS | ^8.4.38 | Processamento CSS |
| Autoprefixer | ^10.4.19 | Prefixos CSS automáticos |

---

## 📁 Estrutura de Diretórios

```
hiring-front/
├── src/
│   ├── App.vue                          # Componente raiz
│   ├── main.js                          # Entry point (Pinia + Router)
│   ├── assets/
│   │   └── main.css                     # Estilos globais
│   ├── router/
│   │   └── index.js                     # Configuração de rotas
│   ├── stores/                          # Estado global (Pinia)
│   │   ├── userStore.js                 # Autenticação e sessão
│   │   ├── recipeStore.js               # Receitas e filtros
│   │   └── toastStore.js                # Notificações toast
│   ├── services/                        # Camada de API
│   │   ├── authService.js               # Login e registro
│   │   └── recipeService.js             # CRUD de receitas
│   ├── views/                           # Páginas
│   │   ├── HomeView.vue                 # Página inicial
│   │   ├── RecipeDetailView.vue         # Detalhe da receita (rota)
│   │   ├── RecipeEditView.vue           # Criar/editar receita
│   │   └── MyRecipesView.vue            # Minhas receitas
│   └── components/
│       ├── ToastNotification.vue        # Notificação toast global
│       ├── layout/
│       │   ├── Header.vue               # Navbar fixa
│       │   └── Footer.vue               # Rodapé
│       ├── auth/
│       │   └── LoginModal.vue           # Modal de login/registro
│       ├── recipes/
│       │   ├── RecipeCard.vue           # Card de receita
│       │   ├── RecipeGrid.vue           # Grid de receitas (home)
│       │   ├── RecipeForm.vue           # Formulário de receita
│       │   ├── RecipeDetailModal.vue    # Modal de detalhes
│       │   └── StarRating.vue           # Componente de estrelas
│       └── home/
│           ├── Hero.vue                 # Seção hero
│           ├── CategoryGrid.vue         # Grid de categorias
│           └── CtaSection.vue           # Call-to-action
├── index.html
├── vite.config.js
├── tailwind.config.js
├── postcss.config.js
├── package.json
└── jsconfig.json
```

---

## 🚦 Rotas

### Configuração (`router/index.js`)

| Rota | Componente | Auth | Descrição |
|------|-----------|------|-----------|
| `/` | `HomeView` | ❌ | Página inicial com hero + grid de receitas |
| `/receitas/:id` | `RecipeDetailView` | ❌ | Detalhe completo da receita |
| `/minhas-receitas` | `MyRecipesView` | ✅ | Painel do usuário com suas receitas |
| `/receitas/nova` | `RecipeEditView` | ✅ | Formulário para criar receita |
| `/receitas/:id/editar` | `RecipeEditView` | ✅ | Formulário para editar receita |

### Guard de Autenticação

```javascript
router.beforeEach((to, from, next) => {
  const user = JSON.parse(localStorage.getItem('recepies_user') || 'null')
  if (to.meta.requiresAuth && !user) {
    next('/')  // Redireciona para home se não logado
  } else {
    next()
  }
})
```

### Lazy Loading

As rotas protegidas e o detalhe da receita usam **lazy loading**:
```javascript
component: () => import('@/views/RecipeDetailView.vue')
```

---

## 🗃️ Gerenciamento de Estado (Pinia)

### Arquitetura de Stores

```
┌─────────────────────────────────────────┐
│              userStore.js                │
│  Estado: currentUser, token             │
│  Ações: login(), register(), logout()   │
│  Persistência: localStorage             │
└─────────────────────────────────────────┘
                    │
                    ▼
┌─────────────────────────────────────────┐
│             recipeStore.js              │
│  Estado: recipes, myRecipes             │
│  Filtros: searchQuery, activeCategory   │
│  Ações: fetchRecipes(), CRUD, rate()    │
└─────────────────────────────────────────┘
                    │
                    ▼
┌─────────────────────────────────────────┐
│             toastStore.js               │
│  Estado: message, type, visible         │
│  Ação: show(msg, type)                  │
│  Auto-dismiss: 5 segundos              │
└─────────────────────────────────────────┘
```

### 1. `userStore.js` — Autenticação

**Responsabilidade:** Gerenciar sessão do usuário, login, registro e logout.

```javascript
// Estado reativo
const currentUser = ref(null)    // { id, name, email, avatarUrl }
const token = ref(null)          // JWT token
const showLoginModal = ref(false)
const loading = ref(false)
const error = ref(null)          // Mensagem de erro global
const fieldErrors = ref(null)    // Erros por campo { email: ['...'] }

// Computed
const isLoggedIn = computed(() => !!currentUser.value && !!token.value)
```

**Fluxo de Login:**
```
1. Usuário preenche formulário → handleLogin()
2. userStore.login(email, password) chamado
3. authService.login() faz POST /auth/login
4. Resposta → extrai user + token
5. setSession() salva no state + localStorage
6. Modal fecha automaticamente
7. Toast mostra "Bem-vindo(a) à cozinha! 👨‍🍳"
```

**Persistência:**
- `recepies_user` → dados do usuário (JSON)
- `recepies_token` → JWT token
- Sessão restaurada automaticamente no carregamento

### 2. `recipeStore.js` — Receitas

**Responsabilidade:** CRUD de receitas, filtros, busca e avaliações.

```javascript
// Estado
const recipes = ref([])          // Todas as receitas
const myRecipes = ref([])        // Receitas do usuário logado
const searchQuery = ref('')      // Busca por texto
const activeCategory = ref('Todas')  // Filtro de categoria

// Computed — filtros reativos
const filteredRecipes = computed(() => {
  let result = recipes.value
  if (activeCategory.value !== 'Todas') {
    result = result.filter(r => r.category === activeCategory.value)
  }
  if (searchQuery.value.trim()) {
    const q = searchQuery.value.toLowerCase()
    result = result.filter(r =>
      r.title.toLowerCase().includes(q) ||
      r.ingredients.some(i => i.toLowerCase().includes(q))
    )
  }
  return result
})
```

**Operações CRUD:**
| Ação | Método | Efeito no Estado |
|------|--------|-----------------|
| `fetchRecipes()` | GET /recipes | Popula `recipes` |
| `fetchMyRecipes()` | GET /recipes/my-recipes | Popula `myRecipes` |
| `getRecipe(id)` | GET /recipes/:id | Retorna receita |
| `createRecipe(data)` | POST /recipes | Adiciona ao início de `recipes` |
| `updateRecipe(id, data)` | PUT /recipes/:id | Atualiza no array |
| `deleteRecipe(id)` | DELETE /recipes/:id | Remove do array |
| `rateRecipe(id, stars)` | POST /recipes/:id/ratings | Atualiza receita |

**Tratamento de Erros de Validação:**
```javascript
async function createRecipe(formData) {
  fieldErrors.value = null
  try {
    const newRecipe = await recipeService.createRecipe(formData)
    recipes.value.unshift(newRecipe)
    return newRecipe
  } catch (e) {
    error.value = e.message
    fieldErrors.value = e.fieldErrors || null  // Erros por campo
    throw e
  }
}
```

### 3. `toastStore.js` — Notificações

**Responsabilidade:** Exibir mensagens temporárias no topo da página.

```javascript
function show(msg, t = 'success') {
  message.value = msg
  type.value = t
  visible.value = true
  if (timeout) clearTimeout(timeout)
  timeout = setTimeout(() => { visible.value = false }, 5000)
}
```

**Tipos suportados:**
| Tipo | Cor | Ícone | Exemplo de uso |
|------|-----|-------|----------------|
| `success` | `graphite` (#1F2420) | ✓ | "Receita salva com carinho 🍲" |
| `error` | `error` (#D9534F) | ! | "Você não pode avaliar sua própria receita." |

**Onde é usado:**
- Login: "Bem-vindo(a) à cozinha! 👨‍🍳"
- Registro: "Conta criada com sucesso! Bem-vindo(a) 👨‍🍳"
- Criar receita: "Sua receita foi salva com carinho 🍲"
- Editar receita: "Sua receita foi atualizada com carinho 🍲"
- Deletar receita: "Receita removida."
- Avaliar: "Avaliação registrada! ⭐"
- Logout: "Até a próxima receita! 👋"
- Erros da API: mensagem de erro retornada pelo backend

---

## 🔌 Chamadas de API

### Configuração Base

```javascript
// authService.js e recipeService.js
const API_BASE = import.meta.env.VITE_API_BASE_URL || '/api/hm'
```

O Vite faz **proxy** automaticamente:
```javascript
// vite.config.js
server: {
  proxy: {
    '/api': {
      target: 'http://localhost:5757',
      changeOrigin: true
    }
  }
}
```

### `authService.js` — Autenticação

| Método | Endpoint | Body | Descrição |
|--------|----------|------|-----------|
| `login(email, password)` | `POST /auth/login` | `{ email, password }` | Login com JWT |
| `register({ name, email, password, password_confirmation })` | `POST /auth/register` | Dados de registro | Criar conta |

**Tratamento de erro padronizado:**
```javascript
async function apiFetch(path, options = {}) {
  const res = await fetch(url, { headers, ...options })
  const data = await res.json().catch(() => null)
  if (!res.ok || data?.errors) {
    const error = new Error(data?.message || `Erro ${res.status}`)
    error.fieldErrors = data?.errors || {}  // Erros por campo
    throw error
  }
  return data
}
```

### `recipeService.js` — Receitas

| Método | Endpoint | Body | Descrição |
|--------|----------|------|-----------|
| `getAllRecipes()` | `GET /recipes` | - | Listar receitas publicadas |
| `getRecipeById(id)` | `GET /recipes/:id` | - | Detalhe da receita |
| `getRecipesByCategory(cat)` | `GET /recipes?category=...` | - | Filtrar por categoria |
| `searchRecipes(query)` | `GET /recipes?search=...` | - | Buscar por texto |
| `getMyRecipes()` | `GET /recipes/my-recipes` | - | Receitas do usuário (auth) |
| `createRecipe(data)` | `POST /recipes` | JSON da receita | Criar receita (auth) |
| `updateRecipe(id, data)` | `PUT /recipes/:id` | JSON da receita | Atualizar receita (auth) |
| `deleteRecipe(id)` | `DELETE /recipes/:id` | - | Excluir receita (auth) |
| `rateRecipe(id, stars)` | `POST /recipes/:id/ratings` | `{ stars }` | Avaliar receita (auth) |

**Autenticação automática:**
```javascript
async function apiFetch(path, options = {}) {
  const savedToken = localStorage.getItem('recepies_token')
  const headers = { 'Content-Type': 'application/json', ...options.headers }
  if (savedToken) headers['Authorization'] = `Bearer ${savedToken}`
  // ...
}
```

### Mapeamento de Dados (API → Frontend)

A função `mapRecipe()` converte o formato da API para o formato do frontend:

| API (snake_case) | Frontend (camelCase) | Observação |
|-----------------|---------------------|------------|
| `id` | `id` (String) | Convertido para string |
| `user_id` | `userId`, `authorId` | Duplicado para uso diferente |
| `title` | `title` | - |
| `description` | `description` | - |
| `ingredients` | `ingredients` | Array JSON |
| `instructions` | `steps` | Renomeado |
| `prep_time` | `prepTimeMinutes` | - |
| `cook_time` | `cookTimeMinutes` | - |
| `difficulty` | `difficulty` | `easy` → `Fácil` |
| `category` | `category` | - |
| `user.name` | `authorName` | Aninhado |
| `ratings` | `ratings` | Array de avaliações |
| `is_published` | `isPublished` | - |
| `created_at` | `createdAt` | Formato `DD/MM/YYYY HH:mm` |

### Sistema de Imagens

Como a API não retorna URLs de imagem, o frontend usa um sistema de **imagens por categoria**:

```javascript
function getCoverImage(raw) {
  if (raw.image) return raw.image                          // Imagem customizada
  const category = raw.category
  if (category && CATEGORY_IMAGES[category]) {
    return CATEGORY_IMAGES[category][raw.id % images.length]  // Imagem da categoria
  }
  return FALLBACK_IMAGES[raw.id % FALLBACK_IMAGES.length]     // Fallback
}
```

**11 categorias com 3-5 imagens cada** (Unsplash):
Doces, Bolos, Saladas, Pratos Principais, Pães, Sobremesas, Sopas, Massas, Carnes, Bebidas, Vegetariano.

---

## 🧩 Componentes

### Hierarquia de Componentes

```
App.vue
├── Header.vue              (fixo, z-50)
├── RouterView
│   ├── HomeView.vue
│   │   ├── Hero.vue
│   │   ├── RecipeGrid.vue
│   │   │   ├── RecipeCard.vue (×n)
│   │   │   │   └── StarRating.vue
│   │   │   └── RecipeDetailModal.vue
│   │   │       └── StarRating.vue
│   │   └── CtaSection.vue
│   ├── RecipeDetailView.vue
│   │   └── StarRating.vue
│   ├── RecipeEditView.vue
│   │   └── RecipeForm.vue
│   └── MyRecipesView.vue
│       └── RecipeDetailModal.vue
├── Footer.vue
├── LoginModal.vue          (z-[100])
└── ToastNotification.vue   (z-[200])
```

### Componentes Detalhados

#### `Header.vue` — Navbar Fixa

- **Comportamento:** Transparente no topo, blur + sombra ao rolar
- **Estado não logado:** Botões "Entrar" e "Criar Receita"
- **Estado logado:** Avatar + dropdown com "Minhas Receitas", "Nova Receita", "Sair"
- **Scroll spy:** Botão de busca faz scroll suave para seção de receitas

```javascript
// Efeito de scroll
onMounted(() => {
  window.addEventListener('scroll', () => {
    scrolled.value = window.scrollY > 20
  })
})
```

#### `LoginModal.vue` — Autenticação

- **Abas:** Login / Criar conta (tabs animadas)
- **Validação:** Erros por campo exibidos inline
- **Erros globais:** Exibidos acima do formulário
- **Loading:** Spinner nos botões durante requisição
- **Sincronização:** Erros do store → refs locais via `watch`

```javascript
// Tabs com limpeza de erros
function switchMode(newMode) {
  mode.value = newMode
  fieldErrors.value = null
  globalError.value = ''
  userStore.error = null
  userStore.fieldErrors = null
}
```

#### `RecipeGrid.vue` — Grid de Receitas (Home)

- **Busca:** Input com filtro em tempo real
- **Categorias:** Botões de filtro (extraídos das receitas da API)
- **Grid responsivo:** 1 → 2 → 3 colunas
- **Modal:** Abre ao clicar no card
- **Exclusão:** Modal de confirmação com `z-[110]`

#### `RecipeCard.vue` — Card de Receita

- **Hover:** Sombra + elevação + zoom na imagem
- **Ações do dono:** Botões de editar/excluir (aparecem só para o autor)
- **Avaliação:** StarRating interativo
- **Autor:** Avatar + primeiro nome

#### `RecipeForm.vue` — Formulário de Receita

- **Seções:** Informações básicas, Ingredientes, Modo de preparo
- **Validação server-side:** Erros exibidos por campo
- **Upload de imagem:** Preview local (URL.createObjectURL)
- **Ingredientes/Passos:** Lista dinâmica (adicionar/remover)
- **Categorias:** 11 opções (Doces, Bolos, Saladas, etc.)
- **Dificuldade:** easy/medium/hard → Fácil/Médio/Difícil

```javascript
// Payload enviado para API
const payload = {
  title, description, category, difficulty,
  prep_time, cook_time, servings, image,
  ingredients: form.ingredients.filter(i => i.trim()),
  instructions: form.instructions.filter(s => s.trim())
}
```

#### `RecipeDetailModal.vue` — Modal de Detalhes

- **Layout:** Header com imagem + gradient, conteúdo em grid 2/5 + 3/5
- **Ingredientes:** Checklist interativo (checkbox com visual de marcado)
- **Passos:** Numerados com circles
- **Avaliações:** Média + StarRating + possibilidade de avaliar
- **Ações do dono:** Editar / Excluir
- **Body scroll:** Bloqueado quando modal aberto

#### `StarRating.vue` — Avaliação por Estrelas

- **Props:** `modelValue`, `average`, `readonly`, `max` (default: 3), `showCount`, `count`
- **Modos:** Somente leitura (média) ou interativo (voto)
- **Evento:** Emite `rate(stars)` ao clicar

```vue
<!-- Uso readonly (média) -->
<StarRating :average="4.5" :readonly="true" />

<!-- Uso interativo (avaliar) -->
<StarRating v-model="userRating" :readonly="false" @rate="handleRate" />
```

#### `Hero.vue` — Seção Inicial

- **Layout:** Grid 2 colunas (texto + imagem circular animada)
- **Animação:** `animate-float` (deslocamento vertical suave)
- **CTA:** "Começar agora" (logado: redireciona; deslogado: abre modal)
- **Badge:** "4.8 média de avaliação"

#### `CtaSection.vue` — Call-to-Action

- **Fundo:** Olive (#8DB33F)
- **CTA dinâmico:** Logado → "Criar nova receita"; Deslogado → "Criar conta grátis"

---

## 🎨 Design System (Tailwind)

### Paleta de Cores

| Nome | Hex | Uso |
|------|-----|-----|
| `off-white` | `#FDFDF9` | Background principal |
| `mint` | `#EAF1E4` | Background secundário, cards |
| `graphite` | `#1F2420` | Texto principal, header dark |
| `sage` | `#5C6B5A` | Texto secundário, labels |
| `olive` | `#8DB33F` | Cor primária (CTAs, botões) |
| `olive-dark` | `#719432` | Hover de botões olive |
| `terracotta` | `#E07A3E` | Acento (não usado ativamente) |
| `gold` | `#F2B705` | Estrelas de avaliação |
| `star-empty` | `#D8DED2` | Estrelas vazias |
| `border-light` | `#E3E9DD` | Bordas suaves |
| `error` | `#D9534F` | Erros, exclusão |

### Tipografia

| Fonte | Uso |
|-------|-----|
| **Playfair Display** (serif) | Títulos, headings |
| **Inter** (sans-serif) | Corpo do texto, botões |

### Bordas Arredondadas

| Classe | Raio | Uso |
|--------|------|-----|
| `rounded-full` | 9999px | Botões, inputs, avatares |
| `rounded-2xl` | 1rem | Cards de stats |
| `rounded-3xl` | 1.5rem | Cards de receita, modais |
| `rounded-[2rem]` | 2rem | Header de modal de detalhe |

### Animações

| Animação | Efeito |
|----------|--------|
| `animate-float` | Deslocamento vertical 12px (6s loop) |
| `hover:scale-105` | Zoom 5% na imagem ao hover |
| `hover:-translate-y-1` | Elevação 4px no card |
| `animate-spin` | Spinner de loading |

---

## 🔄 Fluxo de Interações do Usuário

### 1. Descobrir Receitas (Não logado)

```
HomeView
  → Hero.vue (CTA "Começar agora" → abre LoginModal)
  → RecipeGrid.vue
      → onMounted() → recipeStore.fetchRecipes()
      → GET /recipes → popula recipes[]
      → RecipeCard.vue × N (grid responsivo)
      → Busca em tempo real (filteredRecipes computed)
      → Filtro por categoria
      → Clique no card → RecipeDetailModal
          → Ingredientes com checkbox
          → Modo de preparo numerado
          → Avaliação (星星) → "Entre para avaliar" → abre LoginModal
```

### 2. Login / Registro

```
Header "Entrar" ou Hero "Começar agora"
  → userStore.openLogin() → showLoginModal = true
  → LoginModal.vue aparece (z-[100])
  → Aba "Entrar":
      → Preenche email + senha
      → handleLogin() → userStore.login()
      → POST /auth/login
      → Sucesso: setSession() + localStorage + fecha modal + toast "Bem-vindo!"
      → Erro: fieldErrors sincronizados via watch → exibidos inline
  → Aba "Criar conta":
      → Preenche nome, email, senha, confirmação
      → handleRegister() → userStore.register()
      → POST /auth/register
      → Sucesso: sessão criada + toast "Conta criada!"
      → Validação: "As senhas não conferem" (client-side)
```

### 3. Criar Receita (Logado)

```
Header "Nova Receita" ou Hero "Criar Receita"
  → RouterLink → /receitas/nova
  → RecipeEditView → RecipeForm
  → Preenche: título, descrição, categoria, dificuldade, tempo, porções
  → Adiciona ingredientes (lista dinâmica)
  → Adiciona passos (lista dinâmica)
  → Upload de imagem (preview local)
  → Submit → handleSubmit() → recipeStore.createRecipe()
  → POST /recipes (com Authorization: Bearer)
  → Sucesso: redireciona para /minhas-receitas + toast "Receita salva! 🍲"
  → Erro: fieldErrors exibidos por campo no formulário
```

### 4. Avaliar Receita (Logado)

```
RecipeDetailModal ou RecipeDetailView
  → StarRating interativo aparece (se não é o dono)
  → Clique na estrela → handleRate(stars)
  → recipeStore.rateRecipe(id, stars)
  → POST /recipes/:id/ratings
  → Sucesso: toast "Avaliação registrada! ⭐"
  → Erro: toast com mensagem de erro (ex: "não pode avaliar própria receita")
```

### 5. Editar / Excluir Receita (Dono)

```
RecipeCard (botões edit/delete) ou RecipeDetailModal
  → Editar: router.push(`/receitas/${id}/editar`)
      → RecipeEditView carrega receita existente
      → RecipeForm preenche com initialData
      → Submit → recipeStore.updateRecipe()
      → PUT /recipes/:id
      → Sucesso: redireciona + toast "Atualizada com carinho 🍲"

  → Excluir: abre modal de confirmação (z-[110])
      → "Excluir receita?" + "Esta ação não pode ser desfeita"
      → Confirmar → recipeStore.deleteRecipe()
      → DELETE /recipes/:id
      → Sucesso: toast "Receita removida." + fecha modal
```

### 6. Minhas Receitas (Logado)

```
Header dropdown → "Minhas Receitas"
  → /minhas-receitas → MyRecipesView
  → onMounted() → recipeStore.fetchMyRecipes()
  → GET /recipes/my-recipes (com token)
  → Exibe: stats (receitas, avaliações, média)
  → Grid de cards com botões editar/excluir
  → Busca local (filteredMyRecipes)
  → Clique → RecipeDetailModal
  → Empty state: "Sua cozinha está vazia" + CTA para criar
```

---

## 🧪 Tratamento de Erros

### Padrão de Erros da API

```json
{
  "status": "error",
  "message": "Mensagem descritiva",
  "errors": {
    "campo": ["Mensagem de erro para este campo"]
  }
}
```

### Fluxo de Erros

```
API retorna erro
  → apiFetch() detecta !res.ok ou data.errors
  → Cria Error com message + fieldErrors
  → Lança exceção
  → Store captura: error.value = e.message, fieldErrors.value = e.fieldErrors
  → Componente reage via watch ou props
  → Toast exibe mensagem global
  → Formulário exibe erros por campo
```

### Componentes de Erro

| Componente | Erro Global | Erros por Campo |
|-----------|------------|-----------------|
| `LoginModal` | `globalError` (div vermelha) | `fieldError(field)` inline |
| `RecipeForm` | `globalError` prop (div vermelha) | `fieldError(field)` + borda vermelha |
| `RecipeEditView` | via RecipeForm | via RecipeForm |
| Qualquer视图 | `toast.show(msg, 'error')` | - |

---

## ⚙️ Configuração do Projeto

### `vite.config.js`

```javascript
export default defineConfig({
  plugins: [vue()],
  resolve: {
    alias: { '@': fileURLToPath(new URL('./src', import.meta.url)) }
  },
  server: {
    proxy: {
      '/api': { target: 'http://localhost:5757', changeOrigin: true }
    }
  }
})
```

- **Alias `@`** → resolves to `./src`
- **Proxy `/api`** → encaminha para Laravel na porta 5757

### `tailwind.config.js`

- **Cores customizadas:** 11 cores do design system
- **Fontes:** Playfair Display (serif) + Inter (sans)
- **Animação `float`:** translateY de 0 a -12px em 6s

### `package.json`

```json
{
  "scripts": {
    "dev": "vite",        // Dev server com HMR
    "build": "vite build", // Build de produção
    "preview": "vite preview" // Preview do build
  }
}
```

---

## 📊 Resumo de Dados

### Modelo de Receita (Frontend)

```javascript
{
  id: String,              // "3"
  userId: Number,          // 1
  title: String,           // "Pão de Fermentação Natural"
  description: String,     // "Pão caseiro feito com..."
  slug: String,            // "pao-de-fermentacao-natural"
  coverImage: String,      // URL do Unsplash
  category: String,        // "Pães"
  prepTimeMinutes: Number, // 30
  cookTimeMinutes: Number, // 45
  servings: Number,        // 1
  difficulty: String,      // "Difícil"
  ingredients: Array,      // ["500g de farinha...", ...]
  steps: Array,            // ["Misture a farinha...", ...]
  authorId: Number,        // 1
  authorName: String,      // "Maria Clara"
  ratings: Array,          // [{ id, userId, stars, userName }]
  isPublished: Boolean,    // true
  createdAt: String,       // "20/08/2026 14:30"
  updatedAt: String        // "20/08/2026 14:30"
}
```

### Modelo de Usuário (Frontend)

```javascript
{
  id: String,              // "2"
  name: String,            // "Maria Clara"
  email: String,           // "maria.clara@culinaria.com"
  avatarUrl: String        // "https://ui-avatars.com/api/?name=..."
}
```

---

## 🔑 Pontos-Chave para Entrevista

1. **Composition API:** Todo o projeto usa `<script setup>` com Composition API
2. **Separation of Concerns:** Services (API) ≠ Stores (Estado) ≠ Components (UI)
3. **Reatividade:** `computed` para filtros, `watch` para sincronização de erros
4. **Persistência:** localStorage para sessão (user + token)
5. **Lazy Loading:** Rotas protegidas carregam sob demanda
6. **Error Handling:** Dupla camada — toast global + erros por campo
7. **Design System:** Paleta consistente via Tailwind config
8. **UX:** Loading states, empty states, confirmação de exclusão, breadcrumbs
9. **Proxy:** Vite proxy elimina CORS em desenvolvimento
10. **Imagens:** Sistema de fallback por categoria (Unsplash)
