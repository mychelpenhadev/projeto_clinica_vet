# Vida Pet - Sistema de Gestão Veterinária

O **Vida Pet** é um sistema web desenvolvido para auxiliar na gestão de clínicas veterinárias. Ele permite o cadastro de animais, controle de prontuários, agendamento de consultas e gerenciamento de planos de saúde veterinários.

## 🚀 Funcionalidades Principais

-   **Gestão de Animais**: Cadastro completo com foto, nome, espécie e dono.
-   **Prontuário Eletrônico**: Histórico detalhado de tratamentos e observações para cada paciente.
-   **Planos de Saúde**:
    -   **Amigo Fiel**: Cobertura básica.
    -   **Proteção Total**: Cobertura avançada com exames.
    -   **VIP Pet**: Serviços premium, incluindo spa e transporte.
-   **Agendamento**: Solicitação de serviços integrada aos planos contratados.
-   **Controle de Acesso**: Níveis de acesso para Administradores, Veterinários e Tutores (Donos).
-   **Carrossel de Pacientes**: Visualização moderna dos pacientes na página inicial.

## 🛠️ Tecnologias Utilizadas

-   **Frontend**: HTML5, CSS3 (Design Moderno & Responsivo), JavaScript.
-   **Backend**: PHP 8.
-   **Banco de Dados**: MySQL / MariaDB.
-   **Servidor Local**: XAMPP (Apache).

## 📦 Instalação e Configuração

Siga os passos abaixo para rodar o projeto localmente:

### 1. Pré-requisitos
-   Tenha o [XAMPP](https://www.apachefriends.org/pt_br/index.html) instalado.

### 2. Configuração dos Arquivos
1.  Baixe ou clone este repositório dentro da pasta `htdocs` do seu XAMPP (geralmente em `C:\xampp\htdocs\projeto_clinica_veterinaria`).

### 3. Banco de Dados
1.  Inicie o **Apache** e o **MySQL** no painel do XAMPP.
2.  Acesse o **PHPMyAdmin** (http://localhost/phpmyadmin).
3.  Crie um novo banco de dados chamado `prontuario_vet`.
4.  Importe o arquivo `database.sql` localizado na raiz do projeto.
5.  **Importante**: Para configurar as tabelas de planos e tratamentos, execute o script de migração acessando no navegador:
    `http://localhost/projeto_clinica_veterinaria/migrate_plans.php`
    *(Isso criará as tabelas de planos e populará os dados iniciais)*.

### 4. Acesso ao Sistema
Acesse o sistema pelo navegador:
`http://localhost/projeto_clinica_veterinaria`

### 👤 Usuários Padrão (para teste)
-   **Administrador**:
    -   Email: `admin@vetlife.com`
    -   Senha: `123456`
-   **Usuário Comum**:
    -   Você pode registrar um novo usuário na tela de cadastro.

## 📄 Estrutura do Projeto
-   `/classes`: Controladores e Modelos (MVC).
-   `/css`: Estilos do sistema (`modern.css`).
-   `/images`: Imagens dos animais e logo.
-   `/js`: Scripts interativos (Carrossel).
-   `atendimento.php`: Gestão de prontuários.
-   `plans.php`: Visualização dos planos disponíveis.
