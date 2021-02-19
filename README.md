# criar nova categoria Embalagem.
# criar produtos embalagem.
 - configurar em Dados do produto/inventário como:
   - Gerenciar estoque?: ativar a opção.
   - Quantidade em estoque: sua quantidade de embalagens.
# instalar o plugin InCuca Tech - Woocommerce product wrap on cart.
 - salvar nas configurações do plugin o nome da categoria criada.
# instalar plugin Advanced Custom Fields.
 - criar um novo grupo de campos de embalagempara presente que será adicionado aos produtos.
 - criar um campo:
   - Nome do campo: embalagem_produto.
   - Tipo de campo: Relação
   - Filtros: nenhum.
   - Filtrar por taxinomia: categoria embalagem.
   - Qtde. máxima de posts: 1 (apenas um tipo de embalagem por presente).
 - localização: Tipo de Post - é igual a - Produto.
# alterar template do cart.php