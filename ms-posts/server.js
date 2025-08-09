// server.js
const express = require('express');
const app = express();
const PORT = 3000;

// Simulação de dados de posts
const posts = [
    { id: 1, title: 'Primeiro Post', content: 'Conteúdo do primeiro post' },
    { id: 2, title: 'Segundo Post', content: 'Conteúdo do segundo post' },
    { id: 3, title: 'Terceiro Post', content: 'Conteúdo do terceiro post' }
];

// Endpoint GET /posts
app.get('/posts', (req, res) => {
    res.json(posts);
});

// Inicia o servidor
app.listen(PORT, () => {
    console.log(`Servidor rodando em http://localhost:${PORT}`);
});
