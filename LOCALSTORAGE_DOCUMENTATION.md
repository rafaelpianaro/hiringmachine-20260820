# LocalStorage in the Project

This project uses browser `localStorage` only to persist the authenticated session for the frontend.

## Purpose

The app stores the user session so the user remains logged in after page refreshes or when the browser is reopened.

The session is managed in:
- `hiring-front/src/stores/userStore.js`

## Keys used

### 1) `recepies_user`
This key stores the logged-in user profile as JSON.

Example:

```js
{
  "id": "12",
  "name": "Rafael",
  "email": "rafael@email.com",
  "avatarUrl": "https://ui-avatars.com/api/?name=Rafael&background=8DB33F&color=fff&size=128"
}
```

It is written with:

```js
localStorage.setItem(STORAGE_KEY_USER, JSON.stringify(user))
```

It is read with:

```js
const savedUser = localStorage.getItem(STORAGE_KEY_USER)
const parsedUser = JSON.parse(savedUser)
```

### 2) `recepies_token`
This key stores the JWT token returned by the API.

Example:

```js
"eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."
```

It is written with:

```js
localStorage.setItem(STORAGE_KEY_TOKEN, tokenValue)
```

It is read with:

```js
const savedToken = localStorage.getItem(STORAGE_KEY_TOKEN)
```

---

## Session restore flow

When the app starts, `userStore` tries to restore the session automatically:

```js
const savedUser = localStorage.getItem(STORAGE_KEY_USER)
const savedToken = localStorage.getItem(STORAGE_KEY_TOKEN)

if (savedUser && savedToken) {
  try {
    currentUser.value = JSON.parse(savedUser)
    token.value = savedToken
  } catch (e) {}
}
```

This means:
- the user remains authenticated without needing to log in again
- routes with `requiresAuth` can check `isLoggedIn`
- the token is reused by services that call the API

---

## Where it is used

### Authentication flow

The login and register actions create a minimal user object and persist it:

```js
const user = {
  id: String(data.user?.id || data.id),
  name: data.user?.name || data.name,
  email: data.user?.email || data.email,
  avatarUrl: `https://ui-avatars.com/api/?name=${encodeURIComponent(data.user?.name || data.name)}&background=8DB33F&color=fff&size=128`
}

const authToken = data.token || data.access_token
setSession(user, authToken)
```

The `setSession` function saves both values:

```js
function setSession(user, tokenValue) {
  currentUser.value = user
  token.value = tokenValue
  localStorage.setItem(STORAGE_KEY_USER, JSON.stringify(user))
  localStorage.setItem(STORAGE_KEY_TOKEN, tokenValue)
}
```

### API calls

The recipe API service reads the token from storage before every request:

```js
const savedToken = localStorage.getItem('recepies_token')
if (savedToken) {
  headers['Authorization'] = `Bearer ${savedToken}`
}
```

This is how the frontend sends the JWT to protected routes.

---

## Session clearing

When the user logs out, the app removes both keys:

```js
function clearSession() {
  currentUser.value = null
  token.value = null
  localStorage.removeItem(STORAGE_KEY_USER)
  localStorage.removeItem(STORAGE_KEY_TOKEN)
}
```

After that, the app treats the user as logged out and the UI redirects or hides protected actions.

---

## Important notes

- This is frontend persistence only; it is not a secure database.
- The token is stored in browser storage, so it is visible to the browser environment and should not be considered completely secure in an untrusted environment.
- The project does not store recipes, ratings, or comments in `localStorage`; those are loaded from the API.
- Development tools can also write extra keys to `localStorage` (for example Vue DevTools settings), but the application itself uses only `recepies_user` and `recepies_token` for the auth flow.

---

## Summary

In this project, the browser `localStorage` is used for one main purpose: keeping the authenticated user session alive.

The persisted values are:
- `recepies_user`: JSON with minimal user profile information
- `recepies_token`: JWT used for authenticated API requests

This keeps the app usable after refresh without forcing the user to log in again.
