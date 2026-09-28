# 🏢 Pinciara Manager - CodeIgniter 4 + Tailwind CSS + MySQL

Este projeto é a recriação completa do sistema **Pinciara Manager** utilizando **CodeIgniter 4 (PHP 8.3+)**, estilização moderna com **Tailwind CSS**, gerenciador de dependências **Composer** e banco de dados **MySQL 8.0** em contêineres Docker.

---

## ⚡ Diferenciais do CodeIgniter 4 nesta Aplicação

* **Leveza e Desempenho Extremo:** Ao contrário de frameworks mais pesados, o CodeIgniter 4 possui inicialização quase instantânea e consumo mínimo de memória.
* **Arquitetura MVC Clara e Limpa:**
  * `app/Controllers/`: Regras de negócio, fluxo de autenticação e esteiras (`Auth`, `Imoveis`, `Placas`).
  * `app/Models/`: Modelos com validações e consultas (`UserModel`, `ImovelModel`, `PlacaModel`).
  * `app/Views/`: Telas construídas com **Tailwind CSS**, design responsivo e componentes leves com **Alpine.js**.
* **Autenticação Segura:** Proteção por filtro (`AuthFilter`) com sessões protegidas via HTTP-only cookies e senhas criptografadas com `bcrypt`.
* **Zero Overhead de Build:** Tailwind CSS e Alpine.js integrados de forma reativa, sem a lentidão ou complexidade de bundlers intermediários desnecessários.

---

## 🚀 Funcionalidades Implementadas

### 1. 🏠 Esteira de Imóveis (Pipeline de Captação e Mídia)
Acompanha todas as etapas do imóvel desde a captação até a publicação final:
* **Abas Interativas com Contadores:**
  * `Cadastrar`: Entrada inicial do imóvel, título, endereço, fotos preliminares e descrição.
  * `Tour 360º`: Imóveis aguardando ou em fase de produção de imagens panorâmicas.
  * `Vídeo`: Imóveis aguardando gravação ou edição de vídeo promocional (com suporte a link de vídeo direto).
  * `Concluído`: Imóvel totalmente finalizado para divulgação.
* **Avanço Rápido de Etapa:** Seletor direto no card do imóvel para movê-lo de fase com 1 clique.
* **Upload e Galeria:** Suporte a upload múltiplo de fotos (`public/uploads/imoveis/`) e URLs externas de imagem.
* **Edição e Exclusão:** Modais dinâmicos para alterar dados ou remover imóveis.

### 2. 🏷️ Controle de Placas e Impressos
Gerenciamento operacional e financeiro de comunicação visual:
* **Indicadores Financeiros em Tempo Real (KPIs):**
  * Total investido acumulado (R$).
  * Volume total de placas confeccionadas.
  * Quantidade em produção.
  * Estoque de placas prontas na imobiliária.
* **Fluxo de Produção:**
  * `Produzir` ➔ Enviar para Pagamento.
  * `Pagar` ➔ Confirmar Pagamento com Fornecedor.
  * `Pago` ➔ Mover para Disponíveis no Estoque.
  * `Disponíveis` ➔ Marcar como Usada / Instalada em Fachada.
  * `Usadas` ➔ Histórico de placas já alocadas.
* **Especificações Técnicas:** Medidas (largura x altura em metros), tipos de materiais (*Polionda, Lona com Reforço, PVC 2mm, ACM*), tipo de anúncio (*Vende-se, Aluga-se, Exclusividade*) e data de envio.

### 3. 🔐 Autenticação e Segurança
* Tela de login moderna com visual escuro em Tailwind.
* **Botão "Entrar como Visitante (1 clique)":** preenche e autentica automaticamente com as credenciais padrão para testes instantâneos.
* Filtro de proteção de rotas (`AuthFilter`) que impede acesso não autorizado a qualquer tela do sistema.

---

## 🛠️ Como Executar com Docker

### 1. Subir os Contêineres
Na pasta `php-CodeIgniter4`, execute:
```bash
docker compose up -d --build
```
Os seguintes contêineres serão inicializados:
* `pinciara_ci4_app`: Aplicação PHP 8.3 + CodeIgniter 4 na porta **8085** (mapeada para 8080 interna)
* `pinciara_ci4_db`: Banco de dados MySQL 8.0 na porta interna **3306** (porta externa **3308**)

### 2. Executar as Migrations e os Dados Iniciais (Seeders)
Execute dentro do contêiner da aplicação:
```bash
docker exec -it pinciara_ci4_app php spark migrate
docker exec -it pinciara_ci4_app php spark db:seed PinciaraSeeder
```

### 3. Acessar o Sistema
* **URL:** [http://localhost:8085](http://localhost:8085)
* **Usuário Visitante:** `visitante@pi.com` | **Senha:** `visitante` (ou use o botão de 1 clique na tela de login)
* **Usuário Admin:** `bernardo.pinciara@gmail.com` | **Senha:** `admin123`

---

## 🗄️ Conexão com o Banco de Dados (MySQL)

Se desejar conectar um cliente SQL externo (DBeaver, TablePlus, VS Code, etc.):
* **Host:** `localhost`
* **Porta:** `3308`
* **Database:** `pinciara_ci4`
* **Usuário:** `pinciara`
* **Senha:** `secretpassword`
