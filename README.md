# API de Personagens RPG

## Informações

**Curso:** Informática

**Unidade Curricular:** Desenvolver Serviços Web

**Aluno:** Evandro Azevedo

---

## Sobre o projeto

Este projeto é uma Web Service API desenvolvida em PHP utilizando o Slim Framework.

A API permite cadastrar, consultar, atualizar e excluir personagens de RPG.

Os dados dos personagens são armazenados em um arquivo JSON.

---

## Endpoints

### GET /personagens

Retorna todos os personagens cadastrados.

**Exemplo:**

```text
GET http://localhost:8080/personagens