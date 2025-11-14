# Gestor Max - Sistema de Vendas Simplificado

**⚠️ Nota Importante: Este projeto foi desenvolvido para fins estritamente educacionais para o IFTO - Campus Paraíso do Tocantins.**

O objetivo é servir como um exemplo prático e didático de um sistema web básico utilizando a stack PHP/MySQL, demonstrando conceitos fundamentais de desenvolvimento web, como CRUD, autenticação de sessão, transações de banco de dados e arquitetura de módulos.

## 🚀 Sobre o Projeto

O Gestor Max é um mini-ERP (sistema de gestão) focado em controle de vendas. Ele permite que usuários logados gerenciem clientes, produtos e registrem vendas, além de fornecer um relatório básico de transações.

### 🛠️ Tecnologias Utilizadas

- Back-end: PHP 8.x
- Banco de Dados: MySQL
- Front-end: HTML5, CSS3, JavaScript
- Framework CSS: Tailwind CSS (utilizado via CDN para simplicidade)
- Ambiente de Servidor: XAMPP / WAMP / MAMP (PHP + Apache + MySQL)

### ⚙️ Instalação e Execução

**1. Pré-requisitos**
Tenha um ambiente de servidor local instalado e em execução. O mais comum é o XAMPP:

Baixe e instale o XAMPP (que inclui Apache, PHP e MySQL).

**2. Arquivos do Projeto**
Clone este repositório ou copie a pasta GestorMax (contendo todos os arquivos .php) para dentro do diretório htdocs do seu XAMPP (normalmente C:\xampp\htdocs\).

**3. Configuração do Banco de Dados**
Inicie os módulos Apache e MySQL no painel de controle do XAMPP.

Acesse o phpMyAdmin pelo seu navegador: ``http://localhost/phpmyadmin``

Execute o **sql** que está na pasta ``docs``.

**4. Acesso ao Sistema**
Abra seu navegador e acesse: ``http://localhost/GestorMax/``

Faça o login com as credenciais padrão:

```
Usuário: admin@gestormax.com
Senha: admin
```