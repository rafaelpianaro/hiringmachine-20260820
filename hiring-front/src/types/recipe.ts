export interface User {
  id: string
  name: string
  avatarUrl: string
}

export interface Rating {
  userId: string
  stars: 1 | 2 | 3
}

export type Category = 'Sobremesas' | 'Sopas' | 'Vegetariano' | 'Carnes' | 'Massas' | 'Bebidas'
export type Difficulty = 'Fácil' | 'Médio' | 'Difícil'

export interface Recipe {
  id: string
  title: string
  slug: string
  coverImage: string
  category: Category
  prepTimeMinutes: number
  servings: number
  difficulty: Difficulty
  ingredients: string[]
  steps: string[]
  authorId: string
  ratings: Rating[]
  createdAt: string
  updatedAt: string
}

export interface RecipeFormData {
  id?: string
  title: string
  category: Category
  prepTimeMinutes: number
  servings: number
  difficulty: Difficulty
  coverImage: string
  ingredients: string[]
  steps: string[]
}
