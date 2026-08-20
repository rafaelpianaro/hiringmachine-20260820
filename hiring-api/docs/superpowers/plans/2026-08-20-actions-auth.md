# Actions Auth Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Refatorar a lógica de negócio do domínio Auth/User do `AuthController` para classes de ação (`app/Actions/`), seguindo o padrão do `nunomaduro/essentials` (sem instalar o pacote).

**Architecture:** Cada ação é uma `final readonly class` com um método `handle()` que executa uma única tarefa de negócio. Ações recebem dados já validados (via FormRequest) e retornam modelo/resultado ou lançam `ValidationException` — nunca retornam resposta HTTP. O controller valida → chama a ação → monta a resposta com a trait `ApiResponser`. Escritas usam `DB::transaction`.

**Tech Stack:** PHP 8.2, Laravel 13, PHPUnit 12, JWT (tymon/jwt-auth), Laravel Pint.

**Spec:** `docs/superpowers/specs/2026-08-20-actions-auth-design.md`

## Global Constraints

- Nomenclatura de ações: `UserCreate`, `UserLogin`, `UserUpdateProfile`, `UserChangePassword` — domínio primeiro, sem sufixo `Action`, em `app/Actions/`.
- Ações são `final readonly class` e incluem `declare(strict_types=1);` na primeira linha do arquivo.
- Validação de request fica em FormRequests; validação de negócio fica na ação.
- Mensagens de erro em pt-BR devem ser preservadas exatamente.
- Escritas (`create`/`update`) usam `DB::transaction`.
- Não instalar pacote `nunomaduro/essentials`. Não criar comando `make:action`.
- Testes unitários de ações vão em `tests/Unit/Actions/` e usam `RefreshDatabase` (e `seed()` quando precisarem de dados do seeder).
- Rodar suite com `vendor/bin/phpunit`.
- **`JWTAuth::user()` retorna `null` neste projeto (verificado empiricamente)** — usar `Auth::user()` / `auth('api')->user()` no controller. Não reintroduzir `JWTAuth::user()` na Task 6.

---

### Task 1: Action `UserCreate`

**Files:**
- Create: `app/Actions/UserCreate.php`
- Test: `tests/Unit/Actions/UserCreateTest.php`

**Interfaces:**
- Consumes: `App\Models\User`, `Illuminate\Support\Facades\DB`, `Illuminate\Support\Facades\Hash`.
- Produces: `App\Actions\UserCreate::handle(array $data): User` — cria usuário com role `user`, senha hashada e dados opcionais (`phone`, `company`, `position`) quando fornecidos.

- [ ] **Step 1: Write the failing test**

`tests/Unit/Actions/UserCreateTest.php`:

```php
<?php

namespace Tests\Unit\Actions;

use App\Actions\UserCreate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserCreateTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_a_user_with_default_role(): void
    {
        $user = (new UserCreate())->handle([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'email' => 'test@example.com',
            'role' => 'user',
        ]);

        $this->assertTrue(Hash::check('password123', $user->password));
    }

    public function test_it_sets_optional_fields_when_provided(): void
    {
        $user = (new UserCreate())->handle([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'phone' => '+55 11 99999-9999',
            'company' => 'ACME',
            'position' => 'Dev',
        ]);

        $this->assertEquals('+55 11 99999-9999', $user->phone);
        $this->assertEquals('ACME', $user->company);
        $this->assertEquals('Dev', $user->position);
    }

    public function test_it_leaves_optional_fields_null_when_omitted(): void
    {
        $user = (new UserCreate())->handle([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $this->assertNull($user->phone);
        $this->assertNull($user->company);
        $this->assertNull($user->position);
    }
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `vendor/bin/phpunit tests/Unit/Actions/UserCreateTest.php -v`
Expected: FAIL with "Class \"App\\Actions\\UserCreate\" not found".

- [ ] **Step 3: Write minimal implementation**

`app/Actions/UserCreate.php`:

```php
<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

final readonly class UserCreate
{
    public function handle(array $data): User
    {
        return DB::transaction(fn () => User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'user',
            'phone' => $data['phone'] ?? null,
            'company' => $data['company'] ?? null,
            'position' => $data['position'] ?? null,
        ]));
    }
}
```

- [ ] **Step 4: Run test to verify it passes**

Run: `vendor/bin/phpunit tests/Unit/Actions/UserCreateTest.php -v`
Expected: PASS (3 tests).

- [ ] **Step 5: Commit**

```bash
git add app/Actions/UserCreate.php tests/Unit/Actions/UserCreateTest.php
git commit -m "feat(actions): add UserCreate action"
```

---

### Task 2: Action `UserLogin`

**Files:**
- Create: `app/Actions/UserLogin.php`
- Test: `tests/Unit/Actions/UserLoginTest.php`

**Interfaces:**
- Consumes: `App\Models\User`, `Illuminate\Support\Facades\Hash`.
- Produces: `App\Actions\UserLogin::handle(string $email, string $password): ?User` — retorna o `User` se as credenciais estiverem corretas, `null` caso contrário. **Nota:** a checagem de `is_active` NÃO fica nesta ação; fica no controller (Task 6) para preservar as duas mensagens pt-BR distintas ("credenciais incorretas" vs "conta desativada").

- [ ] **Step 1: Write the failing test**

`tests/Unit/Actions/UserLoginTest.php`:

```php
<?php

namespace Tests\Unit\Actions;

use App\Actions\UserLogin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserLoginTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_it_returns_user_with_valid_credentials(): void
    {
        $user = (new UserLogin())->handle('pedro@email.com', 'password');

        $this->assertNotNull($user);
        $this->assertEquals('pedro@email.com', $user->email);
    }

    public function test_it_returns_null_with_wrong_password(): void
    {
        $this->assertNull((new UserLogin())->handle('pedro@email.com', 'wrongpassword'));
    }

    public function test_it_returns_null_with_nonexistent_email(): void
    {
        $this->assertNull((new UserLogin())->handle('nobody@email.com', 'password'));
    }

    public function test_it_returns_user_even_when_inactive(): void
    {
        $user = User::where('email', 'pedro@email.com')->first();
        $user->update(['is_active' => false]);

        $returned = (new UserLogin())->handle('pedro@email.com', 'password');

        $this->assertNotNull($returned);
        $this->assertFalse($returned->is_active);
    }
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `vendor/bin/phpunit tests/Unit/Actions/UserLoginTest.php -v`
Expected: FAIL with "Class \"App\\Actions\\UserLogin\" not found".

- [ ] **Step 3: Write minimal implementation**

`app/Actions/UserLogin.php`:

```php
<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

final readonly class UserLogin
{
    public function handle(string $email, string $password): ?User
    {
        $user = User::where('email', $email)->first();

        if (! $user || ! Hash::check($password, $user->password)) {
            return null;
        }

        return $user;
    }
}
```

- [ ] **Step 4: Run test to verify it passes**

Run: `vendor/bin/phpunit tests/Unit/Actions/UserLoginTest.php -v`
Expected: PASS (4 tests).

- [ ] **Step 5: Commit**

```bash
git add app/Actions/UserLogin.php tests/Unit/Actions/UserLoginTest.php
git commit -m "feat(actions): add UserLogin action"
```

---

### Task 3: Action `UserUpdateProfile`

**Files:**
- Create: `app/Actions/UserUpdateProfile.php`
- Test: `tests/Unit/Actions/UserUpdateProfileTest.php`

**Interfaces:**
- Consumes: `App\Models\User`.
- Produces: `App\Actions\UserUpdateProfile::handle(User $user, array $data): User` — atualiza apenas os campos presentes em `$data` e retorna `$user->fresh()`.

- [ ] **Step 1: Write the failing test**

`tests/Unit/Actions/UserUpdateProfileTest.php`:

```php
<?php

namespace Tests\Unit\Actions;

use App\Actions\UserUpdateProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserUpdateProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_updates_only_the_provided_fields(): void
    {
        $user = User::factory()->create(['name' => 'Original', 'phone' => null]);

        $updated = (new UserUpdateProfile())->handle($user, ['name' => 'Updated']);

        $this->assertEquals('Updated', $updated->name);
        $this->assertNull($updated->phone);
    }

    public function test_it_returns_a_fresh_user_instance(): void
    {
        $user = User::factory()->create(['name' => 'Original']);

        $updated = (new UserUpdateProfile())->handle($user, ['name' => 'Updated']);

        $this->assertNotSame($user, $updated);
        $this->assertEquals('Updated', $updated->name);
    }
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `vendor/bin/phpunit tests/Unit/Actions/UserUpdateProfileTest.php -v`
Expected: FAIL with "Class \"App\\Actions\\UserUpdateProfile\" not found".

- [ ] **Step 3: Write minimal implementation**

`app/Actions/UserUpdateProfile.php`:

```php
<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\User;
use Illuminate\Support\Facades\DB;

final readonly class UserUpdateProfile
{
    public function handle(User $user, array $data): User
    {
        return DB::transaction(function () use ($user, $data) {
            $user->update($data);

            return $user->fresh();
        });
    }
}
```

- [ ] **Step 4: Run test to verify it passes**

Run: `vendor/bin/phpunit tests/Unit/Actions/UserUpdateProfileTest.php -v`
Expected: PASS (2 tests).

- [ ] **Step 5: Commit**

```bash
git add app/Actions/UserUpdateProfile.php tests/Unit/Actions/UserUpdateProfileTest.php
git commit -m "feat(actions): add UserUpdateProfile action"
```

---

### Task 4: Action `UserChangePassword`

**Files:**
- Create: `app/Actions/UserChangePassword.php`
- Test: `tests/Unit/Actions/UserChangePasswordTest.php`

**Interfaces:**
- Consumes: `App\Models\User`, `Illuminate\Support\Facades\Hash`, `Illuminate\Validation\ValidationException`.
- Produces: `App\Actions\UserChangePassword::handle(User $user, string $currentPassword, string $newPassword): void` — lança `ValidationException` com a mensagem atual se a senha atual estiver errada; caso contrário, troca a senha.

- [ ] **Step 1: Write the failing test**

`tests/Unit/Actions/UserChangePasswordTest.php`:

```php
<?php

namespace Tests\Unit\Actions;

use App\Actions\UserChangePassword;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class UserChangePasswordTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_changes_the_password(): void
    {
        $user = User::factory()->create(['password' => Hash::make('oldpassword')]);

        (new UserChangePassword())->handle($user, 'oldpassword', 'newpassword');

        $this->assertTrue(Hash::check('newpassword', $user->fresh()->password));
    }

    public function test_it_throws_when_current_password_is_wrong(): void
    {
        $this->expectException(ValidationException::class);

        $user = User::factory()->create(['password' => Hash::make('oldpassword')]);

        (new UserChangePassword())->handle($user, 'wrongpassword', 'newpassword');
    }
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `vendor/bin/phpunit tests/Unit/Actions/UserChangePasswordTest.php -v`
Expected: FAIL with "Class \"App\\Actions\\UserChangePassword\" not found".

- [ ] **Step 3: Write minimal implementation**

`app/Actions/UserChangePassword.php`:

```php
<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

final readonly class UserChangePassword
{
    public function handle(User $user, string $currentPassword, string $newPassword): void
    {
        DB::transaction(function () use ($user, $currentPassword, $newPassword) {
            if (! Hash::check($currentPassword, $user->password)) {
                throw ValidationException::withMessages([
                    'current_password' => ['A senha atual está incorreta.'],
                ]);
            }

            $user->update(['password' => Hash::make($newPassword)]);
        });
    }
}
```

- [ ] **Step 4: Run test to verify it passes**

Run: `vendor/bin/phpunit tests/Unit/Actions/UserChangePasswordTest.php -v`
Expected: PASS (2 tests).

- [ ] **Step 5: Commit**

```bash
git add app/Actions/UserChangePassword.php tests/Unit/Actions/UserChangePasswordTest.php
git commit -m "feat(actions): add UserChangePassword action"
```

---

### Task 5: FormRequests `UpdateProfileRequest` e `ChangePasswordRequest`

**Files:**
- Create: `app/Http/Requests/Auth/UpdateProfileRequest.php`
- Create: `app/Http/Requests/Auth/ChangePasswordRequest.php`

**Interfaces:**
- Consumes: `Illuminate\Foundation\Http\FormRequest` (mesmo padrão dos `RegisterRequest`/`LoginRequest` existentes).
- Produces: `App\Http\Requests\Auth\UpdateProfileRequest` (autoriza `true`, regras de `name/phone/company/position/avatar`) e `App\Http\Requests\Auth\ChangePasswordRequest` (autoriza `true`, regras de `current_password/password/confirmed`).

- [ ] **Step 1: Write `UpdateProfileRequest`**

`app/Http/Requests/Auth/UpdateProfileRequest.php`:

```php
<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'phone' => 'sometimes|string|max:20',
            'company' => 'sometimes|string|max:255',
            'position' => 'sometimes|string|max:255',
            'avatar' => 'sometimes|string|max:255',
        ];
    }
}
```

- [ ] **Step 2: Write `ChangePasswordRequest`**

`app/Http/Requests/Auth/ChangePasswordRequest.php`:

```php
<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class ChangePasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ];
    }
}
```

- [ ] **Step 3: Verify suite still passes**

Run: `vendor/bin/phpunit`
Expected: PASS (todas as suítes existentes; novos FormRequests não devem quebrar nada).

- [ ] **Step 4: Commit**

```bash
git add app/Http/Requests/Auth/UpdateProfileRequest.php app/Http/Requests/Auth/ChangePasswordRequest.php
git commit -m "feat(requests): add UpdateProfileRequest and ChangePasswordRequest"
```

---

### Task 6: Refatorar `AuthController`

**Files:**
- Modify: `app/Http/Controllers/Api/AuthController.php`

**Interfaces:**
- Consumes: `App\Actions\UserCreate`, `App\Actions\UserLogin`, `App\Actions\UserUpdateProfile`, `App\Actions\UserChangePassword`; `App\Http\Requests\Auth\UpdateProfileRequest`, `App\Http\Requests\Auth\ChangePasswordRequest`; `App\Models\User`; `Tymon\JWTAuth\Facades\JWTAuth`; `Illuminate\Validation\ValidationException`.
- Produces: Controller fino — `register`, `login`, `updateProfile` e `changePassword` delegam às ações. `me`, `refresh`, `logout`, `forgotPassword`, `resetPassword` e `respondWithToken` permanecem como estão.

- [ ] **Step 1: Rewrite the controller**

Substituir o conteúdo inteiro de `app/Http/Controllers/Api/AuthController.php` por:

```php
<?php

namespace App\Http\Controllers\Api;

use App\Actions\UserChangePassword;
use App\Actions\UserCreate;
use App\Actions\UserLogin;
use App\Actions\UserUpdateProfile;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ChangePasswordRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\UpdateProfileRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register', 'forgotPassword', 'resetPassword']]);
    }

    /**
     * Get a JWT via given credentials.
     */
    public function login(LoginRequest $request)
    {
        $user = (new UserLogin())->handle($request->email, $request->password);

        if (! $user) {
            throw ValidationException::withMessages([
                'email' => ['As credenciais fornecidas estão incorretas.'],
            ]);
        }

        if (! $user->is_active) {
            throw ValidationException::withMessages([
                'email' => ['Sua conta está desativada. Entre em contato com o suporte.'],
            ]);
        }

        $token = JWTAuth::fromUser($user);

        return $this->respondWithToken($token, $user);
    }

    /**
     * Register a new user.
     */
    public function register(RegisterRequest $request)
    {
        $user = (new UserCreate())->handle($request->validated());

        $token = JWTAuth::fromUser($user);

        return $this->respondWithToken($token, $user)->setStatusCode(201);
    }

    /**
     * Log the user out (Invalidate the token).
     */
    public function logout(Request $request)
    {
        Auth::guard('api')->logout();

        return response()->json([
            'message' => 'Logout realizado com sucesso.',
        ]);
    }

    /**
     * Refresh a token.
     */
    public function refresh()
    {
        $token = JWTAuth::parseToken()->refresh();

        return $this->respondWithToken($token);
    }

    /**
     * Get the authenticated User.
     */
    public function me(Request $request)
    {
        $user = Auth::user();

        return response()->json([
            'data' => $user,
        ]);
    }

    /**
     * Update user profile.
     */
    public function updateProfile(UpdateProfileRequest $request)
    {
        $user = (new UserUpdateProfile())->handle(Auth::user(), $request->validated());

        return response()->json([
            'message' => 'Perfil atualizado com sucesso.',
            'data' => $user,
        ]);
    }

    /**
     * Change user password.
     */
    public function changePassword(ChangePasswordRequest $request)
    {
        $user = Auth::user();

        (new UserChangePassword())->handle($user, $request->current_password, $request->password);

        return response()->json([
            'message' => 'Senha alterada com sucesso.',
        ]);
    }

    /**
     * Send password reset link.
     */
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        return response()->json([
            'message' => 'Se o email existir, um link de redefinição foi enviado.',
        ]);
    }

    /**
     * Reset password.
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        return response()->json([
            'message' => 'Senha redefinida com sucesso.',
        ]);
    }

    /**
     * Get token structure.
     */
    protected function respondWithToken($token, $user = null)
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Autenticação realizada com sucesso.',
            'data' => [
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => JWTAuth::factory()->getTTL() * 60,
                'user' => $user ?? auth('api')->user(),
            ],
        ]);
    }
}
```

- [ ] **Step 2: Run Auth feature tests to verify behavior is preserved**

Run: `vendor/bin/phpunit tests/Feature/Auth -v`
Expected: PASS (todos os testes de Auth existentes — LoginTest, RegisterTest, LogoutTest, ProfileTest).

- [ ] **Step 3: Commit**

```bash
git add app/Http/Controllers/Api/AuthController.php
git commit -m "refactor(auth): delegate business logic to actions"
```

---

### Task 7: Regressão completa e Pint

**Files:**
- Nenhum arquivo de código — verificação.

**Interfaces:**
- Consumes: todas as tarefas anteriores.

- [ ] **Step 1: Run full test suite**

Run: `vendor/bin/phpunit`
Expected: PASS (todas as suítes Unit e Feature, incluindo as novas de Actions).

- [ ] **Step 2: Run Pint (dry-run) nos arquivos novos/alterados**

Run: `vendor/bin/pint --test app/Actions app/Http/Controllers/Api/AuthController.php app/Http/Requests/Auth`
Expected: sem erros de estilo. Se apontar diferenças, rodar `vendor/bin/pint app/Actions app/Http/Controllers/Api/AuthController.php app/Http/Requests/Auth` para corrigir e re-rodar a suite.

- [ ] **Step 3: Commit final (se Pint alterou algo)**

```bash
git add -A
git commit -m "style: pint fixes"
```
(Se Pint não alterou nada, pular este commit.)