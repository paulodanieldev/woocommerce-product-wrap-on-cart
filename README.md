# instalar plugin Advanced Custom Fields.
 - criar um novo grupo de campos de embalagem para presente que será adicionado aos produtos.
 - criar um campo:
   - Nome do campo: embalagem_produto.
    - ATENÇÃO: pode ser criado com qualquer nome e posteriormente configurado no plugin.
   - Tipo de campo: Relação.
   - Filtros: nenhum.
   - Filtrar por taxinomia: categoria embalagem.
   - Qtde. máxima de posts: 1 (apenas um tipo de embalagem por produto).
 - localização: Tipo de Post - é igual a - Produto.
# criar nova categoria Embalagem.
 - ATENÇÃO: pode ser criada com qualquer nome, mas posteriormente o nome desta categoria deve ser adicionada na configuração do plugin.
# criar produtos embalagem.
 - quando criar o produto salvar ele com a categoria embalagem criada na etapa anterior.
 - configurar em Dados do produto/inventário como:
   - Gerenciar estoque?: ativar a opção.
    - ATENÇÃO: o plugin só funciona com controle de estoque.
   - Quantidade em estoque: sua quantidade de embalagens.
# configurar os produtos que oferecerão embalagem
 - na edição dos produtos aparecerá uma opção onde poderá escolher qual embalagem será oferecido junto a este produto.
# instalar o plugin InCuca Tech - Woocommerce product wrap on cart.
 - salvar nas configurações do plugin o nome da categoria criada (Obrigatório).
 - salvar nas configurações do plugin o nome do custom field criado na primeira etapa (Obrigatório).
 - verificar as outras opções e configurar de acordo com suas preferências.
  - se os campos não obrigatórios ficarem em branco, exibirá no carrinho os valores default.
 - quando tiver dúvidas verifica o icone de ajuda ao lado do campo.
# alterar template do cart.php
 - utilizar o arquivo templates/cart/cart.php como referência, as alterações nescessessárias estão entre comentários.