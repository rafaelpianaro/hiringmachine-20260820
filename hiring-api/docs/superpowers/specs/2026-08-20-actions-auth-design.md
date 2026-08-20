# Design: Action-based architecture para o domínio Auth

**Data:** 2026-08-20
**Projeto:** HiringMachine API (hiring-api)
**Contexto:** Refatoração da lógica de negócio do domínio Auth/User para o padrão de Actions, baseado no `nunomaduro/essentials` (sem instalar o pacote).

## Objetivo

Cada ação executa **uma única tarefa** de negócio (ex.: `UserCreate` = criação de usuário e somente). Controllers ficam finos e as regras de negócio ficam isoladas, testáveis por unidade e reutilizáveis.

## Escopo

Somente o domínio **Auth/User** do `app/Http/Controllers/Api/AuthController.php`. Demais domínios (Job, Application, Recipe, Comment) ficam para iterações futuras.

## Abordagem escolhida

B — Domínio Auth completo:
- Toda a lógica de negócio do `AuthController` vira ação.
- Validações inline de profile/password viram FormRequests dedicados.
- Operações triviais (token/resposta) permanecem no controller.

## Estrutura

```
app/Actions/
├── UserCreate.php            # cria usuário, retorna User
├── UserLogin.php             # valida credenciais + checa is_active, retorna User|null
├── UserUpdateProfile.php     # atualiza perfil do usuário
└── UserChangePassword.php    # verifica senha atual e troca

app/Http/Requests/Auth/
├── LoginRequest.php          # já existe
├── RegisterRequest.php       # já existe
├── UpdateProfileRequest.php  # novo
└── ChangePasswordRequest.php # novo
```

## Regras

- Ação é `final readonly class` com método `handle()` (padrão essentials).
- Ação recebe dados **já validados** (`array` ou tipos simples) e executa uma única tarefa.
- Ação **não** retorna resposta HTTP — retorna modelo/resultado ou lança exceção (`ValidationException`).
- Controller valida → chama a ação → monta resposta com `ApiResponser`.
- Escritas usam `DB::transaction`; leituras/verificações não.
- Validação fica **fora** da action (FormRequest), exceto validações de negócio que exigem estado (ex.: senha atual correta).

**Fluxo:** `Request → FormRequest (validação) → Controller → Action → Model → Response`

## Ações

### UserCreate

```php
final readonly class UserCreate
{
    public function handle(array $data): User
    {
        return DB::transaction(fn () => User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'role'     => 'user',
            'phone'    => $data['phone'] ?? null,
            'company'  => $data['company'] ?? null,
            'position' => $data['position'] ?? null,
        ]));
    }
}
```

### UserLogin

```php
final readonly class UserLogin
{
    public function handle(string $email, string $password): ?User
    {
        $user = User::where('email', $email)->first();

        if (! $user || ! Hash::check($password, $user->password)) {
            return null;
        }

        return $user->is_active ? $user : null;
    }
}
```

Usa `Hash::check` (mesmo resultado de `JWTAuth::attempt`) para não acoplar JWT à ação — o token continua no controller.

### UserUpdateProfile

```php
final readonly class UserUpdateProfile
{
    public function handle(User $user, array $data): User
    {
        $user->update($data);

        return $user->fresh();
    }
}
```

### UserChangePassword

```php
final readonly class UserChangePassword
{
    public function handle(User $user, string $currentPassword, string $newPassword): void
    {
        if (! Hash::check($currentPassword, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['A senha atual está incorreta.'],
            ]);
        }

        $user->update(['password' => Hash::make($newPassword)]);
    }
}
```

## Refatoração do AuthController

| Método | Ação usada | Mudança |
|---|---|---|
| `register` | `UserCreate` | `handle($request->validated())` → token → 201 |
| `login` | `UserLogin` | se `null`, lança `ValidationException` (mensagens atuais preservadas) |
| `updateProfile` | `UserUpdateProfile` | `handle(JWTAuth::user(), $request->validated())` |
| `changePassword` | `UserChangePassword` | `handle($user, $request->current_password, $request->password)` |
| `me`, `refresh`, `logout`, `forgotPassword`, `resetPassword` | — | permanecem no controller |

## FormRequests novos

- `UpdateProfileRequest` — regras atuais de `name/phone/company/position/avatar`.
- `ChangePasswordRequest` — regras atuais de `current_password/password/confirmed`.

## Tratamento de erros

- Validação de request → `FormRequest` → 422 automático.
- Lógica inválida na ação → a ação lança `ValidationException`; o middleware `ForceJsonResponse` devolve 422 JSON.
- Mensagens em pt-BR preservadas.
- Controller decide status HTTP final (`201`, `200`, `401`, etc.).

## Testes

- **Unit tests novos** (`tests/Unit/Actions/`):
  - `UserCreateTest` — cria com role `user`, senha hashada, dados opcionais.
  - `UserLoginTest` — credenciais corretas → user; senha errada → null; inativo → null.
  - `UserUpdateProfileTest` — atualiza só os campos passados.
  - `UserChangePasswordTest` — troca senha; senha atual errada → `ValidationException`.
- **Feature tests existentes** (`tests/Feature/Auth/*`) continuam passando sem alteração — regressão do refactor.

## Fora de escopo

- Demais domínios (Job, Application, Recipe, Comment).
- Instalação do pacote `nunomaduro/essentials`.
- Comando `make:action`.