# Guia de Testes - Sistema de Votação

## Como Testar a Aplicação

Este documento fornece um guia completo para testar todas as funcionalidades da aplicação.

---

## 📋 Pré-requisitos

Antes de começar os testes, certifique-se de que:

- [x] Aplicação está instalada e configurada
- [x] Base de dados MySQL está criada e migrada
- [x] Seeder do admin foi executado
- [x] Servidor está a correr (`php artisan serve`)
- [x] Diretório `public/uploads/` existe

---

## 🧪 Testes Manuais

### 1. Testar Página Principal (Galeria)

**URL:** `http://localhost:8000/`

**Checklist:**
- [ ] Página carrega sem erros
- [ ] Navbar aparece corretamente
- [ ] Mensagem "Nenhuma imagem disponível" aparece (se não há imagens)
- [ ] Link "Admin Login" funciona
- [ ] Layout é responsivo (testar em mobile)

**Resultado Esperado:**
✅ Página vazia com mensagem convidativa para ver imagens

---

### 2. Testar Login de Admin

**URL:** `http://localhost:8000/admin/login`

#### Teste 2.1: Login com Credenciais Corretas

**Passos:**
1. Acesse `/admin/login`
2. Insira:
   - Email: `admin@example.com`
   - Password: `password`
3. Clique em "Entrar"

**Resultado Esperado:**
✅ Redireciona para `/admin/dashboard`
✅ Mensagem de boas-vindas aparece
✅ Navbar mostra opções de admin

**Checklist:**
- [ ] Login bem-sucedido
- [ ] Redireciona para dashboard
- [ ] Navbar mudou (mostra Upload, Estatísticas, Sair)
- [ ] Nome do admin aparece

#### Teste 2.2: Login com Credenciais Incorretas

**Passos:**
1. Acesse `/admin/login`
2. Insira:
   - Email: `wrong@example.com`
   - Password: `wrongpassword`
3. Clique em "Entrar"

**Resultado Esperado:**
❌ Erro: "As credenciais fornecidas não correspondem aos nossos registos."
✅ Permanece na página de login

**Checklist:**
- [ ] Mensagem de erro aparece
- [ ] Não redireciona
- [ ] Email é preservado no campo

#### Teste 2.3: Login com Campos Vazios

**Passos:**
1. Acesse `/admin/login`
2. Deixe campos vazios
3. Clique em "Entrar"

**Resultado Esperado:**
❌ Validação HTML impede submit

**Checklist:**
- [ ] Campos marcados como inválidos
- [ ] Mensagens de validação aparecem

---

### 3. Testar Upload de Imagens

**URL:** `http://localhost:8000/admin/dashboard`
**Pré-requisito:** Estar logado como admin

#### Teste 3.1: Upload Bem-Sucedido

**Passos:**
1. Faça login como admin
2. Acesse `/admin/dashboard`
3. Clique em "Selecionar Imagem"
4. Escolha uma imagem válida (JPEG, PNG, GIF)
5. Veja o preview
6. Clique em "Fazer Upload"

**Resultado Esperado:**
✅ Mensagem: "Imagem carregada com sucesso!"
✅ Imagem aparece na pasta `public/uploads/`
✅ Registo criado na base de dados

**Checklist:**
- [ ] Preview funciona
- [ ] Upload completa
- [ ] Mensagem de sucesso aparece
- [ ] Ficheiro existe em `public/uploads/`
- [ ] Registo na BD (verificar com phpMyAdmin ou Tinker)

#### Teste 3.2: Upload com Formato Inválido

**Passos:**
1. Tente fazer upload de um ficheiro .txt ou .pdf
2. Clique em "Fazer Upload"

**Resultado Esperado:**
❌ Erro de validação
❌ Upload não é processado

**Checklist:**
- [ ] Validação impede upload
- [ ] Mensagem de erro aparece

#### Teste 3.3: Upload Sem Ficheiro

**Passos:**
1. Não selecione ficheiro
2. Tente submeter formulário

**Resultado Esperado:**
❌ Validação HTML impede submit

**Checklist:**
- [ ] Campo marcado como inválido
- [ ] Não permite submit

---

### 4. Testar Sistema de Votação

**URL:** `http://localhost:8000/`
**Pré-requisito:** Ter pelo menos 1 imagem carregada

#### Teste 4.1: Votação Bem-Sucedida

**Passos:**
1. Acesse a galeria principal
2. Clique numa imagem
3. Modal abre
4. Insira email: `teste@example.com`
5. Clique em "Confirmar Voto"

**Resultado Esperado:**
✅ Mensagem: "Voto registado com sucesso! Obrigado por participar."
✅ Contador de votos da imagem incrementa
✅ Registo criado na tabela `votes`

**Checklist:**
- [ ] Modal abre corretamente
- [ ] Email é aceite
- [ ] Voto é registado
- [ ] Contador atualiza
- [ ] Mensagem de sucesso aparece

#### Teste 4.2: Tentar Votar Duas Vezes (Mesmo Email)

**Passos:**
1. Vote com `teste@example.com` (como no teste 4.1)
2. Tente votar novamente com o mesmo email

**Resultado Esperado:**
❌ Erro: "Este email já votou. Cada email só pode votar uma vez!"
❌ Voto não é registado

**Checklist:**
- [ ] Mensagem de erro aparece
- [ ] Contador não incrementa
- [ ] BD não cria novo registo

#### Teste 4.3: Votar com Email Inválido

**Passos:**
1. Clique numa imagem
2. Insira email inválido: `teste123` (sem @)
3. Tente votar

**Resultado Esperado:**
❌ Validação HTML impede submit

**Checklist:**
- [ ] Campo marcado como inválido
- [ ] Não permite submit

#### Teste 4.4: Votar Sem Email

**Passos:**
1. Clique numa imagem
2. Deixe campo email vazio
3. Tente votar

**Resultado Esperado:**
❌ Validação HTML impede submit

**Checklist:**
- [ ] Campo marcado como inválido
- [ ] Não permite submit

---

### 5. Testar Estatísticas

**URL:** `http://localhost:8000/admin/statistics`
**Pré-requisito:** Estar logado como admin, ter imagens e votos

#### Teste 5.1: Ver Estatísticas Gerais

**Passos:**
1. Faça login como admin
2. Acesse `/admin/statistics`

**Resultado Esperado:**
✅ Resumo geral aparece:
- Total de Imagens
- Total de Votos
- Média de Votos

**Checklist:**
- [ ] Cards de resumo aparecem
- [ ] Números estão corretos
- [ ] Ícones aparecem

#### Teste 5.2: Ver Tabela Detalhada

**Passos:**
1. Na página de estatísticas
2. Role até a tabela

**Resultado Esperado:**
✅ Tabela mostra:
- Preview de cada imagem
- Nome do ficheiro
- Admin que fez upload
- Data de upload
- Total de votos
- Percentagem de votos

**Checklist:**
- [ ] Tabela carrega
- [ ] Imagens aparecem (thumbnails)
- [ ] Dados estão corretos
- [ ] Barras de progresso funcionam
- [ ] Percentagens somam 100%

#### Teste 5.3: Ver Imagem Mais Votada

**Passos:**
1. Role até o final da página de estatísticas

**Resultado Esperado:**
✅ Card destaque com:
- Imagem mais votada
- Total de votos
- Percentagem
- Informações do upload

**Checklist:**
- [ ] Card aparece
- [ ] Imagem correta (com mais votos)
- [ ] Dados estão corretos

---

### 6. Testar Logout

**URL:** Qualquer página admin
**Pré-requisito:** Estar logado

**Passos:**
1. Estando logado, clique em "Sair" no navbar
2. Confirme

**Resultado Esperado:**
✅ Redireciona para `/admin/login`
✅ Sessão é destruída
✅ Navbar volta ao estado público

**Checklist:**
- [ ] Logout bem-sucedido
- [ ] Redireciona para login
- [ ] Não consegue acessar rotas admin depois

---

### 7. Testar Proteção de Rotas

#### Teste 7.1: Acesso Sem Login

**Passos:**
1. Faça logout (se estiver logado)
2. Tente acessar diretamente:
   - `http://localhost:8000/admin/dashboard`
   - `http://localhost:8000/admin/statistics`

**Resultado Esperado:**
❌ Redireciona para `/admin/login`

**Checklist:**
- [ ] Não permite acesso
- [ ] Redireciona para login
- [ ] Depois do login, vai para página desejada

---

### 8. Testar Responsividade

**Dispositivos para Testar:**
- Desktop (1920x1080)
- Tablet (768x1024)
- Mobile (375x667)

**Páginas para Testar:**
- [ ] Galeria principal
- [ ] Login admin
- [ ] Dashboard admin
- [ ] Estatísticas

**Checklist Geral:**
- [ ] Layout adapta-se ao tamanho
- [ ] Navbar colapsa em mobile
- [ ] Cards reorganizam-se
- [ ] Imagens redimensionam
- [ ] Tabelas são scrolláveis
- [ ] Botões são clicáveis
- [ ] Formulários são usáveis

---

## 🗄️ Testes de Base de Dados

### Verificar Integridade

```bash
# Entrar no MySQL
mysql -u root -p

USE laravel_voting;

# Ver todas as tabelas
SHOW TABLES;

# Ver imagens
SELECT * FROM images;

# Ver votos
SELECT * FROM votes;

# Ver contagem de votos por imagem
SELECT i.id, i.filename, COUNT(v.id) as votos
FROM images i
LEFT JOIN votes v ON i.id = v.image_id
GROUP BY i.id;

# Verificar email único (não deve haver duplicados)
SELECT email, COUNT(*) as count
FROM votes
GROUP BY email
HAVING count > 1;
```

**Resultado Esperado:**
✅ Nenhum email duplicado
✅ Foreign keys funcionando
✅ Dados consistentes

---

## 🧹 Testes de Limpeza

### Resetar Dados

```bash
# Limpar votos
php artisan tinker
>>> App\Models\Vote::truncate();

# Limpar imagens (e ficheiros)
>>> App\Models\Image::all()->each(function($img) { 
      unlink(public_path($img->path)); 
      $img->delete(); 
    });

# Recriar admin
php artisan migrate:fresh --seed
```

---

## 📊 Checklist de Testes Completa

### Funcionalidades Essenciais

- [ ] ✅ Instalação completa sem erros
- [ ] ✅ Base de dados criada e migrada
- [ ] ✅ Servidor inicia sem problemas
- [ ] ✅ Página principal carrega
- [ ] ✅ Login admin funciona
- [ ] ✅ Upload de imagens funciona
- [ ] ✅ Validação de ficheiros funciona
- [ ] ✅ Sistema de votação funciona
- [ ] ✅ Email único é respeitado
- [ ] ✅ Estatísticas aparecem corretamente
- [ ] ✅ Logout funciona
- [ ] ✅ Proteção de rotas funciona

### Validações

- [ ] ✅ Campos obrigatórios validados
- [ ] ✅ Formato de email validado
- [ ] ✅ Tipos de ficheiro validados
- [ ] ✅ Tamanho de ficheiro validado
- [ ] ✅ CSRF protection ativo
- [ ] ✅ Email único garantido

### Interface

- [ ] ✅ Design responsivo
- [ ] ✅ Animações funcionam
- [ ] ✅ Modais abrem/fecham
- [ ] ✅ Flash messages aparecem
- [ ] ✅ Ícones aparecem
- [ ] ✅ Imagens carregam

---

## 🐛 Problemas Comuns e Soluções

### Problema: Imagens não aparecem na galeria
**Solução:** Verificar se o caminho está correto (`public/uploads/`)

### Problema: Erro ao votar
**Solução:** Verificar foreign keys e constraint de email único

### Problema: Upload falha
**Solução:** Verificar permissões da pasta `public/uploads/`

### Problema: Login não funciona
**Solução:** Verificar se seeder foi executado

---

## ✅ Resultado Final

Após todos os testes, você deve ter:

✅ **Admin funcional**
- Login/logout
- Upload de imagens
- Visualização de estatísticas

✅ **Visitantes podem**
- Ver galeria
- Votar em imagens
- Ver contagem de votos

✅ **Sistema garante**
- Segurança (auth, CSRF)
- Integridade (foreign keys, email único)
- Validação (formulários, ficheiros)

---

**Testes Completos! 🎉**

Se todos os testes passarem, sua aplicação está 100% funcional!
