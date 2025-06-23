# Sistema de Recepção da SEAD

Este repositório contém o código-fonte do sistema de recepção da SEAD (Secretaria de Administração e Gestão). O sistema é desenvolvido em PHP, com suporte a HTML, CSS e JavaScript, e tem como objetivo gerenciar o fluxo de visitantes, registro de visitas, gerenciamento de ramais e setores, além da autenticação de usuários.

## 🚀 Funcionalidades

✅ Autenticação de usuários
✅ Controle de login e logout
✅ Cadastro e atualização de visitantes
✅ Registro de visitas com hora de entrada e saída
✅ Relatórios em Excel e PDF
✅ Gestão de ramais e setores por departamento
✅ Gestão de servidores vinculados a setores
✅ Upload e armazenamento de fotos de visitantes

## 🛠️ Tecnologias Utilizadas

### 🖥️ Frontend
* **HTML**: Linguagem de marcação para estruturação do conteúdo e elementos visuais.
* **CSS**: Linguagem de estilização para controle do layout.
* **JavaScript**: Linguagem de programação para integração de lógica e interatividade nas páginas.

### ⚡Backend
* **PHP**: Linguagem de programação para processamento do lado do servidor e integração com o banco de dados.
* **MySQL**: Sistema de gerenciamento de banco de dados utilizado para armazenar e organizar as informações do sistema.

## 📚 Bibliotecas Utilizadas

* **FPDF**: Gera relatórios e impressões em PDF direto no navegador.
* **Choices.js**: Melhora a navegação e usabilidade de campos de seleção (`<select>`).
* **jQuery**: Simplifica manipulação do DOM, eventos e requisições AJAX.
* **jQuery Mask**: Adiciona máscaras a campos de texto (ex.: CPF, CNPJ, telefone).
* **Chart.js**: Cria gráficos interativos e responsivos (linhas, barras, pizza, radar).

## 📁 Estrutura de Pastas

Abaixo está a organização das pastas e arquivos principais do sistema:

```
/
├── 01-Login/
│   ├── Auth/
│   │   ├── autenticação.php
│   │   ├── controle_sessao.php 
│   │   ├── logout.php 
│   │   └── verificar_login.php
│   │
│   ├── esqueci_senha.php
│   ├── expirar_senha.php
│   ├── index.php
│   ├── login.php
│   ├── primeiro_acesso.php
│   └── redefinir_senha.php
│
├── 02-Inicio/
│   ├── index.php
│   ├── registrar_saida.php
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
│   ├── visitantes.php
│   └── visitantes.css
│
├── 06-Ramais/
│   ├── ramais.php
│   ├── estilo.css
│   └── script.js
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
├── 10-Administrador/
│   ├── Documentos/
│   │   ├── documento.css
│   │   └── documento.php
|   |
│   ├── Setores/
│   │   ├── excluir.php
│   │   ├── index.css
│   │   ├── index.php
│   │   └── salvar.php
|   |
│   ├── Usuarios/
│   │   ├── alterar_status.php
│   │   ├── buscar_usuario.php
│   │   ├── excluir_usuario.php
│   │   ├── salvar_usuario.php
│   │   ├── usuario.css
│   │   └── usuario.php
|   |
│   ├── Visitas/
│   │   ├── Relatorios/
│   |   │   ├── exporter_excel.php
|   │   │   └── gerar_relatorio.php
|   |   |
│   │   ├── estilo.css
│   │   ├── script.js
│   │   └── visitas.php
|   |
│   ├── estilo.css
│   ├── get_top_setores.php
│   └── index.php
|   
├── libs/
│   └── fpdf/ (biblioteca para geração de PDFs)
│
├── uploads/ (pasta para armazenar uploads de imagens ou arquivos)
│
├── .htaccess
│
├── 404.php
│
├── conexao.php (arquivo de conexão com o banco de dados)
|
└── config.php

```

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
