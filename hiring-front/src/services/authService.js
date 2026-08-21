const API_BASE = import.meta.env.VITE_API_BASE_URL || '/api/hm'

async function apiFetch(path, options = {}) {
  const url = `${API_BASE}${path}`
  const res = await fetch(url, {
    headers: {
      'Content-Type': 'application/json',
      ...options.headers
    },
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

export const authService = {
  async login(email, password) {
    return await apiFetch('/auth/login', {
      method: 'POST',
      body: JSON.stringify({ email, password })
    })
  },

  async register({ name, email, password, password_confirmation }) {
    return await apiFetch('/auth/register', {
      method: 'POST',
      body: JSON.stringify({ name, email, password, password_confirmation })
    })
  }
}
