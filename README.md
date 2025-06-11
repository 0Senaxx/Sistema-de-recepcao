# Sistema de Recepção da SEAD

Este repositório contém o código-fonte do sistema de recepção da SEAD (Secretaria de Administração e Gestão). O sistema é desenvolvido em PHP, com suporte a HTML, CSS e JavaScript, e tem como objetivo gerenciar o fluxo de visitantes, registro de visitas, gerenciamento de ramais e setores, além da autenticação de usuários.

## Estrutura de Pastas

Abaixo está a organização das pastas e arquivos principais do sistema:

```
/
├── 01-Login/
│   ├── autenticação.php
│   ├── index.php
│   ├── login.php
│   ├── logout.php
│   ├── verificar_login.php
│   ├── script.js
│   └── estilo.css
│
├── 02-Inicio/
│   ├── index.php
│   ├── registrar_saida.php
│   ├── script.js
│   └── estilo.css
│
├── 03-Registrar/
│   ├── buscar_visitante.php
│   ├── nova_visita.php
│   ├── salvar_visita.php
│   ├── script.js
│   └── style.css
│
├── 04-Visitantes/
│   ├── Fotos/ (pasta para armazenar fotos dos visitantes)
│   ├── atualizar_visitante.php
│   ├── aditar_visitante.php
│   ├── aditar_visitante.css
│   ├── aditar_visitante.js
│   ├── visitante.php
│   └── visitante.css
│
├── 05-Visitas/
│   ├── visitas.php
│   ├── script.js
│   └── estilo.css
│
├── 06-Ramais/
│   ├── ramais.php
│   ├── estilo.css
│   └── script.js
│
├── 07-Relatorio/
│   ├── exportar_excel.php
│   └── gerar_relatório.php
│
├── 08-Servidores/
│   ├── buscar_servidor.php
│   ├── excluir.php
│   ├── index.css
│   ├── index.php
│   ├── salvar.php
│   └── script.js
│
├── 09-Setores/
│   ├── excluir.php
│   ├── index.css
│   ├── index.php
│   └── salvar.php
│
├── libs/
│   └── fpdf/ (biblioteca para geração de PDFs)
│
├── uploads/ (pasta para armazenar uploads de imagens ou arquivos)
│
└── conexao.php (arquivo de conexão com o banco de dados)
```

## Funcionalidades

✅ Autenticação de usuários
✅ Controle de login e logout
✅ Cadastro e atualização de visitantes
✅ Registro de visitas com hora de entrada e saída
✅ Relatórios em Excel e PDF
✅ Gestão de ramais e setores por departamento
✅ Gestão de servidores vinculados a setores
✅ Upload e armazenamento de fotos de visitantes

## Tecnologias Utilizadas

* PHP
* MySQL
* HTML, CSS, JavaScript
* Biblioteca FPDF para geração de relatórios

## Observações

O sistema está em desenvolvimento e pode sofrer alterações. Sinta-se à vontade para abrir issues ou pull requests para sugerir melhorias.

## Como Executar

1. Clone o repositório:

   ```bash
   git clone https://github.com/seu-usuario/nome-do-repositorio.git
   ```
2. Configure o arquivo `conexao.php` com as credenciais do banco de dados.
3. Execute em um servidor web local (XAMPP, WAMP, Laragon ou similar).
4. Acesse a aplicação via navegador.
