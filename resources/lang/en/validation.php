<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Linhas de Linguagem de Validação
    |--------------------------------------------------------------------------
    |
    | As seguintes linhas de linguagem contêm as mensagens de erro padrão usadas pela
    | classe de validação. Algumas destas regras têm múltiplas versões
    | (como as regras de tamanho). Sinta-se à vontade para ajustar cada uma destas mensagens.
    |
    */

    // 'accepted' => 'O campo :attribute deve ser aceite.', // Usado para caixas "Aceito os Termos"
    // 'active_url' => 'O campo :attribute não é um URL válido.',
    // 'after' => 'O campo :attribute deve ser uma data posterior a :date.',
    // 'after_or_equal' => 'O campo :attribute deve ser uma data posterior ou igual a :date.',
    'alpha' => 'O campo :attribute deve conter apenas letras.',
    'alpha_dash' => 'O campo :attribute deve conter apenas letras, números, traços e underscores.',
    'alpha_num' => 'O campo :attribute deve conter apenas letras e números.',
    // 'array' => 'O campo :attribute deve ser um array.',
    // 'before' => 'O campo :attribute deve ser uma data anterior a :date.',
    // 'before_or_equal' => 'O campo :attribute deve ser uma data anterior ou igual a :date.',
    'between' => [ // Usado para números, ficheiros ou texto
        'numeric' => 'O campo :attribute deve estar entre :min e :max.',
        'file'    => 'O campo :attribute deve ter entre :min e :max kilobytes.',
        'string'  => 'O campo :attribute deve ter entre :min e :max caracteres.',
        // 'array'   => 'O campo :attribute deve ter entre :min e :max itens.',
    ],
    'boolean' => 'O campo :attribute deve ser verdadeiro ou falso.', // Usado para checkboxes (sim/não)
    'confirmed' => 'A confirmação do campo :attribute não coincide.', // Usado para "Confirmar Password"
    'current_password' => 'A password atual está incorreta.', // Usado para validar a password antiga
    'date' => 'O campo :attribute não é uma data válida.',
    // 'date_equals' => 'O campo :attribute deve ser uma data igual a :date.',
    // 'date_format' => 'O campo :attribute não corresponde ao formato :format.',
    // 'different' => 'Os campos :attribute e :other devem ser diferentes.',
    'digits' => 'O campo :attribute deve ter :digits dígitos.', // Útil para o telemóvel
    'digits_between' => 'O campo :attribute deve ter entre :min e :max dígitos.',
    'dimensions' => 'O campo :attribute tem dimensões de imagem inválidas.', // Para uploads de imagens
    // 'distinct' => 'O campo :attribute tem um valor duplicado.',
    'email' => 'O campo :attribute deve ser um endereço de email válido.', // Essencial
    'ends_with' => 'O campo :attribute deve terminar com um dos seguintes: :values.',
    'exists' => 'O :attribute selecionado é inválido.', // Usado para verificar se um ID existe (ex: se a freguesia_id existe na tabela freguesias)
    'file' => 'O campo :attribute deve ser um ficheiro.', // Para uploads
    // 'filled' => 'O campo :attribute deve ter um valor.',
    'gt' => [ // "Greater Than" (Maior que)
        'numeric' => 'O campo :attribute deve ser maior que :value.',
        'file'    => 'O campo :attribute deve ser maior que :value kilobytes.',
        'string'  => 'O campo :attribute deve ter mais de :value caracteres.',
        // 'array'   => 'O campo :attribute deve ter mais de :value itens.',
    ],
    'gte' => [ // "Greater Than or Equal" (Maior ou Igual a)
        'numeric' => 'O campo :attribute deve ser maior ou igual a :value.',
        // ...
    ],
    'image' => 'O campo :attribute deve ser uma imagem.', // Para uploads de imagens (ex: anexo de ticket)
    'in' => 'O :attribute selecionado é inválido.', // Usado para ENUMs (ex: 'perfil' deve estar em ['admin', 'cimbb', 'freguesia'])
    // 'in_array' => 'O campo :attribute não existe em :other.',
    'integer' => 'O campo :attribute deve ser um número inteiro.', // Útil para 'ano_instalacao', contagem de pessoas
    // 'ip' => 'O campo :attribute deve ser um endereço IP válido.',
    // 'ipv4' => 'O campo :attribute deve ser um endereço IPv4 válido.',
    // 'ipv6' => 'O campo :attribute deve ser um endereço IPv6 válido.',
    // 'json' => 'O campo :attribute deve ser uma string JSON válida.',
    'lt' => [ // "Less Than" (Menor que)
        'numeric' => 'O campo :attribute deve ser menor que :value.',
        // ...
    ],
    'lte' => [ // "Less Than or Equal" (Menor ou Igual a)
        'numeric' => 'O campo :attribute deve ser menor ou igual a :value.',
        // ...
    ],
    'max' => [ // Limite Máximo
        'numeric' => 'O campo :attribute não pode ser maior que :max.',
        'file'    => 'O campo :attribute não pode ser maior que :max kilobytes.',
        'string'  => 'O campo :attribute não pode ter mais de :max caracteres.', // Útil para o 'nome' (max:15)
        'array'   => 'O campo :attribute não pode ter mais de :max itens.',
    ],
    'mimes' => 'O campo :attribute deve ser um ficheiro do tipo: :values.', // Para uploads (ex: pdf, jpg, png)
    'mimetypes' => 'O campo :attribute deve ser um ficheiro do tipo: :values.',
    'min' => [ // Limite Mínimo
        'numeric' => 'O campo :attribute deve ser pelo menos :min.', // Útil para números (ex: min:0)
        'file'    => 'O campo :attribute deve ter pelo menos :min kilobytes.',
        'string'  => 'O campo :attribute deve ter pelo menos :min caracteres.', // Útil para 'password'
        'array'   => 'O campo :attribute deve ter pelo menos :min itens.',
    ],
    // 'multiple_of' => 'O campo :attribute deve ser um múltiplo de :value.',
    // 'not_in' => 'O :attribute selecionado é inválido.',
    'not_regex' => 'O formato do campo :attribute é inválido.',
    'numeric' => 'O campo :attribute deve ser um número.',
    'password' => 'A password está incorreta.', // (Esta é a regra 'password.confirm', não a 'current_password')
    'present' => 'O campo :attribute deve estar presente.',
    // 'regex' => 'O formato do campo :attribute é inválido.',
    'required' => 'O campo :attribute é obrigatório.', // Essencial
    'required_if' => 'O campo :attribute é obrigatório quando :other é :value.',
    // ... (outras regras 'required_')
    'same' => 'Os campos :attribute e :other devem coincidir.', // Usado internamente pelo 'confirmed' (password_confirmation)
    'size' => [ // Tamanho Exato
        'numeric' => 'O campo :attribute deve ser :size.',
        'file'    => 'O campo :attribute deve ter :size kilobytes.',
        'string'  => 'O campo :attribute deve ter :size caracteres.',
        'array'   => 'O campo :attribute deve conter :size itens.',
    ],
    // 'starts_with' => 'O campo :attribute deve começar com um dos seguintes: :values.',
    'string' => 'O campo :attribute deve ser texto.', // Essencial
    'timezone' => 'O campo :attribute deve ser um fuso horário válido.',
    'unique' => 'O :attribute já está a ser utilizado.', // Essencial (para email, código da família)
    'uploaded' => 'Ocorreu um erro ao carregar o ficheiro :attribute.',
    'url' => 'O campo :attribute deve ser um URL válido.',
    // 'uuid' => 'O campo :attribute deve ser um UUID válido.',

    /*
    |--------------------------------------------------------------------------
    | Linhas de Validação Personalizadas
    |--------------------------------------------------------------------------
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Atributos de Validação Personalizados
    |--------------------------------------------------------------------------
    |
    | As seguintes linhas de linguagem são usadas para trocar o :attribute
    | por algo mais legível, como "Endereço de E-Mail" em vez de "email".
    |
    */

    'attributes' => [
        // Adiciona aqui os teus campos para que as mensagens de erro fiquem amigáveis
        'nome' => 'Nome',
        'email' => 'E-mail',
        'password' => 'Password',
        'telemovel' => 'Telemóvel',
        
        'freguesia_id' => 'Freguesia',
        'concelho_id' => 'Concelho',
        
        'codigo' => 'Código da Família',
        'ano_instalacao' => 'Ano de Instalação',
        'nacionalidade' => 'Nacionalidade',
        'tipologia_habitacao' => 'Tipologia de Habitação',
        'tipologia_propriedade' => 'Tipologia de Propriedade',

        'adultos_laboral' => 'Nº de Adultos (Idade Laboral)',
        'adultos_65_mais' => 'Nº de Adultos (65+)',
        'criancas' => 'Nº de Crianças',
        
        'assunto' => 'Assunto',
        'descricao' => 'Descrição',
        'anexo' => 'Anexo',
        'categoria' => 'Categoria',
    ],

];