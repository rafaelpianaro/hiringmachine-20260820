const API_BASE = import.meta.env.VITE_API_BASE_URL || '/api/v1'

// Imagens de comida por categoria (Unsplash)
const CATEGORY_IMAGES = {
  'Doces': [
    'https://images.unsplash.com/photo-1551024601-bec78aea704b?w=800&h=600&fit=crop',
    'https://images.unsplash.com/photo-1563729784474-d77dbb933a9e?w=800&h=600&fit=crop',
    'https://images.unsplash.com/photo-1558961363-fa8fdf82db35?w=800&h=600&fit=crop',
    'https://images.unsplash.com/photo-1571877227200-a0d98ea607e9?w=800&h=600&fit=crop',
    'https://images.unsplash.com/photo-1488477181946-6428a0291777?w=800&h=600&fit=crop'
  ],
  'Bolos': [
    'https://images.unsplash.com/photo-1578985545062-69928b1d9587?w=800&h=600&fit=crop',
    'https://images.unsplash.com/photo-1606313564200-e75d5e30476c?w=800&h=600&fit=crop',
    'https://images.unsplash.com/photo-1621303837174-89787a7d4729?w=800&h=600&fit=crop',
    'https://images.unsplash.com/photo-1509440159596-0249088772ff?w=800&h=600&fit=crop'
  ],
  'Saladas': [
    'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=800&h=600&fit=crop',
    'https://images.unsplash.com/photo-1540420773420-3366772f4999?w=800&h=600&fit=crop',
    'https://images.unsplash.com/photo-1505253716362-afaea1d3d1af?w=800&h=600&fit=crop',
    'https://images.unsplash.com/photo-1607532941433-304659e8198a?w=800&h=600&fit=crop'
  ],
  'Pratos Principais': [
    'https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=800&h=600&fit=crop',
    'https://images.unsplash.com/photo-1563379926898-05f4575a45d8?w=800&h=600&fit=crop',
    'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=800&h=600&fit=crop',
    'https://images.unsplash.com/photo-1476224203421-9ac39bcb3327?w=800&h=600&fit=crop',
    'https://images.unsplash.com/photo-1603360946369-dc9bb6258143?w=800&h=600&fit=crop'
  ],
  'Pães': [
    'https://images.unsplash.com/photo-1509440159596-0249088772ff?w=800&h=600&fit=crop',
    'https://images.unsplash.com/photo-1574085733277-851d9d856a3a?w=800&h=600&fit=crop',
    'https://images.unsplash.com/photo-1568254183919-78a4f43a2877?w=800&h=600&fit=crop',
  ],
  'Sobremesas': [
    'https://images.unsplash.com/photo-1551024601-bec78aea704b?w=800&h=600&fit=crop',
    'https://images.unsplash.com/photo-1563729784474-d77dbb933a9e?w=800&h=600&fit=crop',
    'https://images.unsplash.com/photo-1488477181946-6428a0291777?w=800&h=600&fit=crop'
  ],
  'Sopas': [
    'https://images.unsplash.com/photo-1476718406336-bb5a9690ee2a?w=800&h=600&fit=crop',
    'https://images.unsplash.com/photo-1547592166-23ac45744acd?w=800&h=600&fit=crop',
    'https://images.unsplash.com/photo-1558961363-fa8fdf82db35?w=800&h=600&fit=crop'
  ],
  'Massas': [
    'https://images.unsplash.com/photo-1473093295043-cdd812d0e601?w=800&h=600&fit=crop',
    'https://images.unsplash.com/photo-1476124369491-e7addf5db371?w=800&h=600&fit=crop',
    'https://images.unsplash.com/photo-1621996346565-e3dbc646d9a9?w=800&h=600&fit=crop'
  ],
  'Carnes': [
    'https://images.unsplash.com/photo-1588168333986-5078d3ae3976?w=800&h=600&fit=crop',
    'https://images.unsplash.com/photo-1598103442097-8b74394b95c6?w=800&h=600&fit=crop',
    'https://images.unsplash.com/photo-1544025162-d76694265947?w=800&h=600&fit=crop'
  ],
  'Bebidas': [
    'https://images.unsplash.com/photo-1623065422902-30a2d299bbe4?w=800&h=600&fit=crop',
    'https://images.unsplash.com/photo-1544145945-f90425340c7e?w=800&h=600&fit=crop',
    'https://images.unsplash.com/photo-1536935338788-846bb9981813?w=800&h=600&fit=crop'
  ],
  'Vegetariano': [
    'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=800&h=600&fit=crop',
    'https://images.unsplash.com/photo-1540420773420-3366772f4999?w=800&h=600&fit=crop',
    'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=800&h=600&fit=crop'
  ]
}

const FALLBACK_IMAGES = [
  'https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=800&h=600&fit=crop',
  'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=800&h=600&fit=crop',
  'https://images.unsplash.com/photo-1567620905732-2d1ec7ab7445?w=800&h=600&fit=crop',
  'https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?w=800&h=600&fit=crop',
  'https://images.unsplash.com/photo-1482049016688-2d3e1b311543?w=800&h=600&fit=crop',
  'https://images.unsplash.com/photo-1495521821757-a1efb6729352?w=800&h=600&fit=crop'
]

function getCoverImage(raw) {
  if (raw.image) return raw.image
  const category = raw.category
  if (category && CATEGORY_IMAGES[category]) {
    const images = CATEGORY_IMAGES[category]
    return images[raw.id % images.length]
  }
  return FALLBACK_IMAGES[raw.id % FALLBACK_IMAGES.length]
}

const DIFFICULTY_MAP = {
  easy: 'Fácil',
  medium: 'Médio',
  hard: 'Difícil'
}

function mapRecipe(raw) {
  return {
    id: String(raw.id),
    userId: raw.user_id,
    title: raw.title,
    description: raw.description || '',
    slug: raw.title.toLowerCase().replace(/\s+/g, '-').replace(/[^a-z0-9-]/g, ''),
    coverImage: getCoverImage(raw),
    category: raw.category,
    prepTimeMinutes: raw.prep_time || 0,
    cookTimeMinutes: raw.cook_time || 0,
    servings: raw.servings,
    difficulty: DIFFICULTY_MAP[raw.difficulty] || raw.difficulty,
    ingredients: raw.ingredients || [],
    steps: raw.instructions || [],
    authorId: raw.user_id,
    authorName: raw.user?.name || 'Desconhecido',
    ratings: raw.ratings || [],
    isPublished: raw.is_published,
    createdAt: raw.created_at_formatted || raw.created_at,
    updatedAt: raw.updated_at_formatted || raw.updated_at
  }
}

function extractData(response) {
  if (response?.data?.data) return response.data.data.map(mapRecipe)
  if (Array.isArray(response?.data)) return response.data.map(mapRecipe)
  if (Array.isArray(response)) return response.map(mapRecipe)
  return []
}

function extractSingle(response) {
  if (response?.data) return mapRecipe(response.data)
  return mapRecipe(response)
}

async function apiFetch(path, options = {}) {
  const url = `${API_BASE}${path}`
  const savedToken = localStorage.getItem('recepies_token')
  const headers = {
    'Content-Type': 'application/json',
    ...options.headers
  }
  if (savedToken) headers['Authorization'] = `Bearer ${savedToken}`
  const res = await fetch(url, {
    headers,
    ...options
  })
  const data = await res.json().catch(() => null)
  if (!res.ok || data?.errors) {
    const error = new Error(data?.message || `Erro ${res.status}`)
    error.fieldErrors = data?.errors || {}
    throw error
  }
  return data
}

export const recipeService = {
  async getAllRecipes() {
    const data = await apiFetch('/recipes')
    return extractData(data)
  },

  async getRecipeById(id) {
    const data = await apiFetch(`/recipes/${id}`)
    return extractSingle(data)
  },

  async getRecipesByCategory(category) {
    const data = await apiFetch(`/recipes?category=${encodeURIComponent(category)}`)
    return extractData(data)
  },

  async searchRecipes(query) {
    const data = await apiFetch(`/recipes?search=${encodeURIComponent(query)}`)
    return extractData(data)
  },

  async getMyRecipes() {
    const data = await apiFetch('/recipes/my-recipes')
    return extractData(data)
  },

  async createRecipe(formData) {
    const data = await apiFetch('/recipes', {
      method: 'POST',
      body: JSON.stringify(formData)
    })
    return extractSingle(data)
  },

  async updateRecipe(id, formData) {
    const data = await apiFetch(`/recipes/${id}`, {
      method: 'PUT',
      body: JSON.stringify(formData)
    })
    return extractSingle(data)
  },

  async deleteRecipe(id) {
    return await apiFetch(`/recipes/${id}`, {
      method: 'DELETE'
    })
  },

  async rateRecipe(recipeId, stars) {
    const data = await apiFetch(`/recipes/${recipeId}/ratings`, {
      method: 'POST',
      body: JSON.stringify({ stars })
    })
    return extractSingle(data)
  }
}
