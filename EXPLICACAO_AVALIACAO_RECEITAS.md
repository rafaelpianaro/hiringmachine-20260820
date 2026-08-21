# Ajuste de avaliação de receitas: fluxo de rating, listagem e sincronização

## Contexto do problema

No frontend do projeto, a área de receitas exibiu um problema recorrente: quando o usuário logado avaliava uma receita no modal, a média e o contador de avaliações não refletiam corretamente na listagem. Em alguns casos, a receita recebia a nota no backend, mas o card da grid continuava com estrelas em branco.

A análise do fluxo mostrou que havia dois pontos principais:

1. O endpoint de listagem não estava retornando a relação `ratings` junto com as receitas.
2. O componente de estrelas (`StarRating`) estava renderizando com o valor errado no estado visual, priorizando `modelValue` em vez da média da receita quando o componente era usado em modo de leitura.

## Causa raiz

### 1) API não carregava as avaliações na listagem

As rotas de listagem de receitas (`GET /api/v1/recipes`, `GET /api/v1/recipes/my-recipes`, filtro por categoria/dificuldade) estavam retornando principalmente o usuário da receita, mas não a coleção `ratings`.

Como consequência, o frontend recebia `ratings: []` e calculava:

- quantidade de avaliações = 0
- média = 0

Isso quebrava a UI da grid e do modal, mesmo quando o registro já existia na tabela `recipe_ratings`.

### 2) Componente de estrelas priorizava o estado errado

O `StarRating` foi implementado com lógica que, em modo readonly, usava o `modelValue` ou a média sem garantir a normalização da informação. Em alguns cenários, como cards de grid, a média da receita ficou em 0 ou em um valor não numérico, resultando em estrelas vazias.

Além disso, o componente tinha `max` configurado para 3, enquanto o sistema de avaliação usa escala de 1 a 5.

## Correção aplicada

### Backend

Mantive a regra de negócio fora do controller, seguindo o padrão Action.

Criei ações para listar receitas e deleguei a lógica para elas:

- `app/Actions/RecipeList.php`
- `app/Actions/RecipeListByCategory.php`
- `app/Actions/RecipeListByDifficulty.php`

Essas actions carregam a relação `ratings.user`, permitindo que a API retorne os dados necessários para a UI.

### Frontend

No frontend, ajustei o fluxo para manter a listagem e o modal sincronizados com o mesmo estado da receita:

- a seleção do modal usa o `id` da receita e busca o registro atual no store
- quando os `ratings` mudam, o modal atualiza o `userRating`
- a média foi calculada com normalização segura para evitar `NaN`
- `StarRating` passou a ter escala de 5 estrelas
- em modo de leitura, ele usa a média da receita como referência visual

## Padrão de arquitetura aplicado

O projeto segue uma abordagem limpa para separar responsabilidades:

- Controller: coordena request/response
- Action: encapsula lógica de negócio e consultas
- Store (Pinia): mantém o estado da aplicação no frontend
- Component: renderiza visualmente os dados

Esse desenho facilita manutenção, testes e entendimento do fluxo de dados.

## Observação importante para entrevista

A resposta esperada em uma entrevista é mostrar que a correção foi feita entendendo a causa raiz e não apenas o efeito visual:

- o dado estava sendo persistido corretamente no banco
- o problema era de serialização/estado do frontend
- a relação `ratings` faltava na listagem
- o componente de estrelas estava renderizando com o valor errado
- a solução foi robustecer o retorno da API e sincronizar o estado no cliente

## Resultado

Com essa correção:

- a média das avaliações aparece corretamente
- o número de avaliações é exibido na listagem
- o usuário logado consegue avaliar e ver a atualização refletida imediatamente
- a experiência do modal e da grid fica consistente
