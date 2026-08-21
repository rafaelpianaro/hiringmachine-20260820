# How the Recipe Search Works

This project has a simple but effective search flow built in two layers: backend filtering for the API and frontend filtering for the user experience.

## 1. Backend search

The backend search is implemented in the `RecipeList` action, which is responsible for building the recipe query.

Relevant logic:

```php
if ($request->has('search')) {
    $search = $request->search;
    $query->where(function ($q) use ($search) {
        $q->where('title', 'like', '%' . $search . '%')
          ->orWhere('description', 'like', '%' . $search . '%');
    });
}
```

What this does:
- Reads the `search` parameter from the request
- Applies a SQL `LIKE` filter to the `title` column
- Applies another `LIKE` filter to the `description` column
- Combines both with `OR`, so the recipe matches if either field contains the search term

This is intentionally lightweight and scalable for an MVP. It keeps the search logic in a dedicated action instead of inside the controller, which makes the controller thinner and the business logic easier to test.

### Example

Request:

```http
GET /api/v1/recipes?search=pasta
```

This returns recipes where the title or description contains `pasta`.

### Combined filters

The same action also supports filters for category and difficulty:

```php
if ($request->has('category')) {
    $query->where('category', $request->category);\n}

if ($request->has('difficulty')) {
    $query->where('difficulty', $request->difficulty);
}
```

That means the API can apply multiple layers of constraints at once:
- category
- difficulty
- free text search
- publish status

The final query is then ordered by `created_at DESC` and paginated.

---

## 2. Frontend search

On the frontend, the store keeps a `searchQuery` string and a `filteredRecipes` computed property.

```js
const searchQuery = ref('')

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

This gives a fast local UX because the app does not need to hit the API every time the user types a character. The frontend search filters:
- title
- ingredients
- current category, if selected

### User flow

1. User types in the search box
2. `searchQuery` is updated reactively
3. `filteredRecipes` recomputes automatically
4. The grid re-renders with only matching recipes

This is a nice UX pattern for small/medium datasets and keeps the app responsive.

---

## 3. API service integration

The frontend service also exposes a dedicated endpoint for search:

```js
async searchRecipes(query) {
  const data = await apiFetch(`/recipes?search=${encodeURIComponent(query)}`)
  return extractData(data)
}
```

So the app can either:
- use the backend search endpoint for server-side filtering, or
- use local filtering in the store for immediate feedback

In this project, the local store filter is the main interaction layer, while the API search endpoint is still available when needed.

---

## 4. Why this approach works well

This architecture is easy to explain in an interview because it shows a few solid principles:

- Separation of concerns: controller stays thin, business logic lives in an Action class
- Reusability: same query logic can be reused across endpoints and filters
- User experience: frontend performs instant local filtering for responsiveness
- Maintainability: logic is explicit and easy to test

## 5. Interview-ready summary

> The search flow in this application is built in two layers. On the backend, the `RecipeList` action checks the `search` parameter and applies `LIKE` filters to the recipe title and description. On the frontend, the Pinia store keeps a `searchQuery` and a computed `filteredRecipes` list, which filters by title and ingredients in real time. This creates a fast, responsive user experience while still keeping the backend API logic clean, reusable, and easy to test.
