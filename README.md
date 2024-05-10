<p align="center">
  <img width="128" src="./assets/barberbot.png">
</p>

<h1 align="center">Whatsapp Bot p/ Barbearia</h1>
<p align="center"><i
>Simplifique o atendimento sua barbearia com mensagens automáticas com um novo Bot de mensagens via WhatsApp</i
></p>

# 🔨 Funcionalidades

- [x] `Automação do Atendimento:` Receba mensagens dos clientes via WhatsApp Web, forneça respostas automáticas sobre **tempo de espera, quantidade de pessoas na fila, preços, promoções e entre na fila.** Através de um menu personalizado por mensagens do zap.
- [x] `Cadastrar e Gerenciar clientes:` Registre novos clientes e armazene em um banco de dados MySQL, **garantindo persistência dos dados** e consistência das interações
- [x] `Sistema de Fila:` Registre o cliente na fila e mostre o tempo médio de espera.
- [x] `SUPERUSER:` Acesso a funcionalidades avançadas de gerenciamento: Ver, adicione, remova e atualize registros dos clientes no sistema; Adicionar, deletar ou reorganizar a fila.

# 📐 Layout

![Authentication](<./assets/layout (2).gif>)

# 🛠️ Baixe e rode o projeto

```bash
# Clone este repositório
$ git clone https://github.com/vitumattos/bot-barbearia-zap.git

# Acesse a pasta do projeto no seu terminal
$ cd bot-barbearia-zap

# Crie uma venv e acesse-a
$ python -m venv .venv
$ .venv/scritps/activate

# Intalação das dependencias
$ pip install requirements.txt

#Execute o aplicativo
$ python ./main.py

# O Whatsapp web será aberto scanei o o códogo estará tudo pronto.
```

## ✔️ Técnicas e tecnologias utilizadas

- `Python`
- `Selenium`
- `PHP`
- `MySQL`

##### 💬 Comentario do Dev (eu)

> _No começo, pensei que seria só mais um desafio maneiro de Python e Selenium, mas conforme o projeto evoluía, percebi que precisava de um banco de dados para guardar algumas informações. Decidi usar PHP para separar a lógica da automação do zap, deixando o Python só na parte da frente._<br> >_Foi incrível pôr em prática meus conhecimentos de MySQL e aprender PHP enquanto desenvolvia o projeto. Desbravar tutoriais, mergulhar em documentações e já sair codando em uma nova linguagem dentro do projeto foi um desafio e tanto!_
